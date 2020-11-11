<?php
 if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: login.php");
    exit;
}

include 'connect.php';

## Read value
$draw = filter_var($_POST['draw']);
$row = filter_var($_POST['start']);
$rowperpage = filter_var($_POST['length']); // Rows display per page
$columnIndex = filter_var($_POST['order'][0]['column']); // Column index
$columnName = filter_var($_POST['columns'][$columnIndex]['data']); // Column name
$columnSortOrder = filter_var($_POST['order'][0]['dir']); // asc or desc
$searchValue = filter_var($_POST['search']['value']); // Search value

$searchArray = array();

## Search 
$searchQuery = " ";
if($searchValue != ''){
   $searchQuery = " AND (id LIKE :id or 
        name LIKE :name) ";
   $searchArray = array( 
        'id'=>"%$searchValue%", 
        'name'=>"%$searchValue%");
}

## Total number of records without filtering
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM lab_experiments");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

## Total number of records with filtering
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM lab_experiments WHERE 1 ".$searchQuery);
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$stmt = $pdo->prepare("SELECT * FROM lab_experiments WHERE 1 ".$searchQuery." ORDER BY ".$columnName." ".$columnSortOrder." LIMIT :limit,:offset");

// Bind values
foreach($searchArray as $key=>$search){
   $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
}

$stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
$stmt->execute();
$empRecords = $stmt->fetchAll();

$data = array();

foreach($empRecords as $row){
   $data[] = array(
      "id"=>$row['id'],
      "name"=>$row['name'],
      "total_stages"=>$row['total_stages'],
      "total_cost"=>$row['total_cost'],
      "density"=>$row['density'],
      "mw"=>$row['mw'],
      "qty_out"=>$row['qty_out'],
      "w_w_yield"=>$row['w_w_yield'],
      "cc_kg_output"=>$row['cc_kg_output'],
      "api_cc"=>$row['api_cc'],
      "actual_cc_kg_api"=>$row['actual_cc_kg_api'],
      "mol"=>$row['mol'],
      "mol_yield"=>$row['mol_yield'],
      "contribution"=>$row['contribution'],
      "created_at"=>$row['created_at'],
      "Options"=>'<form method="get" action="labedit2.php" ><input type="submit" class="btn btn-primary" name="labedit2" style="box-shadow:2px 2px 2px black" value="Edit Lab"><input type="hidden" value="'.$row['id'].'" name="id"></form>
      <form method="get" action="labdelete.php"><input type="submit" name="labdelete" class="btn btn-danger btn-sm" style="box-shadow:2px 2px 2px black" value="Delete Lab"><input type="hidden" value="'.$row['id'].'" name="labid"></form>'
   );
}

## Response
$response = array(
   "draw" => intval($draw),
   "iTotalRecords" => $totalRecords,
   "iTotalDisplayRecords" => $totalRecordwithFilter,
   "aaData" => $data
);

echo json_encode($response);