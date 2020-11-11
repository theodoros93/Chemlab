<?php $title = 'Materials Price List'; 
include "checkuser.php";
include "libraries4.php";

echo '<html>
<header>
<title>'.$title.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</header>
<body>';
include("connect.php");
$base_table = 'material_price';
$sql = "SELECT * FROM materials ORDER BY `type` ASC, id ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);    
$stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
$selected_material_id = @$_GET['mid'];
$materialPrice = 0;
$date = '';
if ( is_numeric($selected_material_id) && !empty($_GET['date']) ){
	$selected_material_id = (double) $selected_material_id;
	$date = $_GET['date'];
	$sql = "SELECT * FROM $base_table WHERE `material_id` = $selected_material_id AND `date` = '$date'";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':selected_material_id', $_POST['material_id'], PDO::PARAM_INT);       
    $stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR); 
	$stmt->execute();
	$material = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( !empty($material) ){
		$materialPrice = $material['price'];
	}
	#GET LAST FILLED PRICE
	$sql = "SELECT * FROM $base_table WHERE `material_id` = $selected_material_id ORDER BY `date` DESC LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':selected_material_id', $_POST['material_id'], PDO::PARAM_INT);       
    $stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR); 
	$stmt->execute();
	$last_price = $stmt->fetch(PDO::FETCH_ASSOC);

}
$material_types = [];

echo '<div class="LoginBox">
<div class="headertext"><h3>'.$title.'</h3></div>
<form id="material-price-form" action="save-material-price.php" method="post">';
	 if ( !empty($materials) ): 
		echo '<h4>Materials List</h4>
		<select id="materials-prices" name="id" class="form-control">
                                <option>---SELECT---</option>';
			 foreach($materials as $material): 
				 if ( $material['type'] == 'SOLID' && !in_array($material['type'], $material_types) ): $material_types[] = $material['type'];
					echo '</optgroup><optgroup label="--- ΣΤΕΡΕΑ ---">';
				 endif; 
				 if ( $material['type'] == 'LIQUID' && !in_array($material['type'], $material_types) ): $material_types[] = $material['type'];
					echo '</optgroup><optgroup label="--- ΥΓΡΑ ---">';
				 endif; 
				echo '<option '.($selected_material_id == $material['id'] ? 'selected="selected"' : '').' 
				data-material='.htmlentities(json_encode($material)).' value="'.$material['id'].'">'.$material['name'].'</option>';
			 endforeach; 
		echo '</select>';
     endif; 
	echo '<div id="datepicker"></div>
	<input type="hidden" value="'.$date.'" name="date" />
	<input type="hidden" value="" id="selected-date" />
	<br/>';
	 if ( !empty($last_price) ): 
		echo 'Last Price: '.$last_price['price'].' € at  '; echo date('d/m/Y', strtotime($last_price['date']));
	 endif; 
	echo '<br/>';
	 if ( is_numeric($selected_material_id) ): 
		echo 'Price/kg or L: <input class="form-control" type="number" required min="0" step="0.01" max="99999" placeholder="0.00" id="material-price" name="price"
		value="'.($materialPrice > 0 ? $materialPrice : '').'" />  &euro;';
	 endif; 
	echo '<br/>
	<input class="btn btn-primary" type="submit" value="Save" />';
	 if ( !empty($_SESSION['post_message']) ): 
		echo '<label class="label label-'.($_SESSION['post_message']['type'] == 'error' ? 'danger' : 'success').'">'.$_SESSION['post_message']['message'].'</label>';
	
	unset($_SESSION['post_message']);
	endif; 
echo '</form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="custom.js"></script>
</body>
</html>';
include "footer.php"; 
?>