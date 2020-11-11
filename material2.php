<?php $title = 'Materials List'; 
include "checkuser.php";
include "libraries3.php";

echo '<html>
<header>
<title>'.$title.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</header>
<body>';
include('connect.php');
$material_fields = [
	'cas' => ['name' => 'CAS Number', 'type' => 'text'],
	'name' => ['name' => 'Material Name', 'type' => 'text'],
	'density' => ['name' => 'Density', 'type' => 'number', 'step' => 0.01],
	'mw' => ['name' => 'Mw', 'type' => 'number', 'step' => 0.01],
];
$sql = "SELECT * FROM `materials` ORDER BY `type` DESC, `id` ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);    
$stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="materials-box">
	<div class="headertext"><h3>'.$title.'</h3></div>
	<button type="button" onclick=location.href=("material.php"); class="btn btn-info create-material-btn">&#10010; Add New Material</button>
	<br/><br/>';
	if ( !empty($_SESSION['post_message']) ):
		echo '<label class="label label-'.($_SESSION['post_message']['type'] == 'error' ? 'danger' : 'success').'">'.$_SESSION['post_message']['message'].'</label>';
	
	unset($_SESSION['post_message']);
	endif; 
	 if ( !empty($materials) ): 
		echo '<form id="edit-materials-form" method="post" action="edit-material.php">
			<table id="datatableid" class="table table-striped table-responsive">
				<thead>
					<tr>
						<td>ID</td>
						<td>Type</td>
						<td>CAS Number</td>
						<td>Material Name</td>
						<td>Density (kg/m3)</td>
						<td>Mw (kg/mol)</td>
						<td>Actions</td>
					</tr>
				</thead>
				<tbody>';
					foreach($materials as $mat): 
					echo '<tr data-id="'.$mat['id'].'">
						<td><input type="hidden" data-name="id" value="'.$mat['id'].'" />'.$mat['id'].'</td>
						<td>'.$mat['type'].'</td>
						<td>'.$mat['cas'].'</td>
						<td>'.$mat['name'].'</td>
						<td><input type="number" class="form-control" data-name="density" value="'.$mat['density'].'" step="0.01" readonly /></td>
						<td><input type="number" class="form-control" data-name="mw" value="'.$mat['mw'].'" step="0.01" readonly /></td>
						<td>
							<button type="button" class="btn btn-default edit-material-btn">&#9998;</button>
							<button type="button" class="btn btn-danger remove-material-btn">&#10006;</button>
						</td>
					</tr>';
					 endforeach; 
				echo '</tbody>
			</table>
		</form>';
	 endif; 
	echo '</div>
<script src="jquery-3.3.1.min.js"></script>
<script src="custom.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $("#datatableid").DataTable();
} );
</script>
</body>
</html>';
include "footer.php"; 
?>