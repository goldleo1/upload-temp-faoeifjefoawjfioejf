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

$query = $conn->prepare("DELETE FROM posts where id = ?");
$query->bind_param('i', $id);
$query->execute();
$result = $query->get_result();

die('<script>alert("Post deleted"); location.href = "/index.php"</script>');
?>