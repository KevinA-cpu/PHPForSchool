<?php
include 'base.php';
include 'database.php';


// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$title = $author_string_list = $abstract = $conference_id = $topic_id = '';

// Fetch conferences for dropdown
$conferences_query = "SELECT conference_id, name FROM conferences ORDER BY name";
$conferences_result = $connection->query($conferences_query);

// Fetch topics for dropdown
$topics_query = "SELECT topic_id, topic_name FROM topics ORDER BY topic_name";
$topics_result = $connection->query($topics_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '';
    $author_string_list = isset($_POST['author_string_list']) ? htmlspecialchars($_POST['author_string_list']) : '';
    $abstract = isset($_POST['abstract']) ? htmlspecialchars($_POST['abstract']) : '';
    $conference_id = filter_var($_POST['conference_id'], FILTER_SANITIZE_NUMBER_INT);
    $topic_id = filter_var($_POST['topic_id'], FILTER_SANITIZE_NUMBER_INT);

    // Split author_string_list into an array of author names
    $authors = array_map('trim', explode(',', $author_string_list));

    // Validate each author name exists in the authors table
    $validAuthors = [];
    foreach ($authors as $author) {
        $author = trim($author);
        $author_query = "SELECT user_id FROM authors WHERE full_name = ?";
        $stmt = $connection->prepare($author_query);
        $stmt->bind_param("s", $author);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $validAuthors[] = $author;
        }
        $stmt->close();
    }

    // If all authors are valid, proceed to insert the paper
    if (count($validAuthors) == count($authors)) {
        // Prepare an SQL statement to insert paper
        $insert_query = "INSERT INTO papers (title, author_string_list, abstract, conference_id, topic_id, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($insert_query);
        $stmt->bind_param("sssiii", $title, $author_string_list, $abstract, $conference_id, $topic_id, $_SESSION['id']);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to papers list after successful creation
            header("Location: view_baibao.php");
            exit();
        } else {
            echo "Lỗi tạo bài báo: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Lỗi tạo bài báo: Một hoặc nhiều tác giả không có trong bảng tác giả.";
    }
}
?>

<div class="container">
    <h1>Tạo bài báo mới</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề:</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="author_string_list" class="form-label">Danh sách tác giả (phân tách bằng dấu phẩy):</label>
            <input type="text" class="form-control" id="author_string_list" name="author_string_list" required>
        </div>
        <div class="mb-3">
            <label for="abstract" class="form-label">Tóm tắt:</label>
            <textarea class="form-control" id="abstract" name="abstract" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label for="conference_id" class="form-label">Hội nghị:</label>
            <select class="form-control" id="conference_id" name="conference_id" required>
                <option value="">Chọn hội nghị</option>
                <?php while ($row = $conferences_result->fetch_assoc()) : ?>
                    <option value="<?php echo $row['conference_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="topic_id" class="form-label">Chủ đề:</label>
            <select class="form-control" id="topic_id" name="topic_id" required>
                <option value="">Chọn chủ đề</option>
                <?php while ($row = $topics_result->fetch_assoc()) : ?>
                    <option value="<?php echo $row['topic_id']; ?>"><?php echo htmlspecialchars($row['topic_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Tạo bài báo</button>
    </form>
    <a href="view_baibao.php" class="btn btn-secondary" style="margin-top: 10px;">Quay lại danh sách bài báo</a>
</div>