<?php
session_start ();
ob_start ();
$user_id = null;
if (isset ( $_SESSION ['user_id'] )) {
	$user_id = $_SESSION ['user_id'];
	$user = $db->getUserByID ( $_SESSION ['user_id'] );
	$countnotstart = count ( $db->getTasksByStatus ( $_SESSION ['user_id'], 0 )->fetch_all () );
	$countinprogress = count ( $db->getTasksByStatus ( $_SESSION ['user_id'], 1 )->fetch_all () );
	$title = "Tasks Schedule";
	$count = $countnotstart + $countinprogress;
	if ($count > 0) {
		$title = "(" . $count . ") Tasks Schedule";
	}
	?>
<?php

	echo 'Welcome, <b>' . $user ['name'] . '</b>
			<br /><a href="usercp.php">Your tasks ' . ($count > 0 ? "(" . $count . ")" : "") . '</a> | <a href="index.php">Home</a>' . ($_SESSION ['user_id'] == 1 ? ' | <a href="../admin/">AdminCP</a>' : '');
} else {
	
	echo '<a href="login.php">Login</a>';
	$title = "Tasks Schedule";
}
?>
<script type="text/javascript">
	document.title = "<?=$title;?>"
</script>
<?php
echo ' | <a href="qa.php">Q&A</a>  | <a href="ref.php">References</a>';
?>