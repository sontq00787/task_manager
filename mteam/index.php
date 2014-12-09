<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="../styles/tabnavigation/css/reset.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../styles/tabnavigation/css/style.css">
<!-- 	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.3/css/jquery.dataTables.css"> -->
<script src="../styles/tabnavigation/js/modernizr.js"></script>
<!-- Modernizr -->
</head>
<body>
<?php
require_once '.././include/DbHandler.php';
require_once '.././include/Functions.php';
$db = new DbHandler ();
$func = new Functions ();
$tasks = $db->getAllTasksOrderByDate ();
include_once '.././mteam/header.php';
?>

	<div class="cd-tabs">
		<nav>
		<ul class="cd-tabs-navigation">
			<li><a data-content="all" class="selected" href="#0">All</a></li>
			<li><a data-content="all" href="#">Not Start</a></li>
			<li><a data-content="all" href="#0">In Progress</a></li>
			<li><a data-content="all" href="#0">Completed</a></li>
		</ul>
		<!-- cd-tabs-navigation --> </nav>

		<ul class="cd-tabs-content">
			<li data-content="all" class="selected">
				<div id="page-wrap">
					<input id="searchInput" size="100" value="Type To Filter" />

					<table id="dataList">
						<thead>
							<tr>
								<th>Task ID</th>
								<th>Task Content</th>
								<th>Status</th>
								<th>Create At</th>
								<th>Developer</th>
								<th>Project</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Task ID</th>
								<th>Task Content</th>
								<th>Status</th>
								<th>Create At</th>
								<th>Developer</th>
								<th>Project</th>
							</tr>
						</tfoot>
						<tbody id="fbody">
							<tr>
								<td colspan="6" class="spacerow">Today ( <?php echo date("Y-m-d")?> )</td>
							</tr>
			<?php
			$todayTasks = $db->getAllTasksToday ();
			while ( $task = $todayTasks->fetch_assoc () ) {
				$user = $db->getUserByID ( $task ['user_id'] );
				echo '<tr>';
				echo '<td>' . $task ['id'] . '</td>';
				echo '<td>' . $task ['task'] . '</td>';
				echo '<td>' . $func->printTaskStatus ( $task ['status'] ) . '</td>';
				echo '<td>' . $task ['created_at'] . '</td>';
				echo '<td>' . $user ['name'] . '</td>';
				echo '<td name="project_name"><b>' . $db->getProjectById ( $task ['project_id'] )['project_name'] . '</b></td>';
				echo '</tr>';
			}
			if (count ( $db->getAllTasksToday ()->fetch_all () ) == 0) {
				echo '<tr><td colspan="6">No task create today</td></tr>';
			}
			?>
			<tr>
								<td colspan="6" class="spacerow">Before</td>
							</tr>
			<?php
			while ( $task = $tasks->fetch_assoc () ) {
				$user = $db->getUserByID ( $task ['user_id'] );
				echo '<tr>';
				echo '<td>' . $task ['id'] . '</td>';
				echo '<td>' . $task ['task'] . '</td>';
				echo '<td name="status">' . $func->printTaskStatus ( $task ['status'] ) . '</td>';
				echo '<td>' . $task ['created_at'] . '</td>';
				echo '<td>' . $user ['name'] . '</td>';
				echo '<td><b>' . $db->getProjectById ( $task ['project_id'] )['project_name'] . '</b></td>';
				echo '</tr>';
			}
			?>
						</tbody>

					</table>
				</div>
			</li>

			<li data-content="notstart"><p>Not Start Tab content</p></li>

			<li data-content="inprogress">In Progress Tab Content</li>

			<li data-content="completed">Completed Tab Content</li>


		</ul>
		<!-- cd-tabs-content -->
	</div>
	<!-- cd-tabs -->

	<script src="../styles/tabnavigation/js/jquery-2.1.1.js"></script>
	<script src="../styles/tabnavigation/js/main.js"></script>

<!-- 	<script src="//code.jquery.com/jquery-1.11.1.min.js"></script> -->
<!-- 	<script src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script> -->

	<!-- Resource jQuery -->
	<script type="text/javascript">
	
	//https://datatables.net/examples/api/multi_filter.html
// 	$(document).ready(function() {
// 	    // Setup - add a text input to each footer cell
// 	    $('#dataList tfoot th').each( function () {
// 	        var title = $('#dataList thead th').eq( $(this).index() ).text();
// 	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
// 	    } );
	 
// 	    // DataTable
// 	    var table = $('#dataList').DataTable();
	 
	    // Apply the search
// 	    table.columns().eq( 0 ).each( function ( colIdx ) {
// 	        $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
// 	            table
// 	                .column( colIdx )
// 	                .search( this.value )
// 	                .draw();
// 	        } );
// 	    } );
// 	} );



	
$("#searchInput").keyup(function () {
    //split the current value of searchInput
    var data = this.value.split(" ");
    //create a jquery object of the rows
    var jo = $("#fbody").find("tr");
    if (this.value == "") {
        jo.show();
        return;
    }
    //hide all the rows
    jo.hide();

    //Recusively filter the jquery object to get results.
    jo.filter(function (i, v) {
        var $t = $(this);
        for (var d = 0; d < data.length; ++d) {
            if ($t.is(":contains('" + data[d] + "')")) {
                return true;
            }
        }
        return false;
    })
    //show the rows that match.
    .show();
}).focus(function () {
    this.value = "";
    $(this).css({
        "color": "black"
    });
    $(this).unbind('focus');
}).css({
    "color": "#C0C0C0"
});
</script>
</body>
</html>