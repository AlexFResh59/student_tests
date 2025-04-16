<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT q.id, q.question_text, q.question_type
    FROM tests.questions q
    WHERE q.created_by = :uid
    ORDER BY q.id
");
$stmt->execute(['uid' => $teacher_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Список вопросов</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h2>Список ваших вопросов</h2>
    <p><a href="dashboard.php">← Назад</a></p>

    <?php foreach ($questions as $q): ?>
        <fieldset style="margin-bottom: 20px;">
            <legend><strong><?= htmlspecialchars($q['question_text']) ?></strong> (<?= $q['question_type'] ?>)</legend>

            <?php if ($q['question_type'] === 'text'): ?>
                <?php
                $stmt = $pdo->prepare("SELECT correct_text FROM tests.text_answers WHERE question_id = :qid");
                $stmt->execute(['qid' => $q['id']]);
                $row = $stmt->fetch();
                ?>
                <p><strong>Правильный ответ:</strong> <?= htmlspecialchars($row['correct_text'] ?? '—') ?></p>
            <?php else: ?>
                <ul>
                <?php
                $stmt = $pdo->prepare("SELECT option_text, is_correct FROM tests.options WHERE question_id = :qid");
                $stmt->execute(['qid' => $q['id']]);
                foreach ($stmt->fetchAll() as $opt):
                ?>
                    <li>
                        <?= htmlspecialchars($opt['option_text']) ?>
                        <?= $opt['is_correct'] ? '✅' : '' ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" action="delete_question.php" onsubmit="return confirm('Удалить этот вопрос?')">
                <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                <button type="submit">🗑 Удалить</button>
            </form>
        </fieldset>
    <?php endforeach; ?>
</body>
</html>
