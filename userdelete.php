<?php
include "checkuser.php";
$sestype = $_SESSION['utype'];
if ($sestype == 0){ 
include ('connect.php');
include ('libraries.php');
echo '<html>
<header>
<title>Delete User</title>

</header>
<body>';

$tid = filter_var(($_GET['tableid']));
try {
   $stmt = $pdo->prepare("SELECT * FROM web_users WHERE id ='$tid'");
   $stmt->bindParam(':tid', $_POST['id'], PDO::PARAM_INT); 
   $stmt->execute(array());
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   $count = $stmt->rowCount();

echo '<div class="LoginBox2">
	<h3>Are you sure you want to delete the user: '.$row["username"].'? </h3>
	<form method="post">
	<input type="submit" class="btn btn-success" name="yes" value="YES">
	<input type="submit" class="btn btn-danger" name="no" value="NO">
	</form>
</div>';

if (isset($_POST["yes"])){
	
$sql = "DELETE FROM web_users WHERE id = '$tid'";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':tid', $_POST['id'], PDO::PARAM_INT);   
$stmt->execute();
		header("Location: userlist2.php ");
	}
	elseif(isset($_POST["no"])){
		header("Location: userlist2.php ");
	}
	}

  catch(PDOException $e){
   echo $e->getMessage();
  }


echo '</body>
</html>';
include ('footer.php');
}
?>