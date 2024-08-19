<!DOCTYPE html>
<html lang="en">
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../config.php";

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!is_string($username) || !is_string($password)) {
        die("Wrong Datatype");
    }

    if (strlen($username)>32 || strlen($password)>32) {
        die("Too Long");
    }

    $query = $conn->prepare("SELECT username FROM users WHERE username=?");
    $query->bind_param('s', $username);
    $query->execute();
    $result = $query->get_result();

    if ($result === false) {
        echo 'Error\n' . mysqli_error($conn);
        die();
    }
    if ($result->num_rows==0) {
        die('<script>alert("User not exists"); location.href = "/auth/register.php"</script>');
    }
    else {
        $query = $conn->prepare("SELECT id,username,year,track FROM users WHERE username=? AND password=?");
        $query->bind_param('ss', $username, $password);
        $query->execute();
        $result = $query->get_result();

        if ($result === false) {
            echo 'Error\n' . mysqli_error($conn);
            die();
        }
        if ($result->num_rows == 0) {
            die('<script>alert("Wrong credential"); location.href = "/auth/login.php"</script>');
        }
        else {
            $row = $result->fetch_array();

            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['year'] = $row['year'];
            $_SESSION['track'] = $row['track'];
            die('<script>alert("Logined"); location.href = "/index.php"</script>');
        }
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/static/css/reset.css">
    <style>
        *::-webkit-scrollbar {
            display: none;
        }


        body {
            background-image: url('/static/image/sky.jpg');
            color: white;
        }

        main {
            margin: 0 auto;
            text-align: center;
        }

        main div {
            position: absolute;
            width: 400px;
            height: 500px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0 auto;
            padding: 50px 30px;
            border: 1px solid white;
            border-radius: 50px;
        }

        main div h1 {
            position: relative;
            top: -50px;
        }

        main div form input {
            width: 100%;
            padding: 10px;
            margin: 10px auto;
            background-color: inherit;
            color: white;
            border: 1px solid white;
            border-radius: 20px;
        }

        main div form input::placeholder {
            color: #eee;
        }

        main div p a {
            text-decoration: none;
        }
    </style>
    <script>
        window.onload = () => {
        }
    </script>
</head>

<body>
    <div id="wrap">
        <main>
            <div>
                <h1>Login</h1>
                <form action="/auth/login.php" method="POST">

                    <input type="text" name="username" id="username" placeholder="username">
                    <input type="text" name="password" id="password" placeholder="password">
                    <input type="submit" value="로그인">
                </form>
                <p><a href="/auth/register.php">Register ></a></p>
            </div>
        </main>
    </div>
</body>

</html>