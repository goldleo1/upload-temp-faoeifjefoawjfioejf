<?php 
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../config.php";

    $username = $_POST['username'];
    $password = $_POST['password'];
    $track = $_POST['track'];
    $year = $_POST['year']; 

    if (!is_string($username) || !is_string($password) || !is_string($track) || !is_numeric($year) ) {
        die("Wrong Datatype");
    }

    if ($track != 'WEB' && $track != 'REV') {
        die("Wrong track");
    }

    if ($year != '1' && $year != '2') {
        die("Wrong year");
    }

    if (strlen($username)>32 || strlen($password)>32) {
        die("Too Long");
    }

    if (!preg_match("/^[a-zA-Z0-9_\-@]+$/",$username)) {
        die("Invalid char");
    }

    $query = $conn->prepare("SELECT username FROM users where username=?");
    $query->bind_param('s', $username);
    $query->execute();
    $result = $query->get_result();

    if (!$result) {
        echo 'Error ' . mysqli_error($conn);
    }

    if ($result->num_rows!=0) {
        die('<script>alert("User already exists"); location.href = "/auth/register.php"</script>');
    }
    else {
        $query = $conn->prepare("INSERT INTO users (username, password, year, track) VALUES(?, ?, ?, ?)");
        $query->bind_param('ssis', $username, $password, $year, $track);
        $query->execute();
        $result = $query->get_result();
        die('<script>alert("Registered"); location.href = "/auth/login.php"</script>');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/static/reset.css">
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
            top: -30px;
        }

        main div form input,
        main div form select {
            width: 100%;
            padding: 10px;
            margin: 10px auto;
            background-color: inherit;
            color: white;
            border: 1px solid white;
            border-radius: 20px;
        }

        main div form input:focus,
        select:focus {
            outline: none;
        }

        select option {
            color: black;
            background: white;
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
                <h1>Register</h1>
                <form action="/auth/register.php" method="POST">

                    <input type="text" name="username" id="username" placeholder="username" autocomplete="off"
                        pattern="[a-zA-Z0-9_\-@]+" required>
                    <input type="text" name="password" id="password" placeholder="password" autocomplete="off"
                        pattern="[a-zA-Z0-9_\-@]+" required>
                    <select name="track" id="track" required>
                        <option value="WEB">WEB</option>
                        <option value="REV">REV</option>
                    </select>
                    <input type="number" name="year" id="year" placeholder="year" value="2" required>
                    <input type="submit" value="계정 생성">
                </form>
                <p><a href="/auth/login.php">Login ></a></p>
            </div>
        </main>
    </div>
</body>

</html>