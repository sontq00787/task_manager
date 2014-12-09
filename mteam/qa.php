<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Q&amp;A Page</title>
<!-- <link rel="stylesheet" href="../css/style.css"> -->
<!-- <script type='text/javascript' -->
<!-- 	src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script> -->
<link rel="stylesheet"
	href="https://cdn.datatables.net/1.10.3/css/jquery.dataTables.css">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
<style>
blockquote {
	background: #f9f9f9;
	border-left: 10px solid #ccc;
	margin: 1.5em 10px;
	padding: 0.5em 10px;
	quotes: "\201C" "\201D" "\2018" "\2019";
}

blockquote:before {
	color: #ccc;
	content: open-quote;
	font-size: 4em;
	line-height: 0.1em;
	margin-right: 0.25em;
	vertical-align: -0.4em;
}

blockquote p {
	display: inline;
}
</style>
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

	$(function(){
	    $(".reply").click(function(){
	        var id = $(this).attr("id");
	        var dataString = 'do=addanswer&id='+id;
	        $.ajax({
	        	type: "GET",
	        	url: "action.php",
	        	data: dataString,
	        	cache: true,
	        	success: function(html){
	        		document.getElementById("answer").innerHTML=html;    	
	        	}  
	        	});
	    });
	});

	$(function(){
	    $(".answered").click(function(){
	        var id = $(this).attr("id");
	        var dataString = 'do=getanswer&id='+id;
	        $.ajax({
	        	type: "GET",
	        	url: "action.php",
	        	data: dataString,
	        	cache: true,
	        	success: function(html){
	        		document.getElementById("answer").innerHTML=html;    
	        		$("#answer").show();	
	        	}  
	        	});
	    });
	});
</script>
<?php
require_once '.././include/DbHandler.php';
require_once '.././include/Functions.php';
$db = new DbHandler ();
$func = new Functions ();
include_once '.././mteam/header.php';

if (isset ( $_POST ['answer'] )) {
	$answer = $_POST ['answer'];
	$id = $_POST ['q_id'];
	$db->updateQAAnswer ( $id, $answer );
	// echo '<script type="text/javascript">', 'alert("' . $_POST ['answer'] . '");', '</script>';
}

$qas = $db->getAllQAs ();

?>
<script type="text/javascript">
    		document.title = "Q & A Page";
</script>
	<blockquote>
		<p id="answer"></p>
	</blockquote>
	<div id="page-wrap">

		<br />
		<table id="example" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Ques ID</th>
					<th>Content</th>
					<th>Create At</th>
					<th>Project</th>
					<th>Developer</th>
					<th>Status</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Ques ID</th>
					<th>Content</th>
					<th>Create At</th>
					<th>Project</th>
					<th>Developer</th>
					<th>Status</th>
				</tr>
			</tfoot>
			<?php
			if (isset ( $_SESSION ['user_id'] )) {
				$user_id = $_SESSION ['user_id'];
			}
			while ( $q = $qas->fetch_assoc () ) {
				echo '<tr>';
				if ($q ['answer']) {
					echo '<td>' . $q ['id'] . '<button id="' . $q ['id'] . '" class="answered">Answered</button>';
				} else
					echo '<td>' . $q ['id'];
				if ($user_id == 1)
					echo '<button id="' . $q ['id'] . '" class="reply">Reply</button></td>';
				echo '<td>' . $q ['content'] . '</td>';
				echo '<td>' . $q ['created_at'] . '</td>';
				echo '<td><b>' . $db->getProjectById ( $q ['project_id'] )['project_name'] . '</b></td>';
				echo '<td>' . $db->getUserByID ( $q ['user_id'] )['name'] . '</td>';
				echo '<td id="status' . $q ['id'] . '">' . $func->printQAStatus ( $q ['status'] ) . '</td>';
				
				if ($user_id == $q ['user_id'] && $q ['status'] != 2 || $user_id == 1 && $q ['status'] != 2) {
					echo '<td class="tool' . $q ['id'] . '"> <button id="' . $q ['id'] . '" class="accepted">Accepted</button></td>';
				}
				
				echo '</tr>';
			}
			?>
			
		</table>
	</div>
	<script>
	$(document).ready(function() {
	    // Setup - add a text input to each footer cell
	    $('#example tfoot th').each( function () {
	        var title = $('#example thead th').eq( $(this).index() ).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
	 
	    // DataTable
	    var table = $('#example').DataTable();
	 
	    // Apply the search
	    table.columns().eq( 0 ).each( function ( colIdx ) {
	        $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
	            table
	                .column( colIdx )
	                .search( this.value )
	                .draw();
	        } );
	    } );
	} );
	</script>
</body>
</html>