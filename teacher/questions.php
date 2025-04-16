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
    $text = trim($_POST['question_text']);
    $type = $_POST['question_type'];

    if (empty($text)) {
        $error = 'Текст вопроса обязателен.';
    } elseif (!in_array($type, ['single', 'multiple', 'text'])) {
        $error = 'Недопустимый тип вопроса.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO tests.questions (question_text, question_type, created_by) VALUES (:text, :type, :uid) RETURNING id");
        $stmt->execute([
            'text' => $text,
            'type' => $type,
            'uid' => $teacher_id
        ]);
        $question_id = $stmt->fetchColumn();

        if ($type === 'text') {
            $correct = trim($_POST['correct_text']);
            if ($correct !== '') {
                $stmt = $pdo->prepare("INSERT INTO tests.text_answers (question_id, correct_text) VALUES (:qid, :text)");
                $stmt->execute([
                    'qid' => $question_id,
                    'text' => $correct
                ]);
                $message = 'Текстовый вопрос добавлен.';
            } else {
                $error = 'Введите правильный текстовый ответ.';
            }
        } else {
            $options = $_POST['options'] ?? [];
            $corrects = $_POST['correct'] ?? [];

            if ($type === 'single') {
                $corrects = is_numeric($corrects) ? intval($corrects) : -1;
            }

            $inserted = 0;
            foreach ($options as $i => $opt_text) {
                $opt_text = trim($opt_text);
                if ($opt_text === '') continue;

                if ($type === 'single') {
                    $is_correct = ($corrects === $i);
                } else {
                    $is_correct = is_array($corrects) && in_array($i, $corrects);
                }

                $stmt = $pdo->prepare("INSERT INTO tests.options (question_id, option_text, is_correct) VALUES (:qid, :text, :correct)");
                $stmt->execute([
                    'qid' => $question_id,
                    'text' => $opt_text,
                    'correct' => $is_correct ? 1 : 0
                ]);
                $inserted++;
            }

            $message = "Вопрос добавлен. Вариантов: $inserted";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Создание вопроса</title>
    <link rel="stylesheet" href="/style.css">
    <script>
        function toggleAnswerFields() {
            const type = document.querySelector('[name="question_type"]').value;
            const optionBlock = document.getElementById('option_block');
            const textBlock = document.getElementById('text_block');

            optionBlock.style.display = (type === 'text') ? 'none' : 'block';
            textBlock.style.display = (type === 'text') ? 'block' : 'none';

            document.querySelectorAll('.correct-select').forEach((el, i) => {
                if (type === 'single') {
                    el.type = 'radio';
                    el.name = 'correct';
                } else {
                    el.type = 'checkbox';
                    el.name = 'correct[]';
                }
            });
        }
    </script>
</head>
<body>
    <h2>Создание вопроса</h2>

    <?php if ($message): ?>
        <p style="color:green"><?= htmlspecialchars($message) ?></p>
    <?php elseif ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Текст вопроса:<br>
            <textarea name="question_text" rows="3" style="width:100%" required></textarea>
        </label><br>

        <label>Тип вопроса:<br>
            <select name="question_type" id="question_type" required>
                <option value="single">Один правильный ответ</option>
                <option value="multiple">Несколько правильных ответов</option>
                <option value="text">Текстовый ответ</option>
            </select>
        </label><br>

        <div id="option_block">
            <h4>Варианты ответа:</h4>
            <?php for ($i = 0; $i < 4; $i++): ?>
                <label>
                    <input class="correct-select" type="checkbox" name="correct[]" value="<?= $i ?>">
                    <input type="text" name="options[]" placeholder="Вариант <?= $i + 1 ?>" style="width:60%">
                </label><br>
            <?php endfor; ?>
        </div>

        <div id="text_block" style="display:none;">
            <label>Правильный ответ (текст):<br>
                <input type="text" name="correct_text" style="width:100%">
            </label>
        </div>

        <br>
        <button type="submit">Сохранить вопрос</button>
    </form>

    <p><a href="dashboard.php">← Назад</a></p>

    <script>
        toggleAnswerFields();
        document.getElementById('question_type').addEventListener('change', toggleAnswerFields);
    </script>
</body>
</html>
