<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isStudentLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$test_id = $_SESSION['test_id'];

$stmt = $pdo->prepare("
    SELECT q.id AS qid, q.question_type, o.id AS oid, o.is_correct, ta.correct_text
    FROM tests.test_questions tq
    JOIN tests.questions q ON tq.question_id = q.id
    LEFT JOIN tests.options o ON o.question_id = q.id
    LEFT JOIN tests.text_answers ta ON ta.question_id = q.id
    WHERE tq.test_id = :tid
");
$stmt->execute(['tid' => $test_id]);

$question_map = [];
foreach ($stmt->fetchAll() as $row) {
    $qid = $row['qid'];
    if (!isset($question_map[$qid])) {
        $question_map[$qid] = [
            'type' => $row['question_type'],
            'correct_options' => [],
            'correct_text' => $row['correct_text']
        ];
    }
    if ($row['oid'] && $row['is_correct']) {
        $question_map[$qid]['correct_options'][] = (string)$row['oid'];
    }
}

$answers = $_POST['answers'] ?? [];
$text_answers = $_POST['text_answers'] ?? [];

$score = 0;
$total_questions = count($question_map);

foreach ($question_map as $qid => $q) {
    $type = $q['type'];

    if ($type === 'text') {
        $student_answer = trim(strtolower($text_answers[$qid] ?? ''));
        $correct_answer = trim(strtolower($q['correct_text'] ?? ''));
        if ($student_answer === $correct_answer && $correct_answer !== '') {
            $score++;
        }
    } elseif ($type === 'single') {
        $selected = isset($answers[$qid]) ? [(string)$answers[$qid]] : [];
        $correct = $q['correct_options'];
        if ($selected === $correct) {
            $score++;
        }
    } elseif ($type === 'multiple') {
        $selected = array_map('strval', $answers[$qid] ?? []);
        $correct = $q['correct_options'];
        sort($selected);
        sort($correct);
        if ($selected === $correct) {
            $score++;
        }
    }
}

$stmt = $pdo->prepare("
    INSERT INTO tests.results (login_id, test_id, score)
    VALUES (:sid, :tid, :score) RETURNING id
");
$stmt->execute([
    'sid' => $student_id,
    'tid' => $test_id,
    'score' => $score
]);
$result_id = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    INSERT INTO tests.answers (result_id, question_id, option_id)
    VALUES (:rid, :qid, :oid)
");

foreach ($answers as $qid => $oids) {
    if (!is_array($oids)) $oids = [$oids]; 
    foreach ($oids as $oid) {
        $stmt->execute([
            'rid' => $result_id,
            'qid' => $qid,
            'oid' => $oid
        ]);
    }
}

header('Location: result.php');
exit;
