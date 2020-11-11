<?php
$title = 'Charts'; 
include "checkuser.php";
include "libraries4.php";
echo '<html>
<header>
<title><?=$title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</header>
<body>';

include('connect.php');
$sql = "SELECT * FROM materials ORDER BY `type` ASC, id ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":type", $_POST['type'], PDO::PARAM_STR);
$stmt->bindParam(":id", $_POST['id'], PDO::PARAM_INT);
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
$material_types = [];

echo '<div class="LoginBox2">
<div class="headertext"><h3>'.$title. '</h3></div>';
if ( !empty($materials) ): 
	echo '<h4>Choose Element From The List:</h4>
	<select id="materials-chart" name="id" class="sel">';
		 foreach($materials as $material): 
			if ( $material['type'] == 'SOLID' && !in_array($material['type'], $material_types) ): $material_types[] = $material['type'] ;
				echo '</optgroup><optgroup label="SOLID">';
			 endif; 
			 if ( $material['type'] == 'LIQUID' && !in_array($material['type'], $material_types) ): $material_types[] = $material['type'] ;
				echo '</optgroup><optgroup label="LIQUID">';
			 endif; 
			$id = $material['id'];
			$stmt = $pdo->prepare("SELECT `price`, `date` FROM material_price WHERE `material_id` = $id ORDER BY `date` ASC");
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();
			$material_prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			echo '<option data-prices='.html_entity_decode(json_encode($material_prices)).' value="'.$material['id'].'">'.$material['name'].'</option>';
		 endforeach; 
	echo '</select>';
 endif; 
echo '<br/><br/>
<input type="text" id="daterangepicker" value="01/01/YYYY - 01/12/YYYY"/>
<button type="button" class="form-control btn btn-primary" id="select-daterangepicker" style="width: auto; position: relative; top: -11px;">OK</button>
<br/>
<div style="height: 700px">
<canvas id="chart"></canvas>
</div>
<textarea id="materials_prices">'.html_entity_decode(json_encode($material_prices)).'</textarea>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="daterangepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script src="custom.js"></script>
</body>
</html>';
include "footer.php"; 
?>