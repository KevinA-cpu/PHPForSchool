<?php
include 'base.php';
include 'database.php';
?>

<div class="container">
    <h1>Danh sách bài báo</h1>

    <form method="POST" action="">
        <div class="sidebar">
            <h2>Lọc theo chủ đề</h2>
            <?php
            $stmt = $connection->prepare("SELECT * FROM topics");
            $stmt->execute();
            $result = $stmt->get_result();
            // Loop through topics and display checkboxes
            while ($row = $result->fetch_assoc()) {
                echo '<label style="margin-right: 10px; margin-bottom: 20px;">';
                echo '<input type="checkbox" name="selectedTopics[]" value="' . $row['topic_id'] . '"> ';
                echo $row['topic_name'];
                echo '</label>';
            }
            $stmt->close();
            ?>
            <button class="btn btn-primary" type="submit" name="searchForm">Lọc</button>
        </div>
    </form>

    <table class="table">
        <tbody>
            <?php

            if (isset($_POST['searchForm'])) {
                if (!empty($_POST['selectedTopics'])) {
                    $selectedTopics = $_POST['selectedTopics'];

                    foreach ($selectedTopics as $topicId) {
                        // Prepare the statement to get the top 5 papers for each topic
                        $stmt = $connection->prepare("SELECT * FROM papers  JOIN conferences ON papers.conference_id = conferences.conference_id  WHERE papers.topic_id = ? ORDER BY conferences.start_date DESC LIMIT 5");
                        $stmt->bind_param("i", $topicId);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        echo "<h3 style='margin-bottom: 15px;'>Top 5 papers for Topic ID: $topicId</h3>";
                        echo '<table style="margin-bottom: 30px;">';
                        echo '<tr> <th scope="col">Tên bài báo</th>
                <th scope="col">Tác giả</th>
                <th scope="col">Tóm tắt</th>
                <th scope="col">Hành động</th></tr>';

                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['title'] . '</td>';

                            // Split author_string_list into individual author names
                            $authors = explode(
                                ', ',
                                $row['author_string_list']
                            );

                            echo '<td>';
                            foreach ($authors as $authorName) {
                                echo '<a href="details_author.php?name=' . urlencode($authorName) . '">' . htmlspecialchars($authorName) . '</a>, ';
                            }
                            echo '</td>';

                            echo '<td>' . $row['abstract'] . '</td>';
                            echo '<td><a href="details_baibao.php?id=' . $row['paper_id'] . '">View</a></td>';
                            echo '</tr>';
                        }

                        echo '</table>';

                        $stmt->close();
                    }
                } else {
                    // Display all papers if no topics are selected
                    $stmt = $connection->prepare("SELECT * FROM papers");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    echo '<table>';
                    echo '<tr> <th scope="col">Tên bài báo</th>
                <th scope="col">Tác giả</th>
                <th scope="col">Tóm tắt</th>
                <th scope="col">Hành động</th></tr>';

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

                    echo '</table>';

                    $stmt->close();
                }
            } else {
                // Display all papers if no topics are selected
                $stmt = $connection->prepare("SELECT * FROM papers");
                $stmt->execute();
                $result = $stmt->get_result();

                echo '<table>';
                echo '<tr> <th scope="col">Tên bài báo</th>
                <th scope="col">Tác giả</th>
                <th scope="col">Tóm tắt</th>
                <th scope="col">Hành động</th></tr>';

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

                echo '</table>';

                $stmt->close();
            }
            ?>
        </tbody>
    </table>

</div>