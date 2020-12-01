<?php

session_start();
 if(isset($_SESSION["id"]) && ($_SESSION["loggedin"] && $_SESSION["loggedin"] === true)){

}
else{
	header("Location: login.php");
	die("You are not Logged In!");
} 
$sestype = $_SESSION['utype'];
if ($sestype == 0){ 

    include 'admin_navbar.php';

 }
 elseif($sestype==1 ){

    include 'user_navbar.php';

}
?>
