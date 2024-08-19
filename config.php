<?php
// 에러 디버깅
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

session_start();

$host = 'localhost';
$db_username = 'guest';
$db_password = 'guest';
$database = 'knockOn';


$conn = new mysqli($host, $db_username, $db_password, $database);

if (!$conn) {
    die('MySQL 연결 실패: ' . mysqli_connect_error());
}
?>