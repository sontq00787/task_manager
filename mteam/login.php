<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Login Page</title>
</head>
<body>

<?php
if (isset ( $_POST ['email'] )) {
	require_once '.././include/DbHandler.php';
	$db = new DbHandler ();
	$email = $_POST ['email'];
	$password = $_POST ['password'];
	if ($db->checkLogin ( $email, $password )) {
		session_start ();
		$user = $db->getUserByEmail ( $email );
		$_SESSION ['user_id'] = $user['id']; 
		echo 'Login Success';
		header ( "Refresh:1; url=usercp.php" );
	} else
		echo 'Password or email is not correct, please try again';
}
?>
<form action="#" method="POST">
		<table>
			<tr>
				<td>Email</td>
				<td><input type="text" name="email" id="email" /></tr>

			
						<tr>
				<td>Password</td>
				<td><input type="password" name="password" id="password" /> </td>
			</tr>
			<tr>
			<td></td>
				<td><input type="submit" value="Login" /> </td>
			</tr>
		</table>
		</form>
</body>
</html>