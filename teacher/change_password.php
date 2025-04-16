<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $stmt = $pdo->prepare("SELECT password FROM tests.users WHERE id = :id");
    $stmt->execute(['id' => $teacher_id]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'Пользователь не найден.';
    } elseif (!password_verify($current, $user['password'])) {
        $error = 'Текущий пароль неверен.';
    } elseif (strlen($new) < 6) {
        $error = 'Новый пароль должен быть не менее 6 символов.';
    } elseif ($new !== $confirm) {
        $error = 'Новый пароль и подтверждение не совпадают.';
    } else {
        $newHash = password_hash($new, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE tests.users SET password = :pass WHERE id = :id");
        $stmt->execute([
            'pass' => $newHash,
            'id' => $teacher_id
        ]);
        $message = 'Пароль успешно изменён.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/style.css">
    <title>Смена пароля</title>
</head>
<body>
    <h2>Смена пароля</h2>

    <?php if ($message): ?>
        <p style="color:green"><?= htmlspecialchars($message) ?></p>
    <?php elseif ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Текущий пароль:<br>
            <input type="password" name="current_password" required>
        </label><br><br>

        <label>Новый пароль:<br>
            <input type="password" name="new_password" required>
        </label><br><br>

        <label>Подтвердите новый пароль:<br>
            <input type="password" name="confirm_password" required>
        </label><br><br>

        <button type="submit">Сменить пароль</button>
    </form>

    <p><a href="dashboard.php">← Назад в панель преподавателя</a></p>
</body>
</html>
