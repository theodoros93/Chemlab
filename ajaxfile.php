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
        email LIKE :email) ";
   $searchArray = array( 
        'id'=>"%$searchValue%", 
        'email'=>"%$searchValue%");
}

## Total number of records without filtering
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM web_users");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

## Total number of records with filtering
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM web_users WHERE 1 ".$searchQuery);
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$stmt = $pdo->prepare("SELECT * FROM web_users WHERE 1 ".$searchQuery." ORDER BY ".$columnName." ".$columnSortOrder." LIMIT :limit,:offset");

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
  if ($row['utype']=== 0) {
        $row['utype']= "Administrator";
      }
      else
      {
         $row['utype']= "User";
      }
   $data[] = array(
      "id"=>$row['id'],
      "username"=>$row['username'],
      "password"=>'<form method="get" action="new_pass.php" ><input type="submit" class="btn btn-primary" name="edit" style="box-shadow:2px 2px 2px black" value="Change Password"><input type="hidden" value="'.$row['id'].'" name="id"></form>',
      "email"=>$row['email'],
      "userdescr"=>$row['userdescr'],
      "utype"=>$row['utype'],
      "Options"=>'<form method="get" action="useredit.php" ><input type="submit" class="btn btn-success" name="edit" style="box-shadow:2px 2px 2px black" value="Edit"><input type="hidden" value="'.$row['id'].'" name="id"></form><form method="get" action="userdelete.php"><input type="submit" name="delete" class="btn btn-danger btn-sm" style="box-shadow:2px 2px 2px black" value="Delete"><input type="hidden" value="'.$row['id'].'" name="tableid"></form>'
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