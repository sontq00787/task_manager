<?php
if (isset($_FILES ["jsonfile"]) ) {
	require_once '.././include/Functions.php';
	$func = new Functions ();
	$filename = $func->uploadFile ();
	if (strpos ( $filename, 'Error' ) === false) {
		
		$url = "http://192.168.100.110:8080/tasks/mteam/json/" . $filename;
		echo 'Upload success, url for test: <a href="' . $url . '">' . $url . '</a>';
	}else
		echo  $filename.$_FILES["jsonfile"]["type"];
} else {
	header("Refresh:0;Location=upload.php");
}

?>