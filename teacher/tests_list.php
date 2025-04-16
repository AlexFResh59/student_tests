<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, test_name FROM tests.tests WHERE created_by = :uid ORDER BY id");
$stmt->execute(['uid' => $teacher_id]);
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Мои тесты</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h2>Список тестов</h2>
    <p><a href="dashboard.php">← Назад</a></p>

    <?php foreach ($tests as $t): ?>
        <fieldset style="margin-bottom: 20px;">
            <legend><strong><?= htmlspecialchars($t['test_name']) ?></strong></legend>

            <form method="POST" action="delete_test.php" onsubmit="return confirm('Удалить этот тест?')">
                <input type="hidden" name="test_id" value="<?= $t['id'] ?>">
                <button type="submit">🗑 Удалить тест</button>
            </form>
        </fieldset>
    <?php endforeach; ?>
</body>
</html>
