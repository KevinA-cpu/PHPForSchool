<?php
include 'base.php';
include 'database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare an SQL statement
    $stmt = $connection->prepare("SELECT * FROM TASK WHERE id = ?");

    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("i", $id);

    // Execute the prepared statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the task from the database
    if ($row = $result->fetch_assoc()) {
        echo '<h1>' . $row['name'] . '</h1>';
        echo '<p>' . $row['description'] . '</p>';
        echo '<p>Start Date: ' . $row['start_date'] . '</p>';
        echo '<p>Due Date: ' . $row['due_date'] . '</p>';
        echo '<p>Status: ' . $row['status'] . '</p>';
        echo '<p>Category: ' . $row['category_id'] . '</p>';
    } else {
        echo 'Task not found.';
    }

    // Close statement
    $stmt->close();
} else {
    echo 'Invalid task ID.';
}

mysqli_close($connection);
