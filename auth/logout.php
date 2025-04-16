<?php
require_once dirname(__DIR__) . '/includes/session.php';
logout();
header("Location: login.php");
exit;
