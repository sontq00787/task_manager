<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Tasks Manage Tools</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href=".././libs/tinyeditor/tinyeditor.css">
<script type='text/javascript'
	src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
<script type="text/javascript"
	src=".././libs/tinyeditor/tiny.editor.packed.js"></script>
</head>
<body>
	<script type="text/javascript">
	$(function(){
	    $(".accepted").click(function(){
	        var id = $(this).attr("id");
	        var dataString = 'id='+ id;
	        $.ajax({
	        	type: "POST",
	        	url: "action.php",
	        	data: dataString,
	        	cache: true,
	        	success: function(html){
	            	if(html=='Success'){
						document.getElementById("status"+id).innerHTML ='<font color="gray"><b>Close</b></font>';
			        	$(".tool"+id).hide();
	        		}else
	            		alert(html);
	        	}  
	        	});
	    });
	});
	function showErrorMessage(msg){
		alert(msg);
	}
</script>

<?php
$id = null;
require_once '.././include/DbHandler.php';
require_once '.././include/Functions.php';
$db = new DbHandler ();
include_once '.././mteam/header.php';
if (! isset ( $_SESSION ['user_id'] ))
	header ( "Refresh:0;url=login.php" );
if (isset ( $_GET ['id'] ))
	$id = $_GET ['id'];

$func = new Functions ();
$user_id = $_SESSION ['user_id'];
$tasks = $db->getAllUserTasks ( $user_id );
if (isset ( $_POST ['status'] )) {
	$status = $_POST ['status'];
	if ($db->updateTaskStatus ( $id, $status )) {
		echo 'Update success';
		header ( "Refresh:1" );
	} 

	else
		echo 'Update fail';
}

?>

 
 <div id="page-wrap">
		<table>
			<tr>
				<th>Task ID</th>
				<th>Task Content</th>
				<th>Status</th>
				<th>Create At</th>
				<th></th>
			</tr>
			<?php
			while ( $task = $tasks->fetch_assoc () ) {
				echo '<tr>';
				echo '<td>' . $task ['id'] . '</td>';
				echo '<td>' . $task ['task'] . '</td>';
				echo '<td>' . $func->printTaskStatus ( $task ['status'] ) . '</td>';
				echo '<td>' . $task ['created_at'] . '</td>';
				echo '<td><a href="?id=' . $task ['id'] . '">Edit</a></td>';
				echo '</tr>';
			}
			?>
		</table>
	</div>
	<hr>

<?php
if (! $id) {
	?>

 
	<h2>Click Edit To Update A Task</h2>
	<?php
} else {
	// fetch category
	$result = $db->getTask ( $id, $user_id );
	?>
	<h2>Update Task</h2>
	<form action="#" method="POST">
		<input type="hidden" name="email" id="email" value="<?php echo $id ?>" /> Content: 
			<?php echo $result['task']?>
			<br /> Status:<select name="status">
			<?php
	
	if ($result ['status'] == 0)
		echo '<option value="0" selected="selected">Not Start</option>';
	else
		echo '<option value="0">Not Start</option>';
	if ($result ['status'] == 1)
		echo '<option value="1" selected="selected">In Progress</option>';
	else
		echo '<option value="1">In Progress</option>';
	if ($result ['status'] == 2)
		echo '<option value="2" selected="selected">Completed</option>';
	else
		echo '<option value="2">Completed</option>';
	?>
<!-- 			<option value="0" >Not Start</option> -->
			<!-- 			<option value="1">In Progress</option> -->
			<!-- 			<option value="2">Completed</option> -->
		</select> <br /> <br /> <input type="submit" value="Save" />
	</form>
	<?php
}
?>
<hr>
<?php
$qid = null;
$ques = null;
if (isset ( $_GET ['qid'] )) {
	$qid = $_GET ['qid'];
	$ques = $db->getQAById ( $qid );
}
?>

	<h2>Post a question</h2>
	<div id="page-wrap">
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST"
			onsubmit="editor.post()">

			Title: <br /> <input type="text" name="title" id="title"
				value="<?php if($qid) echo $ques['title']; else echo 'None' ?>"
				size="100" /><br /> Content: <br />
			<textarea id="tinyeditor" style="width: 400px; height: 200px"
				name="content"><?php if($qid) echo $ques['content']; else echo '' ?></textarea>
			<br /> <br /> <br /> Project: <select name="project_id">
		
		<?php
		$projects = $db->getAllProjects ();
		while ( $project = $projects->fetch_assoc () ) {
			echo '<option value="' . $project ['id'] . '">' . $project ['project_name'] . '</option>';
		}
		?>
		</select><br /> <input type="submit" value="Post" />
		</form>
	</div>

	<h2>My Questions</h2>
	<div id="page-wrap">
		<table>
			<tr>
				<th>Ques ID</th>
				<th>Content</th>
				<th>Create At</th>
				<th>Project</th>
				<th>Status</th>
				<th></th>

			</tr>
			<?php
			$qas = $db->getQAByUser ( $user_id );
			if ($user_id == 1)
				$qas = $db->getAllQAs ();
			while ( $q = $qas->fetch_assoc () ) {
				echo '<tr>';
				echo '<td>' . $q ['id'] . '</td>';
				echo '<td>' . $q ['content'] . '</td>';
				echo '<td>' . $q ['created_at'] . '</td>';
				echo '<td><b>' . $db->getProjectById ( $q ['project_id'] )['project_name'] . '</b></td>';
				echo '<td id="status' . $q ['id'] . '">' . $func->printQAStatus ( $q ['status'] ) . '</td>';
				echo '<td><a href="?qid=' . $q ['id'] . '">Edit</a></td>';
				if ($user_id == $q ['user_id'] && $q ['status'] != 2) {
					echo '<td class="tool' . $q ['id'] . '"> <button id="' . $q ['id'] . '" name="accepted" class="accepted">Accepted</button></td>';
				}
				
				echo '</tr>';
			}
			?>
		</table>
	</div>
	
	
	<?php
	if (isset ( $_POST ['content'] )) {
		$content = $_POST ['content'];
		$title = $_POST ['title'];
		$project_id = $_POST ['project_id'];
		if (! $qid) {
			if ($db->createQA ( $title, $content, $project_id, $user_id )) {
				echo 'Post success';
				header ( "Refresh:1" );
			} else
				echo 'Post fail';
		} else {
			if ($user_id == $ques ['user_id'] || $user_id == 1) {
				if ($db->updateQA ( $qid, $title, $content, 1, $project_id )) {
					header ( "Refresh:0" );
					echo '<script type="text/javascript">', 'showErrorMessage("Update success");', '</script>';
				} else
					echo 'Update fail';
			} else {
				echo '<script type="text/javascript">', 'showErrorMessage("You can\'t edit another user\'s question");', '</script>';
			}
		}
	}
	
	?>
	<script>
	//http://www.scriptiny.com/2010/02/javascript-wysiwyg-editor/
	var editor = new TINY.editor.edit('editor', {
	id: 'tinyeditor',
	width: 584,
	height: 175,
	cssclass: 'tinyeditor',
	controlclass: 'tinyeditor-control',
	rowclass: 'tinyeditor-header',
	dividerclass: 'tinyeditor-divider',
	controls: ['bold', 'italic', 'underline', 'strikethrough', '|', 'subscript', 'superscript', '|',
		'orderedlist', 'unorderedlist', '|', 'outdent', 'indent', '|', 'leftalign',
		'centeralign', 'rightalign', 'blockjustify', '|', 'unformat', '|', 'undo', 'redo', 'n',
		'font', 'size', 'style', '|', 'image', 'hr', 'link', 'unlink', '|', 'print'],
	footer: true,
	fonts: ['Verdana','Arial','Georgia','Trebuchet MS'],
	xhtml: true,
	cssfile: 'custom.css',
	bodyid: 'editor',
	footerclass: 'tinyeditor-footer',
	toggle: {text: 'source', activetext: 'wysiwyg', cssclass: 'toggle'},
	resize: {cssclass: 'resize'}
});
	</script>
</body>
</html>