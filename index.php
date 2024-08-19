<?php
include 'config.php';
if (!isset($_GET['page']) || !is_numeric($_GET['page']) || $_GET['page'] < 1) {
    header("Location: /index.php?page=1");
}
$page = (int)$_GET['page'];
$order = ' ORDER BY createdAt DESC';
$limit = ' limit ' . ($page-1)*10 . ',10';
if (isset($_GET['search']) && is_string($_GET['search']) && $_GET['search'] != '') {
    $search = '%' . $_GET['search'] . '%';
    $_q = "SELECT posts.id,title,description,createdAt,users.username FROM posts LEFT JOIN users ON posts.user_id=users.id where title like ? OR description like ? OR username like ?";
    $q = $_q . $order . $limit;
    $query = $conn->prepare($q);
    $query->bind_param('sss', $search, $search, $search);
} else {
    $_q = "SELECT posts.id,title,description,createdAt,users.username FROM posts LEFT JOIN users ON posts.user_id=users.id";
    $q = $_q . $order . $limit;
    
    $query = $conn->prepare($q);
}
$query->execute();
$result = $query->get_result();
if (!$result) {
    echo 'Error ' . mysqli_error($conn);
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/static/css/reset.css">
    <link rel="stylesheet" href="/static/css/default.css">
    <style>
        header div.right span:not(:first-child)::before {
            content: '';
            height: 1px;
            padding: 0;
            margin: 0 10px;
            border-left: 1px solid black;
        }

        nav {
            text-align: right;
        }

        main div.methods {
            text-align: right;
        }

        main table {
            width: 100%;
            margin: 20px auto;
            text-align: center;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
    <script>
        window.onload = () => {
            const searchInput = document.getElementById('search');
            const writePostBtn = document.getElementById('writePost');
            searchInput.addEventListener('keypress', (e) => {
                if (e.code === 'Enter') {
                    location.href = `${location.origin}/main.php?search=${e.target.value}`;
                }
            })

            writePostBtn.addEventListener('click', (e) => {
                location.href = `${location.origin}/post/write.php`;
            })

            Array.from(document.getElementsByClassName('deleteBtn')).forEach(ele => {
                ele.addEventListener('click', (e) => {
                    const tr = e.target.parentNode.parentNode;
                    const postId = tr.id;
                    const author = tr.querySelector('td.author').innerText;
                    if (author != '<?php echo $_SESSION['username'] ?>') {
                    alert('다른 사람의 글을 삭제할 수 없습니다.');
                } else {
                    const title = tr.querySelector('td.title');
                    const check = prompt(`삭제하려면 제목을 똑같이 입력하세요 : ${title.innerText}`);
                    if (check == title.innerText) {
                        location.href = `/post/delete.php?id=${postId}`;
                    } else {
                        alert('일치하지 않습니다.');
                    }
                }
            })
        });
        Array.from(document.getElementsByClassName('editBtn')).forEach(ele => {
            ele.addEventListener('click', (e) => {
                const tr = e.target.parentNode.parentNode;
                const postId = tr.id;
                const author = tr.querySelector('td.author').innerText;
                if (author != '<?php echo $_SESSION['username'] ?>') {
                alert('다른 사람의 글을 편집할 수 없습니다.');
            } else {
                location.href = `/post/edit.php?id=${postId}`;
            }
        })
            });
        }
    </script>
</head>



<body>
    <div id="wrap">
        <header>
            <div class="right">
                <?php
                if (isset($_SESSION['username'])) {
                    echo '<span>' . $_SESSION['year'] . '기 ' . $_SESSION['track'] . '</span>';
                    echo '<span>' . $_SESSION['username'] . '님</span>';
                    echo '<span><a href="/auth/logout.php">[LOGOUT]</a></span>';
                }
                else {
                    echo '<span><a href="/auth/login.php">[LOGIN]</a></span>';
                }
                ?>
            </div>
            <h1>KnockOn!! 커뮤니티</h1>
        </header>
        <nav>
            <input type="text" name="" id="search" placeholder="검색">
        </nav>
        <main>
            <div class="methods">
                <button id="writePost">글쓰기</button>
            </div>
            <table>
                <colgroup>
                    <col width="10%" />
                    <col width="25%" />
                    <col width="35%" />
                    <col width="15%" />
                    <col width="10%" />
                </colgroup>
                <tr>
                    <th class="date">날짜</th>
                    <th class="title">제목</th>
                    <th class="desription">내용</th>
                    <th class="author">작성자</th>
                    <th class="author">보기</th>
                </tr>
                <?php
                    if ($result) {
                        while ($row = mysqli_fetch_array($result)) {
                            // echo var_dump($row);
                            echo '<tr id="' . $row['id'] . '">';
                            echo '<td class="date">' . explode(' ', $row['createdAt'])[0] . '</td>';
                            echo '<td class="title"><a href="/post/read.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></td>';
                            echo '<td class="description">' . htmlspecialchars(substr($row['description'], 0, 30)) . '</td>';
                            echo '<td class="author">' . htmlspecialchars($row['username']) . '</td>';
                            echo '<td class="author"> <button class="editBtn">수정하기</button> <button class="deleteBtn">삭제하기</button> </td>';
                        }
                    }
                    mysqli_free_result($result);
                    mysqli_close($conn);
                ?>
            </table>
        </main>
    </div>
</body>

</html>