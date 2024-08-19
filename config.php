<?php
// 에러 디버깅

error_reporting(E_ALL);
ini_set("display_errors", 1);

$kb = 1024;
$mb = 1024 * 1024;
$uploadDir = "/var/www/html/uploads/";
$allowedExtensions = ["jpg", "jpeg", "png", "gif"];
$maxFileNameLength = 100; // 100글자
$maxFileSize = 5 * 1024 * 1024; // 5MB
$maxFileSizeSum = 25 * 1024 * 1024; // 50MB

session_cache_expire(60); // 60m
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