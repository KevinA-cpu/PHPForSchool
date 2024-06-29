<?php
include 'base.php';
include 'database.php';

if (isset($_GET['name'])) {
    $authorName = $_GET['name'];

    $query = "
        SELECT * 
        FROM authors 
        WHERE full_name = ?
    ";

    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $authorName);
    $stmt->execute();
    $result = $stmt->get_result();
    $author = $result->fetch_assoc();
    $stmt->close();

    if (!$author) {
        echo "Author not found.";
        exit();
    }
} else {
    echo "Author name not provided.";
    exit();
}
?>

<div class="container">
    <h1><?php echo htmlspecialchars($author['full_name']); ?></h1>

    <div class="author-details">
        <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($author['website']); ?>" target="_blank"><?php echo htmlspecialchars($author['website']); ?></a></p>
        <p><strong>Image Path:</strong> <?php echo htmlspecialchars($author['image_path']); ?></p>
        <?php
        $profile = json_decode($author['profile_json_text'], true);
        if ($profile) :
        ?>
            <p><strong>Tiểu sử:</strong> <?php echo isset($profile['bio']) ? htmlspecialchars($profile['bio']) : ''; ?></p>
            <p><strong>Hướng nghiên cứu quan tâm:</strong> <?php echo isset($profile['interests']) ? htmlspecialchars($profile['interests']) : ''; ?></p>
            <p><strong>Đào tạo:</strong> <?php echo isset($profile['education']) ? htmlspecialchars($profile['education']) : ''; ?></p>
            <p><strong>Kinh nghiệm làm việc:</strong> <?php echo isset($profile['work_experience']) ? htmlspecialchars($profile['work_experience']) : ''; ?></p>
        <?php endif; ?>
    </div>

    <a href="index.php">Quay trở lại trang chủ</a>
</div>