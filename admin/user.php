<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Users Manage Tools</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php
$id = null;
if(isset($_GET['email']))
	$id = $_GET['email'];
require_once '.././include/DbHandler.php';
$db = new DbHandler ();
include_once '.././admin/header.php';
$users = $db -> getAllUsers();
if(!$id){
 ?>
 
 <div id="page-wrap">
		<table>
			<tr>
				<th>User ID</th>
				<th>User Name</th>
				<th>EMail</th>
				<th></th>
			</tr>
			<?php 
		while ( $user = $users->fetch_assoc () ) {
echo '<tr>';
echo '<td>'.$user['id'].'</td>';
echo '<td>'.$user['name'].'</td>';
echo '<td>'.$user['email'].'</td>';
echo '<td><a href="?email='.$user['email'].'">Edit</a></td>';
echo '</tr>';
}
		?>
		</table>
	</div>
	<hr>
 
	<h2>Create New User</h2>
	<form action="/tasks/v1/register" method="post">
		User Name: <input type="text" name="name"
			id="name" /> <br /> Email: <input
			type="text" name="email" id="email" />
		<br />
		Password: <input
			type="text" name="password" id="password" />
		<br /> <input type="submit" value="Create" />
	</form>
	<?php 
}else{

	
	// fetch category
	$result = $db->getUserByEmail($id);
 ?>
	<h2>Update User</h2>
	<form action="/task_manager/v1/user/update" method="POST">
		<input type="hidden" name="email" id="email"
			value="<?php echo $id ?>" /> User Name: <input type="text"
			name="name" id="name"
			value="<?php echo $result['name'] ?>" /> <br /> Password: <input type="text" name="password"
			id="password"
			value="" /> <br /> 
<!-- 			<input type="submit" value="Save" /> -->
	</form>
	<?php 
 }
 ?>
</body>
</html>