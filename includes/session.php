<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isTeacherLoggedIn() {
    return isset($_SESSION['user_id']) && $_SESSION['role'] === 'teacher';
}

function isStudentLoggedIn() {
    return isset($_SESSION['student_id']);
}

function logout() {
    session_unset();
    session_destroy();
}
