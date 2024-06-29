<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thi cuối kỳ ứng dụng phân tán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Thi cuối kỳ ứng dụng phân tán</a>
        <div class="collapse navbar-collapse" style="justify-content: space-between;">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="view_baibao.php">Xem bài báo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search_baibao.php">Tìm kiếm bài báo</a>
                </li>
            </ul>

            <ul class="navbar-nav mr-auto">
                <ul class="navbar-nav ml-auto">
                    <?php
                    session_start();
                    if (isset($_SESSION['username'])) {
                        $username = $_SESSION['username'];
                        $userId = $_SESSION['id'];
                        echo '<li class="nav-item"><a class="nav-link" href="profile.php?id=' . $userId . '">Xin chào, ' . $username . '</a></li>';
                        echo '<li class="nav-item"><a class="nav-link" href="logout.php">Đăng xuất</a></li>';
                    } else {
                        echo '<li class="nav-item"><a class="nav-link" href="login.php">Đăng nhập</a></li>';
                    }
                    ?>
                </ul>

            </ul>
        </div>
    </nav>
    <div class="container">
        <!-- Page content goes here -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>