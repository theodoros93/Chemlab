<?php
include "checkuser.php";
$sestype = $_SESSION['utype'];
if ($sestype == 0){ 
include('connect.php');
include "libraries.php"; 
echo '<html>
<header>
<title>Recover Password</title>
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
<div class="headertext"><h2>Recover Password</h2></div>
<form method="post">
    Insert New Password:<br><input id="textspace" type="text" placeholder="Insert new password" name="password" required><br>
    <input class="btn btn-primary" style="margin-top:10px;" type="submit" name="submit" value="Save new password">';

if (isset($_POST["submit"])){
    $upass = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE web_users SET password=N'$upass' WHERE id = '$id'";
        $stmt = $pdo->prepare($sql);                                   
        $stmt->bindParam(':upass', $_POST['password'], PDO::PARAM_STR);       
        $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);   
        $stmt->execute(); 

    if ($pdo->query($sql)) {
      $_SESSION['success_message'] = "Password Changed";
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