<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isStudentLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$test_id = $_SESSION['test_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM tests.results WHERE login_id = :sid AND test_id = :tid");
$stmt->execute(['sid' => $student_id, 'tid' => $test_id]);
if ($stmt->fetchColumn() > 0) {
    header('Location: result.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT q.id AS qid, q.question_text, q.question_type, o.id AS oid, o.option_text
    FROM tests.test_questions tq
    JOIN tests.questions q ON tq.question_id = q.id
    LEFT JOIN tests.options o ON o.question_id = q.id
    WHERE tq.test_id = :tid
    ORDER BY q.id, o.id
");
$stmt->execute(['tid' => $test_id]);

$questions = [];
foreach ($stmt->fetchAll() as $row) {
    $qid = $row['qid'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'text' => $row['question_text'],
            'type' => $row['question_type'],
            'options' => []
        ];
    }
    if ($row['oid']) {
        $questions[$qid]['options'][$row['oid']] = $row['option_text'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Тестирование</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h2>Тест</h2>
    <form method="POST" action="submit_test.php">
        <?php foreach ($questions as $qid => $q): ?>
            <fieldset style="margin-bottom: 20px;">
                <legend><strong><?= htmlspecialchars($q['text']) ?></strong></legend>

                <?php if ($q['type'] === 'text'): ?>
                    <input type="text" name="text_answers[<?= $qid ?>]" style="width:60%" required><br>
                <?php elseif ($q['type'] === 'single'): ?>
                    <?php foreach ($q['options'] as $oid => $otext): ?>
                        <label>
                            <input type="radio" name="answers[<?= $qid ?>]" value="<?= $oid ?>" required>
                            <?= htmlspecialchars($otext) ?>
                        </label><br>
                    <?php endforeach; ?>
                <?php elseif ($q['type'] === 'multiple'): ?>
                    <?php foreach ($q['options'] as $oid => $otext): ?>
                        <label>
                            <input type="checkbox" name="answers[<?= $qid ?>][]" value="<?= $oid ?>">
                            <?= htmlspecialchars($otext) ?>
                        </label><br>
                    <?php endforeach; ?>
                <?php endif; ?>
            </fieldset>
        <?php endforeach; ?>

        <button type="submit">Отправить тест</button>
    </form>
</body>
</html>
