<?php
session_start();
if(isset( $_SESSION['user_id'])){
	$user = $db->getUserByID($_SESSION['user_id']);
	$name = $user['name'];
	if($name != 'sontq')
		header("Refresh:0;url=login.php");
	echo '<a href="../mteam/">Home</a>';
}else{
	header("Refresh:0;url=login.php");
}
?>