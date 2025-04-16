<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_id'])) {
    $tid = (int) $_POST['test_id'];

    $stmt = $pdo->prepare("SELECT id FROM tests.tests WHERE id = :tid AND created_by = :uid");
    $stmt->execute(['tid' => $tid, 'uid' => $teacher_id]);

    if ($stmt->fetch()) {

        $pdo->prepare("DELETE FROM tests.answers WHERE result_id IN (
            SELECT id FROM tests.results WHERE test_id = :tid
        )")->execute(['tid' => $tid]);

        $pdo->prepare("DELETE FROM tests.results WHERE test_id = :tid")->execute(['tid' => $tid]);

        $pdo->prepare("DELETE FROM tests.test_questions WHERE test_id = :tid")->execute(['tid' => $tid]);

        $pdo->prepare("DELETE FROM tests.student_logins WHERE test_id = :tid")->execute(['tid' => $tid]);

        $pdo->prepare("DELETE FROM tests.tests WHERE id = :tid")->execute(['tid' => $tid]);
    }
}

header('Location: tests_list.php');
exit;
