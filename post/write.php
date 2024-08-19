<?php
include '../config.php';

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

    $query = $conn->prepare("INSERT INTO posts (title, description, user_id) VALUES (?, ?, ?)");
    $query->bind_param('ssi', $title, $description, $_SESSION['user_id']);
    $query->execute();
    $result = $query->get_result();

    die('<script>alert("Post created"); location.href = "/main.php"</script>');
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
            <form action="/post/write.php" id="form" method="POST">
                <input type="text" id="title" name="title" placeholder="title" required>
                <input type="text" id="author" name="author" placeholder="author" readonly
                    value="<?php echo $_SESSION['username'] ?>" required>
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