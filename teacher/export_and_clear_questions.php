<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export_questions.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'question_text', 'question_type',
    'option_1', 'correct_1',
    'option_2', 'correct_2',
    'option_3', 'correct_3',
    'option_4', 'correct_4',
    'correct_text'
]);

$stmt = $pdo->prepare("SELECT * FROM tests.questions WHERE created_by = :uid ORDER BY id");
$stmt->execute(['uid' => $teacher_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($questions as $q) {
    $row = array_fill(0, 11, ''); 

    $row[0] = $q['question_text'];
    $row[1] = $q['question_type'];

    if ($q['question_type'] === 'text') {
        $stmt2 = $pdo->prepare("SELECT correct_text FROM tests.text_answers WHERE question_id = :qid");
        $stmt2->execute(['qid' => $q['id']]);
        $row[10] = $stmt2->fetchColumn() ?? '';
    } else {
        $stmt2 = $pdo->prepare("SELECT option_text, is_correct FROM tests.options WHERE question_id = :qid ORDER BY id");
        $stmt2->execute(['qid' => $q['id']]);
        $options = $stmt2->fetchAll();

        for ($i = 0; $i < min(4, count($options)); $i++) {
            $row[2 + $i * 2] = $options[$i]['option_text'];
            $row[3 + $i * 2] = $options[$i]['is_correct'] ? 1 : 0;
        }
    }

    fputcsv($output, $row);
}

$stmt = $pdo->prepare("DELETE FROM tests.questions WHERE created_by = :uid");
$stmt->execute(['uid' => $teacher_id]);

fclose($output);
exit;
