<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, question_text, question_type FROM tests.questions WHERE created_by = :uid AND exported = FALSE ORDER BY id");
$stmt->execute(['uid' => $teacher_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Список вопросов</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        .filters { margin-bottom: 15px; display: flex; gap: 10px; flex-wrap: wrap; }
        .question-card { border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; border-radius: 6px; }
        .hidden { display: none; }
    </style>
</head>
<body>
<h2>Список ваших вопросов</h2>
<p><a href="dashboard.php">← Назад</a></p>

<div class="filters">
    <select id="filterType">
        <option value="">Все типы</option>
        <option value="single">Один ответ</option>
        <option value="multiple">Несколько</option>
        <option value="text">Текст</option>
    </select>
    <input type="text" id="searchInput" placeholder="Поиск по вопросу">
    <button id="exportFiltered">Экспортировать отфильтрованные</button>
</div>

<div id="questionsContainer">
    <?php foreach ($questions as $q): ?>
        <div class="question-card" data-id="<?= $q['id'] ?>" data-type="<?= $q['question_type'] ?>" data-text="<?= htmlspecialchars(strtolower($q['question_text'])) ?>">
            <strong><?= htmlspecialchars($q['question_text']) ?></strong> (<?= $q['question_type'] ?>)<br>

            <?php if ($q['question_type'] === 'text'): ?>
                <?php
                $stmt2 = $pdo->prepare("SELECT correct_text FROM tests.text_answers WHERE question_id = :qid");
                $stmt2->execute(['qid' => $q['id']]);
                $row = $stmt2->fetch();
                ?>
                <p><strong>Правильный ответ:</strong> <?= htmlspecialchars($row['correct_text'] ?? '—') ?></p>
            <?php else: ?>
                <ul>
                <?php
                $stmt2 = $pdo->prepare("SELECT option_text, is_correct FROM tests.options WHERE question_id = :qid");
                $stmt2->execute(['qid' => $q['id']]);
                foreach ($stmt2->fetchAll() as $opt): ?>
                    <li><?= htmlspecialchars($opt['option_text']) ?> <?= $opt['is_correct'] ? '✅' : '' ?></li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" action="delete_question.php" onsubmit="return confirm('Удалить этот вопрос?')">
                <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                <button type="submit">🗑 Удалить</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<script>
const filterType = document.getElementById('filterType');
const searchInput = document.getElementById('searchInput');
const questionsContainer = document.getElementById('questionsContainer');

function applyFilters() {
    const type = filterType.value;
    const keyword = searchInput.value.toLowerCase();

    document.querySelectorAll('.question-card').forEach(card => {
        const cardType = card.dataset.type;
        const cardText = card.dataset.text;

        const matchType = !type || cardType === type;
        const matchText = !keyword || cardText.includes(keyword);

        card.classList.toggle('hidden', !(matchType && matchText));
    });
}

filterType.addEventListener('change', applyFilters);
searchInput.addEventListener('input', applyFilters);

const exportBtn = document.getElementById('exportFiltered');
exportBtn.addEventListener('click', async () => {
    try {
        const res = await fetch('export_and_clear_questions.php');
        if (!res.ok) throw new Error('Ошибка при экспорте');

        const exportedIds = res.headers.get('X-Exported-IDs')?.split(',') || [];

        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'export_questions.csv';
        a.click();

        exportedIds.forEach(id => {
            const el = document.querySelector(`.question-card[data-id='${id}']`);
            if (el) el.remove();
        });
    } catch (err) {
        alert('Ошибка экспорта: ' + err.message);
    }
});
</script>
</body>
</html>