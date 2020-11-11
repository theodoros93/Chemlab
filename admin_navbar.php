<?php  
include "connect.php";
$stmt = $pdo->prepare("SELECT * FROM requests");
$stmt->execute(array());
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$count = $stmt->rowCount();
echo '<!DOCTYPE html>
<html lang="en">
<head>
<style>
.dropdown-menu {
      background-color:#262626 !important;
      
    }
.dropdown-menu a{
    color: white !important;
}
.dropdown-menu li > a:hover{
color:black !important;
}
#myNavbar .navbar-nav li.active > a {
	background-color: #009933;
	
}</style>
  <title>ChemLab</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <img src="icons/chem2.png" style="float:left;">
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="stages.php"><span class="glyphicon glyphicon-home"></span>  Home</a></li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-user"></span> Users <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="adduser2.php"><span class="glyphicon glyphicon-plus"></span> Add User</a></li>
            <li><a href="userlist2.php"><span class="glyphicon glyphicon-pencil"></span> Edit/Delete User</a></li>               
           <li><a href="request.php"><span class="glyphicon glyphicon-envelope"></span> Users Requests   <b style="color:green;"> '.$count.'</b></a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-book"></span> Chemical Materials <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="material2.php"><span class="glyphicon glyphicon-plus"></span> Add or Edit Material</a></li>
            <li><a href="material_price.php"><span class="glyphicon glyphicon-sort"></span> Material pricing</a></li>
          </ul>
        </li>
        <li><a href="charts.php"><span class="glyphicon glyphicon-stats"></span> Charts</a></li>
         <li><a href="labs.php"><span class="glyphicon glyphicon-folder-open"></span> Lab experiments</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
</body>
</html>';
?>