<?php
session_start(); 

// Include config file
include "connect.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email= "";
$username_err = $password_err = $confirm_password_err =$email_err= "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM web_users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_username = filter_var(trim($_POST["username"]));
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = filter_var(trim($_POST["username"]));
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = filter_var(trim($_POST["password"]));
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = filter_var(trim($_POST["confirm_password"]));
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
     // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM web_users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_email = filter_var(trim($_POST["email"]));
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This Email is already taken.";
                } else{
                    $email = filter_var(trim($_POST["email"]));
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }

$message = "$username  would like to request an account.";

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)&& empty($email_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO requests (username, password,email,message,date) VALUES (:username, :password, :email, :message, CURRENT_TIMESTAMP)";
         
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_username = filter_var($username);
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_email = filter_var($email);
            $param_message = filter_var($message);
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":message", $param_message, PDO::PARAM_STR);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: index.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
} 
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pharmathen Sign Up page</title>
</head>
<body>';
include "libraries.php"; 
 echo'<div class="LoginBox4"> 
    <div class="wrapper">                 
     <form action="' .htmlspecialchars($_SERVER["PHP_SELF"]). '" method="post" class="form-signin">
           </br>
              <img style="margin-bottom:10px;" src="icons/register.png" >
              </br>
              <h3>Sign Up</h3>
              </br>

 <div class="form-group' .(!empty($username_err)).' ? "has-error" : "" ">
                <label for="inputEmail" class="sr-only">Username</label>
                <input type="text" name="username" autocomplete="on" class="form-control" value="' .$username. '" placeholder="Username" required autofocus>
                <span class="help-block">' .$username_err. '</span>
            </div>    
            <div class="form-group' .(!empty($password_err)). ' ? "has-error" : "" ">
               <label for="inputEmail" class="sr-only">Password</label>
                <input type="password" name="password" autocomplete="on" class="form-control" value="' .$password. '"placeholder="Password" required autofocus>
                <span class="help-block">' .$password_err. '</span>
            </div>
            <div class="form-group' .(!empty($confirm_password_err)).' ? "has-error" : "" ">
                <label for="inputEmail" class="sr-only">Confirm Password</label>
                <input type="password" name="confirm_password" autocomplete="on" class="form-control" value="' .$confirm_password. '"placeholder="Confirm Password" required autofocus>
                <span class="help-block">' .$confirm_password_err. '</span>
            </div>
<div class="form-group' .(!empty($email_err)). ' ? "has-error" : "" ">
                <label for="inputEmail" class="sr-only">Email</label>

                <input type="email" name="email"  class="form-control" value="' .$email. '" placeholder="Email" required autofocus>
                <span class="help-block">' .$email_err. '</span>
            </div>    

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Sign Up">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>';
?>
