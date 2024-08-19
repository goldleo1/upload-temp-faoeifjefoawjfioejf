<?php
include '../config.php';

if (!isset($_SESSION['username'])) {
    echo '<script>alert("session required");location.href="/auth/login.php";</script>';
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $id = $_POST['id'];
    $author = $_POST['author'];
    $description = $_POST['description'];

    if (!is_string($title) || !is_string($author) || !is_string($description)) {
        die('Wrong Datatype');
    }

    if (strlen($title)>100 || strlen($title)>10000) {
        die('Too Long');
    }

    $query = $conn->prepare("SELECT user_id FROM posts WHERE id=?");
    $query->bind_param('i', $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_array();

    if ($row['user_id'] != $_SESSION['user_id']) {
        die('<script>alert("Cannot edit others post"); location.href = "/index.php"</script>');
    }

    $query = $conn->prepare("UPDATE posts SET title = ?, description = ? where id = ?");
    $query->bind_param('ssi', $title, $description, $id);
    $query->execute();
    $result = $query->get_result();

    die('<script>alert("Post edited"); location.href = "/index.php"</script>');
} else {
    if (!isset($_GET['id']) || $_GET['id'] == '') {
        echo 'Usage /post/edit.php?id=1';
        die();
    }
    if (!is_numeric($_GET['id'])) {
        echo 'no hack';
        die();
    }
    $query = $conn->prepare("SELECT posts.id, posts.title, posts.description, posts.user_id, users.username FROM posts LEFT JOIN users ON posts.user_id=users.id WHERE posts.id=?");
    $query->bind_param('i', $_GET['id']);
    $query->execute();

    $result = $query->get_result();
    if ($result === false) {
        echo 'Error\n' . mysqli_error($conn);
        die();
    }
    if ($result->num_rows==0) {
        die('<script>alert("No Post exists"); location.href = "/index.php"</script>');
    }
    else {
        $row = $result->fetch_array();
        if ($row['username'] != $_SESSION['username']) {
            die('<script>alert("Cannot edit others post"); location.href = "/index.php"</script>');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        #wrap {
            width: 1000px;
            margin: 0 auto;
        }

        nav {
            text-align: right;
        }

        main {
            width: 1000px;
            margin: 0 auto;
            margin-top: 50px;
        }

        main form {
            display: flex;
            flex-direction: column;
            font-size: 16px;
            line-height: 16px;
        }

        main form input {
            margin-top: 10px;
            height: 20px;
            font-size: inherit;
            line-height: inherit;
        }

        main form input[type="submit"] {
            width: 100px;
            height: 30px;
            margin: 0 auto;
        }

        main form textarea {
            height: 400px;
            margin-top: 10px;
            font-size: inherit;
            resize: none;
        }
    </style>
    <script>
        window.onload = () => {

        }
    </script>
</head>

<body>
    <div id="wrap">
        <header>
            <h1>KnockOn!! 커뮤니티</h1>
        </header>
        <main>
            <form action="/post/edit.php" method="POST">
                <input type="text" id="title" name="title" placeholder="title" value="<?php echo $row['title'] ?>">
                <input type="text" id="author" name="author" placeholder="author" readonly
                    value="<?php echo $row['username'] ?>">
                <textarea name="description" id="description"
                    placeholder="description"><?php echo $row['description'] ?></textarea>
                <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                <input type="submit" value="작성하기" id="submitBtn">
            </form>
        </main>
    </div>
</body>

</html>