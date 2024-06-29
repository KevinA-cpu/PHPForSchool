<?php
include 'base.php';
include 'database.php';


// Function to check if the user is an admin
function isAdmin()
{
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin';
}

// Fetch paper details based on paper_id from GET parameter
if (isset($_GET['id'])) {
    $paperId = $_GET['id'];

    $query = "
        SELECT papers.*, conferences.name AS conference_name, conferences.abbreviation, conferences.start_date, conferences.end_date, conferences.type, topics.topic_name 
        FROM papers 
        JOIN conferences ON papers.conference_id = conferences.conference_id 
        JOIN topics ON papers.topic_id = topics.topic_id 
        WHERE papers.paper_id = ?
    ";

    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $paperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $paper = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "No paper ID provided.";
    exit();
}

// Handle form submission to update author_string_list
if (isset($_POST['updateAuthors'])) {
    // Extract and sanitize author list
    $authorList = $_POST['author_string_list'];
    $authors = explode(', ', $authorList);

    // Check if all authors exist in authors table full_name
    $placeholders = implode(',', array_fill(0, count($authors), '?'));
    $query = "SELECT full_name FROM authors WHERE full_name IN ($placeholders)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param(str_repeat('s', count($authors)), ...$authors);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingAuthors = [];
    while ($row = $result->fetch_assoc()) {
        $existingAuthors[] = $row['full_name'];
    }
    $stmt->close();

    // Validate that all provided authors exist in authors table
    foreach ($authors as $author) {
        if (!in_array($author, $existingAuthors)) {
            echo "Author '$author' does not exist in the database.";
            exit();
        }
    }

    // Update paper's author_string_list
    $query = "UPDATE papers SET author_string_list = ? WHERE paper_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("si", $authorList, $paperId);
    if ($stmt->execute()) {
        echo "Author list updated successfully.";
    } else {
        echo "Error updating author list: " . $stmt->error;
    }
    $stmt->close();

    // Refresh paper details after update
    $query = "
        SELECT papers.*, conferences.name AS conference_name, conferences.abbreviation, conferences.start_date, conferences.end_date, conferences.type, topics.topic_name 
        FROM papers 
        JOIN conferences ON papers.conference_id = conferences.conference_id 
        JOIN topics ON papers.topic_id = topics.topic_id 
        WHERE papers.paper_id = ?
    ";

    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $paperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $paper = $result->fetch_assoc();
    $stmt->close();
}

?>

<div class="container">
    <h1><?php echo htmlspecialchars($paper['title']); ?></h1>

    <div class="paper-details">
        <form method="post" action="">
            <?php if (isAdmin()) : ?>
                <div class="mb-3">
                    <label for="author_string_list" class="form-label">Danh sách tác giả:</label>
                    <input type="text" class="form-control" id="author_string_list" name="author_string_list" value="<?php echo htmlspecialchars($paper['author_string_list']); ?>">
                </div>
            <?php else : ?>
                <div class="mb-3">
                    <label for="author_string_list" class="form-label">Danh sách tác giả:</label>
                    <input type="text" class="form-control" id="author_string_list" name="author_string_list" value="<?php echo htmlspecialchars($paper['author_string_list']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary" name="addSelf">Thêm bản thân vào danh sách tác giả</button>
                </div>
            <?php endif; ?>
            <?php if (isAdmin()) : ?>
                <button type="submit" class="btn btn-primary" name="updateAuthors">Cập nhật danh sách tác giả</button>
            <?php endif; ?>
        </form>

        <p><strong>Tóm tắt:</strong> <?php echo htmlspecialchars($paper['abstract']); ?></p>
        <h3>Thông tin hội nghị</h3>
        <p><strong>Tên hội nghị:</strong> <?php echo htmlspecialchars($paper['conference_name']); ?></p>
        <p><strong>Viết tắt:</strong> <?php echo htmlspecialchars($paper['abbreviation']); ?></p>
        <p><strong>Ngày bắt đầu:</strong> <?php echo htmlspecialchars($paper['start_date']); ?></p>
        <p><strong>Ngày kết thúc:</strong> <?php echo htmlspecialchars($paper['end_date']); ?></p>
        <p><strong>Loại:</strong> <?php echo htmlspecialchars($paper['type']); ?></p>
        <h3>Thông tin chủ đề</h3>
        <p><strong>Tên chủ đề:</strong> <?php echo htmlspecialchars($paper['topic_name']); ?></p>
    </div>

    <a href="index.php">Quay lại trang chủ</a>
</div>

<?php
// Handle form submission to add self to author_string_list
if (isset($_POST['addSelf']) && !isAdmin()) {
    $userId = $_SESSION['id'];

    // Fetch user's full name
    $query = "SELECT full_name FROM authors WHERE user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $userName = $user['full_name'];
        $currentAuthors = explode(', ', $paper['author_string_list']);

        if (!in_array($userName, $currentAuthors)) {
            $currentAuthors[] = $userName;
            $updatedAuthors = implode(', ', $currentAuthors);

            // Update paper's author_string_list
            $query = "UPDATE papers SET author_string_list = ? WHERE paper_id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("si", $updatedAuthors, $paperId);
            if ($stmt->execute()) {
                echo "Author list updated successfully.";
            } else {
                echo "Error updating author list: " . $stmt->error;
            }
            $stmt->close();

            // Refresh paper details after update
            $query = "
                SELECT papers.*, conferences.name AS conference_name, conferences.abbreviation, conferences.start_date, conferences.end_date, conferences.type, topics.topic_name 
                FROM papers 
                JOIN conferences ON papers.conference_id = conferences.conference_id 
                JOIN topics ON papers.topic_id = topics.topic_id 
                WHERE papers.paper_id = ?
            ";

            $stmt = $connection->prepare($query);
            $stmt->bind_param("i", $paperId);
            $stmt->execute();
            $result = $stmt->get_result();
            $paper = $result->fetch_assoc();
            $stmt->close();
        } else {
            echo "You are already listed as an author.";
        }
    } else {
        echo "User not found.";
    }
}
?>