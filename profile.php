<?php
include 'base.php';
include 'database.php';

if (isset($_GET['id'])) {
    $authorId = $_GET['id'];

    // Retrieve author information
    $query = "SELECT * FROM authors WHERE user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $authorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $author = $result->fetch_assoc();
    $stmt->close();

    if (!$author) {
        echo "Author not found.";
        exit();
    }

    // Retrieve papers authored by the user sorted by conference start_date
    $queryPapers = "
        SELECT papers.*, conferences.start_date
        FROM papers
        JOIN conferences ON papers.conference_id = conferences.conference_id
        WHERE papers.user_id = ?
        ORDER BY conferences.start_date DESC
    ";

    $stmtPapers = $connection->prepare($queryPapers);
    $stmtPapers->bind_param("i", $authorId);
    $stmtPapers->execute();
    $resultPapers = $stmtPapers->get_result();
} else {
    echo "Author ID not provided.";
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

    <div>
        <a href="update_profile.php?id=<?php echo $authorId; ?>" class="btn btn-primary">Cập nhật hồ sơ</a>
        <a href="create_paper.php?id=<?php echo $authorId; ?>" class="btn btn-primary">Tạo bài báo</a>
    </div>

    <h2>Bài báo của tác giả</h2>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Tên bài báo</th>
                <th scope="col">Tóm tắt</th>
                <th scope="col">Ngày bắt đầu</th>
                <th scope="col">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $resultPapers->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['title']) . '</td>';
                echo '<td>' . htmlspecialchars($row['abstract']) . '</td>';
                echo '<td>' . htmlspecialchars($row['start_date']) . '</td>';
                echo '<td><a href="details_baibao.php?id=' . $row['paper_id'] . '">Xem</a></td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    <a href="index.php">Quay trở lại trang chủ</a>
</div>