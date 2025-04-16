<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

function generateLogin($length = 6) {
    return 'stu' . bin2hex(random_bytes($length / 2));
}

function generatePassword($length = 8) {
    return bin2hex(random_bytes($length / 2));
}

$generated = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_id = (int) ($_POST['test_id'] ?? 0);
    $count = (int) ($_POST['count'] ?? 0);

    if ($test_id && $count > 0) {

        $stmt = $pdo->prepare("
            INSERT INTO tests.student_logins (login, password, test_id)
            VALUES (:login, :password, :test_id)
        ");

        for ($i = 0; $i < $count; $i++) {
            $login = generateLogin();
            $password = generatePassword();
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->execute([
                'login' => $login,
                'password' => $hashedPassword,
                'test_id' => $test_id
            ]);
            $generated[] = ['login' => $login, 'password' => $password];
        }
    }
}

$stmt = $pdo->query("SELECT id, test_name FROM tests.tests ORDER BY id");
$tests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/style.css">
    <title>Генерация логинов студентов</title>
</head>
<body>
    <h2>Генерация логинов и паролей для студентов</h2>

    <form method="POST">
        <label>Выберите тест:<br>
            <select name="test_id" required>
                <option value="">-- выберите --</option>
                <?php foreach ($tests as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['test_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Количество логинов:<br>
            <input type="number" name="count" min="1" required>
        </label><br><br>

        <button type="submit">Сгенерировать</button>
    </form>

    <?php if (!empty($generated)): ?>
        <h3>Сгенерированные логины:</h3>
        <table border="1" cellpadding="5">
            <tr><th>Логин</th><th>Пароль</th></tr>
            <?php foreach ($generated as $pair): ?>
                <tr>
                    <td><?= htmlspecialchars($pair['login']) ?></td>
                    <td><?= htmlspecialchars($pair['password']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard.php">← Назад в панель преподавателя</a></p>
</body>
</html>
