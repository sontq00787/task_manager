<?php
if (isset ( $_POST ['id'] )) {
	require_once '.././include/DbHandler.php';
	$db = new DbHandler ();
	$id = $_POST ['id'];
	if ($db->updateQAStatus ( $id, 2 ))
		echo 'Success';
	else
		echo 'Fail';
}
if (isset ( $_GET ['do'] )) {
	$action = $_GET ['do'];
	if ($action == 'getanswer') {
		require_once '.././include/DbHandler.php';
		$db = new DbHandler ();
		$q = $db->getQAById ( $_GET ['id'] );
		echo $q ['answer'];
	} else {
		?>
<html>
<form method="POST" action="qa.php">
	<textarea rows="20" cols="50" name="answer"></textarea>
	<input type="hidden" name="q_id" value="<?php echo $_GET['id']; ?>"> <input
		type="submit" value="Add" />
</form>
</html>
<?php
	}
}
?>