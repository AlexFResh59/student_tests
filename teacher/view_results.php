<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$questionCounts = [];
$stmt = $pdo->query("SELECT test_id, COUNT(*) AS total FROM tests.test_questions GROUP BY test_id");
foreach ($stmt->fetchAll() as $row) {
    $questionCounts[$row['test_id']] = $row['total'];
}

$stmt = $pdo->query("SELECT r.id AS result_id, r.login_id, s.login AS student_login, r.score, r.test_id, t.test_name, r.created_at FROM tests.results r JOIN tests.tests t ON r.test_id = t.id JOIN tests.student_logins s ON r.login_id = s.id ORDER BY r.test_id, r.score DESC");
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Результаты тестирования</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h2>Результаты тестирования студентов</h2>

    <?php if (empty($results)): ?>
        <p>Нет результатов для отображения.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Студент</th>
                <th>Тест</th>
                <th>Правильных</th>
                <th>Процент</th>
                <th>Дата</th>
                <th>Действие</th>
            </tr>
            <?php foreach ($results as $r): 
                $total = $questionCounts[$r['test_id']] ?? 1;
                $percent = round(($r['score'] / $total) * 100);
            ?>
                <tr>
                    <td><?= htmlspecialchars($r['student_login']) ?></td>
                    <td><?= htmlspecialchars($r['test_name']) ?></td>
                    <td><?= $r['score'] ?> / <?= $total ?></td>
                    <td><?= $percent ?>%</td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                    <td><a href="view_result_details.php?result_id=<?= $r['result_id'] ?>">Подробнее</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard.php">← Назад в панель преподавателя</a></p>
</body>
</html>
