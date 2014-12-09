<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href=".././libs/tinyeditor/tinyeditor.css">
<script type='text/javascript'
	src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
<script type="text/javascript"
	src=".././libs/tinyeditor/tiny.editor.packed.js"></script>
</head>
<body>
<?php
require_once '.././include/DbHandler.php';
// require_once '.././include/Functions.php';
$db = new DbHandler ();
// $func = new Functions ();
// $refs = $db->getAllRefs ();
include_once '.././mteam/header.php';

if (isset ( $_POST ['url'] )) {
	$user_id = $_POST ['user_id'];
	$task = $_POST ['task'];
	$project_id = $_POST ['project_id'];
	if ($db->updateTask ( $user_id, $id, $task, 0 )) {
		echo 'UPDATE Success';
		header ( "Refresh:1; url=task.php" );
	} else
		echo 'UPDATE fail';
	if ($db->createRef ( $url, $des )) {
		header ( "Refresh:0" );
	}
}

?>
 <script type="text/javascript">
    		document.title = "Register a Task";
		</script>
	<div id="page-wrap">
<?php if($user_id) { ?>
		<h2>Add a tasks</h2>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST"
			onsubmit="editor.post()">
		Task:
		<textarea name="task" id="task" id="tinyeditor" style="width: 400px; height: 200px"></textarea>
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
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST"
			onsubmit="editor.post()">

			Url: <br /> <input type="text" name="url" id="url" value=""
				size="100" /><br /> Content: <br />
			<textarea id="tinyeditor" style="width: 400px; height: 200px"
				name="description"></textarea>
			<br /> <input type="submit" value="Add" />
		</form>
<?php }?>
	</div>
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