<?php
include '../config.php';

if (!isset($_SESSION['username'])) {
    echo '<script>alert("session required");location.href="/auth/login.php";</script>';
    die();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] == '') {
    die('Wrong Datatype');
}

$id = $_GET['id'];

$query = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
$query->bind_param('i', $id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_array();

if ($row['user_id'] != $_SESSION['user_id']) {
    echo '<script>alert("Cannot delete others post");location.href="/index.php";</script>';
    die();
}

$query = $conn->prepare("SELECT fileNames FROM files where post_id = ?");
$query->bind_param('i', $id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_array();

$query = $conn->prepare("DELETE FROM posts where id = ?");
$query->bind_param('i', $id);
$query->execute();

if($row && $row['fileNames']) {
    $fileNames = $row['fileNames'];
    $fileNames = explode('|',$fileNames);
    echo var_dump($fileNames);

    $userPath = $uploadDir . substr(hash('sha256', $_SESSION['username']), 0, 16);
    if (is_dir($userPath)) {
        $postPath = $userPath . '/' . $id;
        if (is_dir($postPath)) {
            for ($i=0; $i < count($fileNames); $i++) { 
                $file_name = base64_decode($fileNames[$i]);
                $file_path = $postPath . "/" . base64_encode($file_name);
                
                if (!file_exists($file_path)) continue;
                unlink($file_path);
            }             
            rmdir($postPath);
        }
    }    
}

die('<script>alert("Post deleted"); location.href = "/index.php"</script>');
?>