<?php $title = 'Add New Material'; 
include "checkuser.php";
include "libraries3.php";

echo '<html>
<header>
<title>'.$title.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</header>
<body>';

include('connect.php');
$base_table = 'materials';
$material_fields = [
	'cas' => ['name' => 'CAS Number', 'type' => 'text'],
	'name' => ['name' => 'Material Name', 'type' => 'text'],
	'density' => ['name' => 'Density [0.01,..etc.](kg/m3)', 'type' => 'number', 'step' => 0.01],
	'mw' => ['name' => 'Mw [0.01,..etc.](kg/mol)', 'type' => 'number', 'step' => 0.01],
];
$sql = "SELECT * FROM $base_table ORDER BY `type` DESC, `id` ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);    
$stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
$material_types = [];

echo '<div class="LoginBox5">
<div class="headertext"><h3>'.$title.'</h3></div>
<form id="materials" method="post" action="save-material.php">
		<div class="center-wrap">';
		 foreach($material_fields as $key=>$field): 
			echo '<input class="input form-control" type="'.$field['type'].'" name="'.$key.'"'.($key == 'cas' ? '' : 'required').'
			placeholder="'.$field['name'].'" min="'.@$field['min'].'" step="'.@$field['step'].'" />';
		 endforeach; 
	echo '</div>
	<input class="btn btn-success" type="submit" value="Save Material" name="save" />
	<button type="button" onclick=location.href=("material2.php"); class="btn btn-info create-material-btn">&#10149; Material List</button>';
		 if ( !empty($_SESSION['post_message']) ): 
		echo '<label class="label label-'.($_SESSION['post_message']['type'] == 'error' ? 'danger' : 'success').'">'.$_SESSION['post_message']['message'].'</label>';
	
	unset($_SESSION['post_message']);
	endif; 
echo '</form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="custom.js"></script>
</body>
</html>';
include "footer.php"; 
?>