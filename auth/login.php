<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, password FROM tests.users WHERE login = :login");
    $stmt->execute(['login' => $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'teacher';
        header('Location: ../teacher/dashboard.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, test_id, password FROM tests.student_logins WHERE login = :login");
    $stmt->execute(['login' => $login]);
    $student = $stmt->fetch();

    if ($student && crypt($password, $student['password']) === $student['password']) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['test_id'] = $student['test_id'];
        header('Location: ../student/take_test.php');
        exit;
    }

    $error = 'Неверный логин или пароль';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="container">
        <h2>Вход в систему</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Логин:<br>
                <input type="text" name="login" required>
            </label><br><br>

            <label>Пароль:<br>
                <input type="password" name="password" required>
            </label><br><br>

            <button type="submit">Войти</button>
        </form>

        <p>Пример: Логин: <strong>teacher1</strong>, Пароль: <strong>teacher123</strong></p>
    </div>
</body>
</html>