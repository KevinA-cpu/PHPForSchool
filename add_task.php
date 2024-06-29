<?php
include 'base.php';
include 'database.php';
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

  <h1>Add New Task</h1>
  <form id="addTaskForm" onsubmit="return validateForm()" method="post" action="">
    <div class="mb-3">
      <label for="taskName" class="form-label">Task Name:</label>
      <input type="text" class="form-control" id="taskName" name="taskName">
    </div>
    <div class="mb-3">
      <label for="taskDescription" class="form-label">Task Description:</label>
      <textarea class="form-control" id="taskDescription" name="taskDescription"></textarea>
    </div>
    <div class="mb-3">
      <label for="startDate" class="form-label">Start Date:</label>
      <input type="date" class="form-control" id="startDate" name="startDate">
    </div>
    <div class="mb-3">
      <label for="dueDate" class="form-label">Due Date:</label>
      <input type="date" class="form-control" id="dueDate" name="dueDate">
    </div>
    <div class="mb-3">
      <label for="categoryId" class="form-label">Category:</label>
      <select class="form-control" id="categoryId" name="categoryId">
        <?php
        $result = $connection->query("SELECT id, name FROM Category");
        while ($row = $result->fetch_assoc()) {
          echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
        }
        ?>
      </select>
    </div>
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

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $taskName = htmlspecialchars($_POST['taskName']);
  $taskDescription = htmlspecialchars($_POST['taskDescription']);
  $startDate = htmlspecialchars($_POST['startDate']);
  $dueDate = htmlspecialchars($_POST['dueDate']);
  $categoryId = filter_var($_POST['categoryId'], FILTER_SANITIZE_NUMBER_INT);

  // Prepare an SQL statement
  $stmt = $connection->prepare("INSERT INTO TASK (name, description, start_date, due_date, category_id) VALUES (?, ?, ?, ?, ?)");

  // Bind variables to the prepared statement as parameters
  $stmt->bind_param("ssssi", $taskName, $taskDescription, $startDate, $dueDate, $categoryId);

  // Attempt to execute the prepared statement
  if ($stmt->execute()) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $stmt->error;
  }

  // Close statement
  $stmt->close();
}
mysqli_close($connection);
?>