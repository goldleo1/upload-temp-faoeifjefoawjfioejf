<?php
include '../config.php';
include '../function.php';

if (!isset($_SESSION['username'])) {
    echo '<script>alert("session required");location.href="/auth/login.php";</script>';
    die();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];

    if (!is_string($title) || !is_string($author) || !is_string($description)) {
        die('Wrong Datatype');
    }

    if ($title=='' || $description=='') {
        die('Title or description is missing');
    }
    if (strlen($title)>100 || strlen($description)>10000) {
        die('Too Long');
    }

    if ($author != $_SESSION['username']) {
        die('Author must be your username');
    }

    $fileCheck = isset($_FILES['file']) && $_FILES["file"]["name"][0];

    if ($fileCheck) {
        $cnt = count($_FILES["file"]["name"]) ? count($_FILES["file"]["name"]) : 0;
        $fileSizeSum = 0;
        $fileNames = array();
        $fileSizes = array();
        echo var_dump($_FILES["file"]["name"]);
        
        if ($cnt > 5) alert_back("첨부파일이 너무 많습니다. 최대 5개입니다.");
        for ($i=0; $i < $cnt; $i++) {
            $fileSize = $_FILES["file"]["size"][$i];
            $fileName = $_FILES["file"]["name"][$i];
            if ($fileSize > $maxFileSize) alert_back("단일 파일 크기가 너무 큽니다. 최대 단일 파일 크기는 " . ($maxFileSize / (1024 * 1024)) . "MB입니다.");
            if (strlen($fileName) > 50) alert_back("파일 이름이 너무 깁니다. 최대 50자입니다.");
            $fileSizeSum += $fileSize;

            $fileNames[] = base64_encode($fileName);
            $fileSizes[] = base64_encode((string)$fileSize);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) alert_back("지원하지 않는 파일 형식입니다. " . implode(', ', $allowedExtensions) . "파일만 허용됩니다.");
        }
        if ($fileSize > $maxFileSizeSum) alert_back("총 파일 크기가 너무 큽니다. 최대 총 파일 크기는 " . ($maxFileSize / (1024 * 1024)) . "MB입니다.");          
    }

    $query = $conn->prepare("INSERT INTO posts (title, description, user_id) VALUES (?, ?, ?)");
    $query->bind_param('ssi', $title, $description, $_SESSION['user_id']);
    $query->execute();
    $result = $query->get_result();

    if($fileCheck) {
        $query = $conn->prepare("SELECT id FROM posts WHERE title=? AND user_id=? order by createdAt DESC limit 1");
        $query->bind_param('ss', $title, $_SESSION['user_id']);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_array();
        $post_id = $row['id'];

        $userPath = $uploadDir . substr(hash('sha256', $_SESSION['username']), 0, 16);
        if (!is_dir($userPath)) mkdir($userPath);
    
        $postPath = $userPath . '/' . $post_id;
        if (!is_dir($postPath)) mkdir($postPath);    
    
        for ($i=0; $i < $cnt; $i++) {
            $fileName = $fileNames[$i];
    
            $uploadPath = $postPath . '/' . $fileName;
    
            if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploadPath)) {
                echo "파일 업로드 성공: " . $uploadPath;
            } else {
                die("파일 업로드 실패. but post created");
            }
        }
        $_sizes = implode('|', $fileSizes);
        $_names = implode('|', $fileNames);
        $query = $conn->prepare("INSERT INTO files (fileSizes, fileSizeSum, fileCount, fileNames, user_id, post_id) VALUES (?, ?, ?, ?, ?, ?)");
        $query->bind_param('siisii', $_sizes, $fileSizeSum, $cnt, $_names, $_SESSION['user_id'], $post_id);
        $query->execute();
        $result = $query->get_result();
    }


    // die(var_dump($fileSizes));  
    die('<script>alert("Post created");location.href = "/index.php?page=1"</script>');
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
            width: 100%;
            font-size: 16px;
            line-height: 16px;
        }

        main form input {
            width: 100%;
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

        main form input[type="file"] {
            height: 30px;
        }

        main form textarea {
            width: 100%;
            height: 400px;
            margin-top: 10px;
            font-size: inherit;
            resize: none;
        }

        main form div#letterCounter {
            color: green;
            width: 100%;
            text-align: right;
            margin-top: 2px;
        }

        main form div#letterCounter span {
            color: green;
        }
    </style>
    <script>
        window.onload = () => {
            const textarea = document.querySelector('textarea#description');
            const letterCounter = document.getElementById('letterCounter');
            const form = document.getElementById('form');
            textarea.addEventListener('keydown', (e) => {
                setTimeout(() => {
                    const { length } = e.target.value;
                    if (length >= 5000) {
                        letterCounter.style.color = 'red';
                    } else if (letterCounter.style.color == 'red') {
                        letterCounter.style.color = 'green';
                    }
                    letterCounter.innerText = `(${length}/5000)`;
                }, 0);
            })
            form.addEventListener('submit', (e) => {
                const { length } = textarea.value;
                if (length > 5000) {
                    alert('5000자 제한!!');
                    e.preventDefault();
                }
            })
        }
    </script>
</head>


<body>
    <div id="wrap">
        <header>
            <h1>KnockOn!! 커뮤니티</h1>
        </header>
        <main>
            <form action="/post/write.php" id="form" method="POST" enctype="multipart/form-data">
                <input type="text" id="title" name="title" placeholder="title" required>
                <input type="text" id="author" name="author" placeholder="author" readonly
                    value="<?php echo $_SESSION['username'] ?>" required>
                <input type="file" name="file[]" id="file" value="파일첨부" multiple>
                <textarea name="description" id="description" placeholder="description" required></textarea>
                <div id="letterCounter">
                    <span>(0/5000)</span>
                </div>
                <input type="submit" value="작성하기" id="submitBtn">
            </form>
        </main>
    </div>
</body>

</html>