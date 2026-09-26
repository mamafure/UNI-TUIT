<?php
mysqli_report(MYSQLI_REPORT_OFF); // PHP 8.1+ throws by default; keep the plain die() below instead

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'uni_tuit_db';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
session_start();
?>