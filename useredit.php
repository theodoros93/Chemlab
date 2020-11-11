<?php
include "checkuser.php";
$sestype = $_SESSION['utype'];
if ($sestype == 0){ 
include('connect.php');
include "libraries.php"; 
echo '<html>
<header>
<title>User Edit</title>
</header>
<body>';

$id = $_GET['id'];
try{

   $stmt = $pdo->prepare("SELECT * FROM web_users WHERE id =:id");
   $id = filter_var($id);
   $stmt->bindParam(":id", $id, PDO::PARAM_INT);
   $stmt->execute([':id' => $id]);
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   $count = $stmt->rowCount();



echo '<div class="LoginBox5">
<div class="headertext"><h2>User Edit</h2></div>
<form method="post">
	User Name:<br><input id="textspace" type="text"  placeholder="Όνομα Χρήστη" name="username" value="' .$row["username"]. '" required><br>
	Email:<br>
<input id="textspace" type="email" placeholder="Email Χρήστη" name="email" value="' .$row["email"]. '"required><br>

	User Description:<br>
<textarea id="textspace" type="text" placeholder="Περιγραφή Χρήστη" name="userdesc">' .$row["userdescr"]. '</textarea>
	</br>
  User Type:<br>
<select style="width:200px; margin:auto;" class="form-control" name="utype" required>
    <option value="">Select</option>
    <option value="0">Administrator</option>
    <option value="1">User</option>
</select>
  </br>
	<input class="btn btn-primary" style="margin-top:10px;" type="submit" name="submit" value="Save Changes">
	<h4><font color="green"><div id="success"></div></font></h4>';

if (isset($_POST["submit"])){
	$uname = filter_var($_POST["username"]);
	$umail = filter_var($_POST["email"]);
	$udesc = filter_var($_POST["userdesc"]);
  $value = filter_var($_POST['utype']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "UPDATE web_users SET username=N'$uname', email=N'$umail' , userdescr=N'$udesc', utype=N'$value' WHERE id = '$id'";
        $stmt = $pdo->prepare($sql);                                  
        $stmt->bindParam(':uname', $_POST['username'], PDO::PARAM_STR);           
        $stmt->bindParam(':umail', $_POST['email'], PDO::PARAM_STR); 
        $stmt->bindParam(':udesc', $_POST['userdescr'], PDO::PARAM_STR);   
        $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        $stmt->bindParam(':value', $_POST['utype'], PDO::PARAM_INT);    
        $stmt->execute(); 

	if ($pdo->query($sql)) {
    $_SESSION['success_message'] = "Changes saved successfully";
//header("Location: userlist2.php");
}
$pdo = null;
}

}

  catch(PDOException $e){
   echo $e->getMessage();
  }
if (isset($_SESSION['success_message'])) { 
                        echo '<div class="success-message" style="margin-bottom: 20px; font-size:16px; color: green;">' .$_SESSION['success_message'].'</br><a href="userlist2.php">Go back to userlist</a></div>';
                    
                        unset($_SESSION['success_message']);
                    }

echo '</form>
</div>
</body>
</html>';
include ('footer.php');
}
?>