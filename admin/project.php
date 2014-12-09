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
		$project_name = $_POST ['project_name'];
		$description = $_POST ['description'];
		if ($db-> updateProject($id, $project_name, $description)) {
			echo 'UPDATE Success';
			header ( "Refresh:1; url=project.php" );
		} else
			echo 'UPDATE fail';
	}
}

// Do insert
if( isset($_POST['project_name'])){
	$project_name = $_POST ['project_name'];
	$description = $_POST ['description'];
	if($db -> createProject($project_name, $description)){
			echo 'Add Success';
			header ( "Refresh:1; url=project.php" );
		} else
			echo 'Add fail';
}

$id = null;
if (isset ( $_GET ['id'] ))
	$id = $_GET ['id'];

$func = new Functions ();
$projects = $db->getAllProjects ();
if (! $id) {
	?>

	<h2>Create New Project</h2>
	<form action="#" method="post">
	Project Name: <input type="text" id="project_name" name="project_name" />
	<br />
		Description:
		<textarea name="description" id="description" rows="20" cols="20"></textarea>
		 <br /> <input type="submit" value="Create" />
	</form>
	<hr>
	<div id="page-wrap">
		<table>
			<tr>
				<th>Project ID</th>
				<th>Project Name</th>
				<th>Description</th>
				<th></th>
			</tr>
			<?php
	while ( $project = $projects->fetch_assoc () ) {
		echo '<tr>';
		echo '<td>' . $project ['id'] . '</td>';
		echo '<td>' . $project ['project_name'] . '</td>';
		echo '<td>' . $project ['description'] . '</td>';
		echo '<td><a href="?id=' . $project ['id'] . '">Edit</a></td>';
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
	<h2>Update User</h2>
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
	?> </select> <br /> <input type="submit" value="Save" />

	</form>
	<?php
}

?>
</body>
</html>