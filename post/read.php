<?php
include '../config.php';
if (!isset($_GET['id']) || $_GET['id'] == '') {
    echo 'Usage /post/read.php?id=1';
    die();
}
if (!is_numeric($_GET['id'])) {
    echo 'no hack';
    die();
}
$query = $conn->prepare("SELECT posts.title, posts.description, users.username FROM posts LEFT JOIN users ON posts.user_id=users.id WHERE posts.id=?");
$query->bind_param('i', $_GET['id']);
$query->execute();

$result = $query->get_result();
if ($result === false) {
    echo 'Error\n' . mysqli_error($conn);
    die();
}
if ($result->num_rows==0) {
    die('<script>alert("No Post exists");location.href="/main.php"</script>');
}
else {
    $row = $result->fetch_array();
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

        /* readonly */
        input:focus,
        textarea:focus {
            outline: none;
        }

        input:active,
        textarea:active {
            outline: none;
        }

        nav {
            text-align: right;
        }

        main {
            width: 1000px;
            margin: 0 auto;
            margin-top: 50px;
        }

        main div {
            display: flex;
            flex-direction: column;
            font-size: 16px;
            line-height: 16px;
        }

        main div input {
            margin-top: 10px;
            height: 20px;
            font-size: inherit;
            line-height: inherit;
        }

        main div input[type="submit"] {
            width: 100px;
            height: 30px;
            margin: 0 auto;
        }

        main div textarea {
            height: 400px;
            margin-top: 10px;
            font-size: inherit;
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
            <div>
                <input type="text" id="title" name="title" placeholder="title" readonly value="<?php echo $row['title'] ?>">
                <input type="text" id="author" name="author" placeholder="author" readonly value="<?php echo $row['username'] ?>">
                <textarea name="description" id="description" placeholder="description" readonly><?php echo $row['description'] ?></textarea>
            </div>
        </main>
    </div>
</body>

</html>