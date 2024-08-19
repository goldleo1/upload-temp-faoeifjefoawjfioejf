<?php
include '../config.php';
include '../function.php';

$post_id = $_GET['post_id'];
$file_name = $_GET['filename'];

$userPath = $uploadDir . substr(hash('sha256', $_SESSION['username']), 0, 16);
if (!is_dir($userPath)) alert_back("File Not found");

$postPath = $userPath . '/' . $post_id;
if (!is_dir($postPath)) alert_back("File Not found");
 
$file_path = $postPath . "/" . base64_encode($file_name);

if (file_exists($file_path)) {
    header("Content-Type:application/octet-stream");
    header("Content-Disposition:attachment;filename={$file_name}");
    header("Content-Transfer-Encoding:binary");
    header("Content-Length:{$file_size}");
    header("Cache-Control:cache,must-revalidate");
    header("Pragma:no-cache");
    header("Expires:0");
 
    $fp = fopen($file_path, "r");
 
    while(!feof($fp)) {
        $buf = fread($fp, $file_size);
        $read = strlen($buf);
        print($buf);
        flush();
    }
 
    fclose($fp);
 
} else {
    alert_back("파일이 존재하지 않습니다.");
}