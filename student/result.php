<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isStudentLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$test_id = $_SESSION['test_id'];

$stmt = $pdo->prepare("
    SELECT r.score, t.test_name, r.created_at
    FROM tests.results r
    JOIN tests.tests t ON r.test_id = t.id
    WHERE r.login_id = :sid AND r.test_id = :tid
    LIMIT 1
");
$stmt->execute([
    'sid' => $student_id,
    'tid' => $test_id
]);
$result = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM tests.test_questions WHERE test_id = :tid
");
$stmt->execute(['tid' => $test_id]);
$total_questions = (int)$stmt->fetchColumn();

$percent = ($result && $total_questions > 0)
    ? round(($result['score'] / $total_questions) * 100)
    : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Результат теста</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h2>Результат теста</h2>

    <?php if ($result): ?>
        <p><strong>Тест:</strong> <?= htmlspecialchars($result['test_name']) ?></p>
        <p><strong>Правильных ответов:</strong> <?= $result['score'] ?> из <?= $total_questions ?></p>
        <p><strong>Процент:</strong> <?= $percent ?>%</p>
        <p><strong>Дата:</strong> <?= htmlspecialchars($result['created_at']) ?></p>
    <?php else: ?>
        <p>Результат не найден.</p>
    <?php endif; ?>
</body>
</html>

