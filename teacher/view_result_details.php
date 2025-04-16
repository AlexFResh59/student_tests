<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

if (!isset($_GET['result_id'])) {
    exit('Не указан результат.');
}

$result_id = (int) $_GET['result_id'];

// Общая информация о прохождении
$stmt = $pdo->prepare("SELECT r.*, s.login AS student_login, t.test_name FROM tests.results r JOIN tests.student_logins s ON r.login_id = s.id JOIN tests.tests t ON r.test_id = t.id WHERE r.id = :rid");
$stmt->execute(['rid' => $result_id]);
$result = $stmt->fetch();

if (!$result) {
    exit('Результат не найден.');
}

// Вопросы теста
$stmt = $pdo->prepare("SELECT q.* FROM tests.test_questions tq JOIN tests.questions q ON tq.question_id = q.id WHERE tq.test_id = :tid");
$stmt->execute(['tid' => $result['test_id']]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ответы студента (для вариантов)
$stmt = $pdo->prepare("SELECT * FROM tests.answers WHERE result_id = :rid");
$stmt->execute(['rid' => $result_id]);
$student_answers_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$student_answers = [];
foreach ($student_answers_raw as $a) {
    $student_answers[$a['question_id']][] = $a['option_id'];
}

// Ответы студента (для текстовых)
$stmt = $pdo->prepare("SELECT question_id, student_text FROM tests.text_user_answers WHERE result_id = :rid");
$stmt->execute(['rid' => $result_id]);
$text_student_answers = [];
foreach ($stmt->fetchAll() as $row) {
    $text_student_answers[$row['question_id']] = $row['student_text'];
}

function getCorrectOptions($pdo, $question_id) {
    $stmt = $pdo->prepare("SELECT id FROM tests.options WHERE question_id = :qid AND is_correct = true");
    $stmt->execute(['qid' => $question_id]);
    return array_column($stmt->fetchAll(), 'id');
}

function getOptionsText($pdo, $question_id) {
    $stmt = $pdo->prepare("SELECT id, option_text FROM tests.options WHERE question_id = :qid");
    $stmt->execute(['qid' => $question_id]);
    $options = [];
    foreach ($stmt->fetchAll() as $opt) {
        $options[$opt['id']] = $opt['option_text'];
    }
    return $options;
}

function getTextAnswer($pdo, $question_id) {
    $stmt = $pdo->prepare("SELECT correct_text FROM tests.text_answers WHERE question_id = :qid");
    $stmt->execute(['qid' => $question_id]);
    return $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ответы студента</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<h2>Ответ студента: <?= htmlspecialchars($result['student_login']) ?></h2>
<p><strong>Тест:</strong> <?= htmlspecialchars($result['test_name']) ?><br>
<strong>Дата:</strong> <?= htmlspecialchars($result['created_at']) ?><br>
<strong>Результат:</strong> <?= $result['score'] ?> баллов</p>

<?php foreach ($questions as $q): ?>
    <div style="border:1px solid #ccc; margin:10px 0; padding:10px;">
        <p><strong><?= htmlspecialchars($q['question_text']) ?></strong> (<?= $q['question_type'] ?>)</p>

        <?php if ($q['question_type'] === 'text'): ?>
            <?php
                $correct = getTextAnswer($pdo, $q['id']);
                $student = $text_student_answers[$q['id']] ?? '';
                $isCorrect = strtolower(trim($correct)) === strtolower(trim($student));
            ?>
            <p><strong>Правильный ответ:</strong> <?= htmlspecialchars($correct) ?></p>
            <p><strong>Ответ студента:</strong> <?= htmlspecialchars($student) ?></p>
            <p><strong>Результат:</strong> <?= $isCorrect ? '✅ Верно' : '❌ Неверно' ?></p>

        <?php else: ?>
            <?php
                $correct = getCorrectOptions($pdo, $q['id']);
                $student = $student_answers[$q['id']] ?? [];
                $options = getOptionsText($pdo, $q['id']);
                $isCorrect = $correct === $student || (count($correct) === count($student) && !array_diff($correct, $student));
            ?>
            <p><strong>Варианты:</strong></p>
            <ul>
                <?php foreach ($options as $oid => $text): ?>
                    <li>
                        <?= htmlspecialchars($text) ?>
                        <?= in_array($oid, $correct) ? '✅' : '' ?>
                        <?= in_array($oid, $student) ? ' (ответ студента)' : '' ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Результат:</strong> <?= $isCorrect ? '✅ Верно' : '❌ Неверно' ?></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<p><a href="view_results.php">← Назад к результатам</a></p>
</body>
</html>
