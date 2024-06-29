<?php
include 'base.php';
include 'database.php';

// Check if the form is submitted for login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate user credentials against database (replace with your actual authentication logic)
    $query = "SELECT user_id, user_type FROM users WHERE username = ? AND password = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User authenticated successfully
        $row = $result->fetch_assoc();
        session_start();
        $_SESSION['id'] = $row['user_id'];
        $_SESSION['username'] = $username;
        $_SESSION['user_type'] = $row['user_type'];
        header("Location: index.php"); // Redirect to dashboard or another secure page
        exit();
    } else {
        // Invalid credentials
        echo "<p style='color: red; text-align: center;'>Invalid username or password.</p>";
    }

    $stmt->close();
}
?>

<div class="container">
    <div class="toast" id="validationToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="toast-body">
            Tất cả các trường là bắt buộc!
        </div>
    </div>

    <h1>Đăng nhập</h1>
    <form id="loginForm" onsubmit="return validateForm()" method="post" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" class="form-control" id="username" name="username">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <!-- Validation toast script -->
    <script>
        var toastEl = document.getElementById('validationToast');
        var toast = new bootstrap.Toast(toastEl);

        document.querySelector('.close').addEventListener('click', function() {
            toast.hide();
        });

        function validateForm() {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;

            if (!username || !password) {
                toast.show();
                return false;
            }

            return true;
        }
    </script>
</div>