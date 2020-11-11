<?php
// Initialize the session
session_start();
// Check if the user is already logged in, if yes then redirect him to welcome page

// Include config file
include "connect.php";


// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = filter_var(trim($_POST["username"]));
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = filter_var(trim($_POST["password"]));
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password, utype FROM web_users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_username = filter_var(trim($_POST["username"]));
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                 
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $type=$row["utype"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;   
                            $_SESSION["utype"] = $type;                        
                            $sesid = $_SESSION['id'];
                            if ($sesid == 0){ 
                            // Redirect user to welcome page
                            header("location: stages.php");
                            die();
                            }
                            elseif($sesid > 0){
                            header("location: stages.php");
                            die();
                        } }else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
}
 
echo'
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>';
include "libraries.php";
    echo '<div class="LoginBox3">
    <div class="wrapper">    
        <img src="icons/login.png" ><h2>Log In</h2>
<form action="'  .htmlspecialchars($_SERVER["PHP_SELF"]). '" method="post">           
<div class="form-group'  .(!empty($username_err)).' ? "has-error" : "" ">                
<i class="fa fa-user icon"></i>
<label>Username<input type="text" name="username" autocomplete="on" class="form-control" value="'   .$username. '"></label>
<span class="help-block">'.$username_err. '</span>           
</div>    
<div class="form-group'   .(!empty($password_err)).' ? "has-error" : """>               
<i class="fa fa-lock icon"></i>
                <label>Password
                <input type="password" name="password" autocomplete="on" class="form-control"></label>
                <span class="help-block">'  .$password_err. '</span>           
 </div>
            <div class="form-group">
<span class="icon-input-btn"><span class="glyphicon glyphicon-log-in"></span>
                <input type="submit" class="btn btn-success" value="Login">
            </div>
            <p>Dont have an account? <a href="signup2.php">Sign up now</a>.</p>
        </form>
    </div>
</div>
</body>
</html>';
?>