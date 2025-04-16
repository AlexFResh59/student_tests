<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question_id'])) {
    $qid = (int) $_POST['question_id'];

    $stmt = $pdo->prepare("SELECT id FROM tests.questions WHERE id = :qid AND created_by = :uid");
    $stmt->execute(['qid' => $qid, 'uid' => $teacher_id]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM tests.questions WHERE id = :qid");
        $stmt->execute(['qid' => $qid]);
    }
}

header('Location: questions_list.php');
exit;
