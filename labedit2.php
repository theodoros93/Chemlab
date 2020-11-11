<?php $title = 'API Stages';
include "libraries2.php"; 
include "checkuser.php";
include('connect.php');

echo '<html>
<head>  
<title>'.$title.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body onload="initializeVariables();">';


#GET EUR/USD PARITY
$sql = "SELECT * FROM settings WHERE `name` = 'eur-usd'";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':eur-usd', $_POST['name'], PDO::PARAM_STR);
$stmt->execute();
$parity = $stmt->fetch(PDO::FETCH_ASSOC);
$base_table = 'materials';
$table = [
	'header' => 
	['A/A', 'Raw Material', 'CAS', 'sp.gr.', 'MW', 'price/kg', 'unit', 'qty', 'cc/kg INPUT', 'cc/kg OUTPUT', 'cc/kg API', 'actual cc/kg API', 'cost', 'mol', 'mol ratio', 'contribution %', 'Action'],
	'footer' => 
	['', 'Output', '', 'Density', 'MW', 'EUR', 'USD', 'qty out', 'w/w yield', 'cc/kg OUTPUT', 'API cc', 'actual cc/kg API', 'cost', 'mol', 'mol yield', 'contribution', '']
];
$sql = "SELECT * FROM materials ORDER BY `type` ASC, id ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
$stmt->execute();
$materials_tmp = $stmt->fetchAll(PDO::FETCH_ASSOC);
$materials = [];
$material_types = [];
if ( !empty($materials_tmp) ){
	foreach($materials_tmp as &$m){
		$material_id = $m['id'];
		$sql = "SELECT price FROM `material_price` WHERE `material_id` = $material_id AND `date` <= CURDATE() ORDER BY id DESC LIMIT 1";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':material_id', $_POST['material_id'], PDO::PARAM_INT);
		$stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR); 
		$stmt->execute();
		$price = $stmt->fetch();
		if ( !empty($price) ){
			$m['price/kg'] = $price['price'];
		} else{
			$m['price/kg'] = '';
		}
		$materials[] = $m;
	}
}

echo '<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">File
    <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="#" type="button" id="save-new-material-btn">Save API Material</a></li>
      <li><a href="#" type="button" id="export-lab-btn">Save Lab Experiment</a></li>
      <li><a href="#" type="button" id="export-pdf-btn">Export As PDF</a></li>
    </ul>
  </div>
</div>';
 $cid = $_GET['id'];


   $stmt = $pdo->prepare("SELECT content FROM lab_experiments WHERE id = '$cid'");
   $stmt->bindParam(':cid', $_POST['id'], PDO::PARAM_INT);
   $stmt->execute(array());
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   $count = $stmt->rowCount();
   
echo '<div class="headertext"><h3>Edit Lab Experiment '.$cid.'</h3></div>
<form method="post">
	<input type="hidden" id="parity" value="'.$parity['value'].'" />
	<div id="tab">
    <table id="stages">
	'.$row["content"].'
    </table>
	<hr/>
</div>';

	if ( !empty($materials) ): 
		echo '<div class="col-lg-4">
			<h4>Chemical Elements List</h4>
			<select class="form-control" id="materials">';
				 foreach($materials as $material):
					 if ( $material['type'] == 'SOLID' && !in_array($material['type'], $material_types) ): $material_types[] = $material['type'];
						echo '</optgroup><optgroup label="SOLID">';
					 endif; 
					 if ( $material['type'] == 'LIQUID' && !in_array($material['type'], $material_types) ): $material_types[] = $material['type'];
						echo '</optgroup><optgroup label="LIQUID">';
					 endif;
					echo '<option data-material="'.htmlentities(json_encode($material)).'" value="'.$material['id'].'">'.$material['name'].'</option>';
				  endforeach; 
			echo '</select>
			<h4>Select Stage:</h4><select style="width:100px; border-radius:4px;"  id="selstage" required>
                          <option value="">Select</option>
                          <option value="1">Stage 1</option>
                          <option value="2">stage 2</option>
                          <option value="3">stage 3</option>
                          <option value="4">stage 4</option>
                          <option value="5">stage 5</option>
                          <option value="6">stage 6</option>
                          <option value="7">stage 7</option>
                          <option value="8">stage 8</option>
                          <option value="9">stage 9</option>
                          <option value="10">stage 10</option>
                      </select></br>
			<input type="button" id="add-mat-btn" class="btn btn-success" value="Add" />
		</div>
		<div class="col-lg-3">
			<h4>Programmer</h4>
			<input type="button" id="debug-btn" class="btn btn-danger" value="DEBUG" />
			<input type="button" id="test-btn" class="btn btn-warning" value="RANDOM VALUES" />
			<input class="btn btn-info" id="alert-target" type="button" value="
Recalculation" onclick="initializeVariables();" />
		</div>
		<div class="col-lg-3">
			<h4>Options</h4>
			<input type="button" id="add-stage-btn" class="btn btn-primary" value="Insert a new stage" />
			<input type="button" id="calculate-btn" class="btn btn-primary" value="Calculate current stage" />
			<br/>
			<input type="button" id="complete-btn" class="btn btn-success" value="Stages Finalization" />
		</div>';
	 endif; 
echo '</form>

</div>

<div class="container">
  <h4>Table Info</h4>
  <!-- Trigger the modal with a button -->
  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Click for</br>Information</button>

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Table Header rows information</h4>
        </div>
        <div class="modal-body">
<p><u style="color:blue">Raw Material:</u> Material name</p>
<p><u style="color:blue">CAS:</u> Material Code</p>
<p><u style="color:blue">sp.gr.:</u> Material density(kg/m^3)</p>
<p><u style="color:blue">MW:</u> Molecular weight(kg/Mol)</p>
<p><u style="color:blue">price/kg:</u> Material price per kilos</p>
<p><u style="color:blue">Unit:</u> Material unit(kg or L)</p>
<p><u style="color:blue">Qty:</u> Amount of chemical element in kg or L</p>
<p><u style="color:blue">cc/kg INPUT:</u> This field contains the cubic centimeters of a liquid element or the kilograms of a solid element at the entry of a chemical process.</p>
<p><u style="color:blue">cc/kg OUTPUT:</u> This field contains the cubic centimeters of a liquid element or the kilograms of a solid element at the exit of a chemical process. </p>
<p><u style="color:blue">cc/kg API or API cc:</u> The cubic centimeters or kilos of the final active medicinal ingredient (final product)</p>
<p><u style="color:blue">Actual cc/kg API:</u> The cubic centimeters or kilos of the final active medicinal ingredient (final product) </p>
<p><u style="color:blue">Cost:</u> The cost of chemical element</p>
<p><u style="color:blue">Total Cost:</u> The total cost of final chemical element</p>
<p><u style="color:blue">Mol:</u> This field contains the amount of substance in kilograms per molecular weight. The mol is a unit of measurement.</p>
<p><u style="color:blue">Mol ratio:</u> A mole ratio is the ratio between the amounts in moles of any two compounds involved in a chemical reaction.</p>
<p><u style="color:blue">Contribution:</u> This field shows the amount of item (%) used to generate the final item.</p>
<p><u style="color:blue">Output:</u> The name of final chemical element</p>
<p><u style="color:blue">Density:</u> Material density(kg/m^3)</p>
<p><u style="color:blue">EUR:</u> Total cost of fianl chemical element in EURO(€)</p>
<p><u style="color:blue">Qty out:</u> The quantity in kg,gr,L, or ml of final chemical element</p>
<p><u style="color:blue">w/w yield:</u> This field contains the percentage efficiency, which serves to measure the effectiveness of a process, composition.</p>
<p><u style="color:blue">mol yield:</u> This field contains the efficiency of the molecular element in percentage</p>      

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>

<script>
$("#alert-target").click(function () {
toastr["info"]("Έγινε Επαναυπολογισμός")
});</script>
<script src="jquery-3.3.1.min.js"></script>
<script src="chosen.jquery.min.js"></script>
<script src="toastr.min.js"></script>
<script src="custom.js"></script>
</body>
</html>';
include "footer.php"; 
?>