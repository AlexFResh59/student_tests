<?php
require_once dirname(__DIR__) . '/includes/session.php';
if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Панель преподавателя</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<h2>Панель преподавателя</h2>

    <ul>
        <li><a href="questions.php">➕ Создать вопрос</a></li>
        <li><a href="questions_list.php">📋 Список вопросов</a></li>
        <li><a href="upload_questions.php">⬆ Загрузить вопросы из CSV</a></li>
        <li><a href="export_and_clear_questions.php"
         onclick="return confirm('Вы действительно хотите выгрузить тест в CSV?');">⬇ Выгрузить и очистить вопросы (CSV)</a></li>
        <li><a href="create_test.php">🧪 Создать тест</a></li>
        <li><a href="tests_list.php">📋 Список тестов</a></li>
        <li><a href="generate_logins.php">🎫 Сгенерировать логины</a></li>
        <li><a href="view_results.php">📊 Результаты</a></li>
        
        <li><a href="change_password.php">🔒 Сменить пароль</a></li>
        <li><a href="../auth/logout.php">🚪 Выйти</a></li>
    </ul>
</body>
</html>

