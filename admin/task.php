<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Tasks Manage Tools</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php
require_once '.././include/DbHandler.php';
require_once '.././include/Functions.php';
$db = new DbHandler ();
include_once '.././admin/header.php';

// Do update
if (isset ( $_GET ['action'] )) {
	if ($_GET ['action'] == "update" && isset ( $_POST ['id'] )) {
		$id = $_POST ['id'];
		$user_id = $_POST ['user_id'];
		$task = $_POST ['task'];
		$project_id = $_POST['project_id'];
		if ($db->updateTask ( $user_id, $id, $task, 0 )) {
			echo 'UPDATE Success';
			header ( "Refresh:1; url=task.php" );
		} else
			echo 'UPDATE fail';
	}
}

$id = null;
if (isset ( $_GET ['id'] ))
	$id = $_GET ['id'];

$func = new Functions ();
$tasks = $db->getAllTasks ();
if (! $id) {
	?>

	<h2>Create New Task</h2>
	<form action="/tasks/v1/tasks" method="post">
		Task:
		<textarea name="task" id="task" rows="20" cols="20"></textarea>
		<br /> User : <select name="user_id">
			<?php
	$users = $db->getAllUsers ();
	while ( $user = $users->fetch_assoc () ) {
		echo '<option value="' . $user ['id'] . '">' . $user ['name'] . '</option>';
	}
	?>
		</select> <br />Project: <select name="project_id">
		
		<?php
	$projects = $db->getAllProjects ();
	while ( $project = $projects->fetch_assoc () ) {
		echo '<option value="' . $project ['id'] . '">' . $project ['project_name'] . '</option>';
	}
	?>
		</select> <br /> <input type="submit" value="Create" />
	</form>
	<hr>
	<div id="page-wrap">
		<table>
			<tr>
				<th>Task ID</th>
				<th>Task Content</th>
				<th>Status</th>
				<th>Create At</th>
				<th>Developer</th>
				<th>Project</th>
				<th></th>
			</tr>
			<?php
	while ( $task = $tasks->fetch_assoc () ) {
		$user = $db->getUserByID ( $task ['user_id'] );
		$project =$db->getProjectById($task ['project_id']);
		echo '<tr>';
		echo '<td>' . $task ['id'] . '</td>';
		echo '<td>' . $task ['task'] . '</td>';
		echo '<td>' . $func->printTaskStatus ( $task ['status'] ) . '</td>';
		echo '<td>' . $task ['created_at'] . '</td>';
		echo '<td>' . $user ['name'] . '</td>';
		echo '<td>' . $project['project_name'] . '</td>';
		echo '<td><a href="?id=' . $task ['id'] . '">Edit</a></td>';
		echo '</tr>';
	}
	?>
		</table>
	</div>
	
	<?php
} else {
	
	// fetch category
	$result = $db->getTaskByID ( $id );
	?>
	<h2>Update Task</h2>
	<form action="?action=update" method="POST">
		<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />
		Task:
		<textarea name="task" id="task" rows="20" cols="20"><?php echo $result['task']?></textarea>
		<br /> User : <select name="user_id">
			<?php
	$users = $db->getAllUsers ();
	while ( $user = $users->fetch_assoc () ) {
		echo '<option value="' . $user ['id'] . '">' . $user ['name'] . '</option>';
	}
	?> </select><br />Project: <select name="project_id">
		
		<?php
	$projects = $db->getAllProjects ();
	while ( $project = $projects->fetch_assoc () ) {
		echo '<option value="' . $project ['id'] . '">' . $project ['project_name'] . '</option>';
	}
	?>
		</select> <br /> <input type="submit" value="Save" />

	</form>
	<?php
}

?>
</body>
</html>