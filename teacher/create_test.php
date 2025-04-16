<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';
$error = '';
$teacher_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_name = $_POST['test_name'] ?? '';
    $question_ids = $_POST['questions'] ?? [];

    if ($test_name && count($question_ids)) {
        $stmt = $pdo->prepare("INSERT INTO tests.tests (test_name, created_by) VALUES (:name, :created_by) RETURNING id");
        $stmt->execute([
            'name' => $test_name,
            'created_by' => $teacher_id
        ]);
        $test_id = $stmt->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO tests.test_questions (test_id, question_id) VALUES (:tid, :qid)");
        foreach ($question_ids as $qid) {
            $stmt->execute(['tid' => $test_id, 'qid' => $qid]);
        }

        $message = "Тест «$test_name» успешно создан.";
    } else {
        $error = "Введите название теста и выберите хотя бы один вопрос.";
    }
}

$stmt = $pdo->query("SELECT id, question_text FROM tests.questions ORDER BY id");
$questions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/style.css">
    <title>Создание теста</title>
</head>
<body>
    <h2>Создание нового теста</h2>

    <?php if (!empty($message)): ?>
        <p style="color:green"><?= htmlspecialchars($message) ?></p>
    <?php elseif (!empty($error)): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Название теста:<br>
            <input type="text" name="test_name" required>
        </label><br><br>

        <label>Выберите вопросы:</label><br>
        <?php foreach ($questions as $q): ?>
            <input type="checkbox" name="questions[]" value="<?= $q['id'] ?>">
            <?= htmlspecialchars($q['question_text']) ?><br>
        <?php endforeach; ?>
        <br>
        <button type="submit">Создать тест</button>
    </form>

    <p><a href="dashboard.php">← Назад в панель преподавателя</a></p>
</body>
</html>
