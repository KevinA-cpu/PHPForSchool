<?php
include 'database.php';

$keyword = isset($_POST['keyword']) ? '%' . $_POST['keyword'] . '%' : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = 2;
$offset = ($page - 1) * $limit;

$query = "
    SELECT papers.*, conferences.name AS conference_name, conferences.abbreviation, conferences.start_date, conferences.end_date, conferences.type 
    FROM papers 
    JOIN conferences ON papers.conference_id = conferences.conference_id 
    WHERE papers.title LIKE ? 
    OR papers.author_string_list LIKE ? 
    OR conferences.name LIKE ? 
    OR conferences.abbreviation LIKE ? 
    OR conferences.start_date LIKE ? 
    OR conferences.end_date LIKE ? 
    OR conferences.type LIKE ?
    LIMIT ? OFFSET ?
";

$stmt = $connection->prepare($query);
$stmt->bind_param("ssssssssi", $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$total_query = "
    SELECT COUNT(*) AS total
    FROM papers 
    JOIN conferences ON papers.conference_id = conferences.conference_id 
    WHERE papers.title LIKE ? 
    OR papers.author_string_list LIKE ? 
    OR conferences.name LIKE ? 
    OR conferences.abbreviation LIKE ? 
    OR conferences.start_date LIKE ? 
    OR conferences.end_date LIKE ? 
    OR conferences.type LIKE ?
";
$total_stmt = $connection->prepare($total_query);
$total_stmt->bind_param("sssssss", $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);

echo '<table class="table">';
echo '<thead><tr><th>Tên bài báo</th><th>Tác giả</th><th>Tóm tắt</th><th>Hành động</th></tr></thead><tbody>';

while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $row['title'] . '</td>';

    // Split author_string_list into individual author names
    $authors = explode(', ', $row['author_string_list']);

    echo '<td>';
    foreach ($authors as $authorName) {
        echo '<a href="details_author.php?name=' . urlencode($authorName) . '">' . htmlspecialchars($authorName) . '</a>, ';
    }
    echo '</td>';

    echo '<td>' . $row['abstract'] . '</td>';
    echo '<td><a href="details_baibao.php?id=' . $row['paper_id'] . '">View</a></td>';
    echo '</tr>';
}

echo '</tbody></table>';

if ($total_pages > 1) {
    echo '<nav><ul class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<li class="page-item"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
    }
    echo '</ul></nav>';
}

$stmt->close();
$total_stmt->close();
