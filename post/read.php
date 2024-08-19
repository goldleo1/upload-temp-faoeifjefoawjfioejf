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

$post_id = $_GET['id'];

$join1 = "LEFT JOIN users ON posts.user_id=users.id";
$join2 = "LEFT JOIN files ON posts.id=files.post_id";
$query = $conn->prepare("SELECT posts.title,posts.description,users.username,files.fileSizes,files.fileNames FROM posts ".$join1. " " .$join2. " WHERE posts.id=?");
$query->bind_param('i', $_GET['id']);
$query->execute();

$result = $query->get_result();
if ($result === false) {
    echo 'Error\n' . mysqli_error($conn);
    die();
}
if ($result->num_rows==0) {
    die('<script>alert("No Post exists"); location.href = "/index.php?page=1"</script>');
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
    <link rel="stylesheet" href="/static/css/reset.css">
    <style>
        #wrap {
            width: 1000px;
            margin: 0 auto;
            padding: 10px 0;
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

        .download {
            color: "black" !important;
            text-decoration: none !important;
        }
    </style>
    <script>
        window.onload = () => {
            document.getElementById('deleteBtn').addEventListener('click', (e) => {
                const author = document.getElementById('author').value;
                if (author != '<?php echo isset($_SESSION['username'])?$_SESSION['username']:'' ?>') {
                alert('다른 사람의 글을 삭제할 수 없습니다.');
            } else {
                const title = document.getElementById('title').value;
                const check = prompt(`삭제하려면 제목을 똑같이 입력하세요 : ${title}`);
                if (check == title) {
                    location.href = `/post/delete.php?id=<?php echo $_GET['id'] ?>`;
                } else {
                    alert('일치하지 않습니다.');
                }
            }
        });
        document.getElementById('editBtn').addEventListener('click', (e) => {
            const author = document.getElementById('author').value;
            if (author != '<?php echo isset($_SESSION['username'])?$_SESSION['username']:'' ?>') {
            alert('다른 사람의 글을 편집할 수 없습니다.');
        } else {
            location.href = `/post/edit.php?id=<?php echo $_GET['id'] ?>`;
        }
        });
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
                <input type="text" id="title" name="title" placeholder="title" readonly
                    value="<?php echo $row['title'] ?>">
                <input type="text" id="author" name="author" placeholder="author" readonly
                    value="<?php echo $row['username'] ?>">
                <textarea name="description" id="description" placeholder="description"
                    readonly><?php echo $row['description'] ?></textarea>
            </div>
            <?php if($row['fileNames']) {
                $fileNames = explode('|', $row['fileNames']);
                $fileSizes = explode('|', $row['fileSizes']);
                echo "<ul>";
                
                for ($i=0; $i < count($fileNames); $i++) { 
                    $fileName = base64_decode($fileNames[$i]);
                    $fileSize = base64_decode($fileSizes[$i]);
                    if ($fileSize>$mb) {
                        $_size = round($fileSize/$mb, 1) . "MB";
                    } elseif ($fileSize > $kb) {
                        $_size = round($fileSize/$kb, 1) . "KB";
                    } else {
                        $_size = $fileSize . "Byte";
                    }
                    $file = $_size;
                    echo "<li class='download'><a href='/post/download.php?post_id=".$post_id."&filename=".$fileName."' class='download'>" . $fileName ." (" . $file . ")" . "</a></li>";
                }
                echo "</ul>";
            }
            ?>
            <button id="editBtn">수정하기</button>
            <button id="deleteBtn">삭제하기</button>
        </main>
    </div>
</body>

</html>