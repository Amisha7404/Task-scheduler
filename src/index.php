<?php
require_once 'functions.php';

// TODO: Implement the task scheduler, email form and logic for email registration.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Task
    if (isset($_POST['task-name'])) {
        $task_name = trim($_POST['task-name']);
        if ($task_name !== '') {
            addTask($task_name);
        }
    }
	if (isset($_POST['email-subscribe'])) {
        $email = trim($_POST['email-subscribe']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            subscribeEmail($email);
        }
    }
	if (isset($_POST['toggle-task']) && isset($_POST['task-id'])) {
        $id = (int)$_POST['task-id'];
        $status = $_POST['toggle-task'] === 'on' ? 1 : 0;
        markTaskAsCompleted($id, $status);
    }
if (isset($_POST['delete-task']) && isset($_POST['task-id'])) {
        deleteTask((int)$_POST['task-id']);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
$tasks = getAllTasks();

// In HTML, you can add desired wrapper `<div>` elements or other elements to style the page. Just ensure that the following elements retain their provided IDs.

?>
<!DOCTYPE html>
<html>

<head>
	<!-- Implement Header !-->
	 <title>Task Manager</title>
</head>

<body>
	<h2>Add Task</h2>
	<!-- Add Task Form -->
	<form method="POST">
		<!-- Implement Form !-->
		<input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
		<button type="submit" id="add-task">Add Task</button>
	</form>

	<!-- Tasks List -->
	 <h2>Task List</h2>
	<ul id="tasks-list">
		<!-- Implement Tasks List (Your task item must have below
		provided elements you can modify there position, wrap them
		in another container, or add styles but they must contain
		specified classnames and input type )!-->
		<?php foreach ($tasks as $task): ?>
		<input type="email" name="email" required />
		<button id="submit-email">Submit</button>
		<li class="task-item">
			<form method="POST" style="display:inline;">
			<input type="hidden" name="task-id" value="<?= $task[0] ?>">
			<input type="checkbox" class="task-status" name="toggle-task" <?= $task[2] ? 'checked' : '' ?> onchange="this.form.submit()">
			</form>
			<?= htmlspecialchars($task[1]) ?>
			<form method="POST" style="display:inline;">
                <input type="hidden" name="task-id" value="<?= $task[0] ?>">
			<button class="delete-task">Delete</button>
			</form>
		</li>
		 <?php endforeach; ?>	
	</ul>

	<!-- Subscription Form -->
	 <h2>Subscribe to Email Reminders</h2>
	<form method="POST" >
		<!-- Implement Form !-->
		 <input type="email" name="email-subscribe" required>
		<button type="submit" id="submit-email">Subscribe</button>
	</form>

</body>

</html>