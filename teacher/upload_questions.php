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
$skipped = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        $header = fgetcsv($handle, 0, ",", '"', "\\"); 
        $inserted = 0;

        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM tests.questions WHERE question_text = :text");

        while (($row = fgetcsv($handle, 0, ",", '"', "\\")) !== false) {
            $question_text = trim($row[0] ?? '');
            $question_type = isset($row[1]) ? strtolower((string)$row[1]) : '';

            if (!$question_text || !in_array($question_type, ['single', 'multiple', 'text'])) {
                continue;
            }

            $stmtCheck->execute(['text' => $question_text]);
            if ($stmtCheck->fetchColumn() > 0) {
                $skipped[] = $question_text;
                continue;
            }

            $stmt = $pdo->prepare(
                "INSERT INTO tests.questions (question_text, question_type, created_by)
                 VALUES (:text, :type, :uid) RETURNING id"
            );
            $stmt->execute([
                'text' => $question_text,
                'type' => $question_type,
                'uid' => $teacher_id
            ]);
            $question_id = $stmt->fetchColumn();

            if ($question_type === 'text') {
                $correct_text = trim($row[10] ?? '');
                if ($correct_text !== '') {
                    $stmt = $pdo->prepare("INSERT INTO tests.text_answers (question_id, correct_text) VALUES (:qid, :text)");
                    $stmt->execute(['qid' => $question_id, 'text' => $correct_text]);
                }
            } else {
                for ($i = 0; $i < 4; $i++) {
                    $opt_index = 2 + $i * 2;
                    $correct_index = 3 + $i * 2;

                    $opt_text = trim($row[$opt_index] ?? '');
                    $correct_val = trim($row[$correct_index] ?? '');

                    $is_correct = $correct_val === '1' ? 1 : 0;

                    if ($opt_text !== '') {
                        $stmt = $pdo->prepare("INSERT INTO tests.options (question_id, option_text, is_correct)
                            VALUES (:qid, :text, :correct)");
                        $stmt->execute([
                            'qid' => $question_id,
                            'text' => $opt_text,
                            'correct' => $is_correct
                        ]);
                    }
                }
            }

            $inserted++;
        }

        fclose($handle);
        $message = "Импортировано вопросов: $inserted";
        if (!empty($skipped)) {
            $message .= ". Пропущено дубликатов: " . count($skipped);
        }
    } else {
        $error = 'Не удалось прочитать файл.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Импорт вопросов из CSV</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h2>Загрузка вопросов из CSV</h2>
    <p><a href="dashboard.php">← Назад</a></p>

    <?php if ($message): ?>
        <p style="color:green"><?= htmlspecialchars($message) ?></p>
    <?php elseif ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (!empty($skipped)): ?>
        <div style="background:#fff3cd;padding:10px;border:1px solid #ffeeba;margin-top:10px;">
            <strong>Пропущенные дубликаты:</strong>
            <ul>
                <?php foreach ($skipped as $q): ?>
                    <li><?= htmlspecialchars($q) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>CSV-файл: <input type="file" name="csv_file" accept=".csv" required></label><br><br>
        <button type="submit">Загрузить</button>
    </form>

    <p>🔹 Формат CSV-файла (с заголовками):</p>
    <pre style="background:#f8f8f8;padding:10px;">
question_text,question_type,option_1,correct_1,option_2,correct_2,option_3,correct_3,option_4,correct_4,correct_text
Какой язык?,single,Python,1,Java,0,C++,0,Ruby,0,
Укажите цвета,multiple,Белый,1,Синий,1,Красный,1,Зелёный,0,
Формула воды,text,,,,,,,,,H2O
    </pre>
</body>
</html>
