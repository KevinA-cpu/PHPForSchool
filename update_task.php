<?php
include 'base.php';
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $connection->prepare("SELECT * FROM TASK WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $taskName = htmlspecialchars($_POST['taskName']);
    $taskDescription = htmlspecialchars($_POST['taskDescription']);
    $startDate = htmlspecialchars($_POST['startDate']);
    $dueDate = htmlspecialchars($_POST['dueDate']);
    $categoryId = filter_var($_POST['categoryId'], FILTER_SANITIZE_NUMBER_INT);

    // Prepare an SQL statement
    $stmt = $connection->prepare("UPDATE TASK SET name = ?, description = ?, start_date = ?, due_date = ?, category_id = ? WHERE id = ?");

    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("ssssii", $taskName, $taskDescription, $startDate, $dueDate, $categoryId, $id);

    $stmt->execute();

    // Close statement
    $stmt->close();

    // Redirect back to view_tasks.php
    header("Location: view_tasks.php");

    mysqli_close($connection);
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
            All fields are required!
        </div>
    </div>

    <h1>Update Task</h1>
    <form id="updateTaskForm" onsubmit="return validateForm()" method="post" action="">
        <div class="mb-3">
            <label for="taskName" class="form-label">Task Name:</label>
            <input type="text" class="form-control" id="taskName" name="taskName" value="<?php echo $task['name']; ?>">
        </div>
        <div class="mb-3">
            <label for="taskDescription" class="form-label">Task Description:</label>
            <textarea class="form-control" id="taskDescription" name="taskDescription"><?php echo $task['description']; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="startDate" class="form-label">Start Date:</label>
            <input type="date" class="form-control" id="startDate" name="startDate" value="<?php echo date('Y-m-d', strtotime($task['start_date'])); ?>">
        </div>
        <div class="mb-3">
            <label for="dueDate" class="form-label">Due Date:</label>
            <input type="date" class="form-control" id="dueDate" name="dueDate" value="<?php echo date('Y-m-d', strtotime($task['due_date'])); ?>">
        </div>
        <div class="mb-3">
            <label for="categoryId" class="form-label">Category:</label>
            <select class="form-control" id="categoryId" name="categoryId">
                <?php
                $result = $connection->query("SELECT id, name FROM Category");
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['id'] . '"' . ($task['category_id'] == $row['id'] ? ' selected' : '') . '>' . $row['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <script>
        var toastEl = document.getElementById('validationToast');
        var toast = new bootstrap.Toast(toastEl);

        document.querySelector('.close').addEventListener('click', function() {
            toast.hide();
        });
    </script>

    <script>
        function validateForm() {
            var taskName = document.getElementById('taskName').value;
            var taskDescription = document.getElementById('taskDescription').value;
            var startDate = document.getElementById('startDate').value;
            var dueDate = document.getElementById('dueDate').value;
            var categoryId = document.getElementById('categoryId').value;

            if (!taskName || !taskDescription || !startDate || !dueDate || !categoryId) {
                var toastEl = document.getElementById('validationToast');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
                return false;
            }

            return true;
        }
    </script>
</div>