<?php
include "checkuser.php"; 
$sestype = $_SESSION['utype'];
if ($sestype == 0){ 

include('connect.php');
include "libraries.php"; 

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
            $param_username = filter_var(trim($_POST["username"]),FILTER_SANITIZE_STRING);
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = filter_var(trim($_POST["username"]),FILTER_SANITIZE_STRING);
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
        $password = filter_var(trim($_POST["password"]),FILTER_SANITIZE_STRING);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = filter_var(trim($_POST["confirm_password"]),FILTER_SANITIZE_STRING);
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
            $param_email = filter_var(trim($_POST["email"]),FILTER_SANITIZE_STRING);
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This Email is already taken.";
                } else{
                    $email = filter_var(trim($_POST["email"]),FILTER_SANITIZE_STRING);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }

$userdescr = filter_var($_POST['userdescr'],FILTER_SANITIZE_STRING);
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)&& empty($email_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO web_users (username, password,email,userdescr,utype) VALUES (:username, :password, :email, :userdescr, :value)";
         
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_username = filter_var($username,FILTER_SANITIZE_STRING);
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_email = filter_var($email,FILTER_SANITIZE_STRING);
            $param_userdescr = filter_var($userdescr,FILTER_SANITIZE_STRING);
            $value = filter_var($_POST['utype']);
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":userdescr", $param_userdescr, PDO::PARAM_STR);
            $stmt->bindParam(':value', $_POST['utype'], PDO::PARAM_INT); 
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                
                $_SESSION['success_message'] = "User added successfully.";
                //header("location: adduser2.php");
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
    <title>Pharmathen Add User page</title>
</head>
<body>

 <div class="LoginBox5"> 
    <div class="wrapper">                 
     <form action="' .htmlspecialchars($_SERVER["PHP_SELF"]). '" method="post" class="form-signin">
           </br>
              <div class="headertext"><h3>Add User</h3></div>
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
               <textarea class="form-control" id="textspace" type="text" placeholder="User Description" name="userdescr"  value="-"></textarea>
               
            User Type:<select style="width:200px; margin:auto;" class="form-control" name="utype" required>
                          <option value="">Select</option>
                          <option value="0">Administrator</option>
                          <option value="1">User</option>
                      </select>
            </br>
            </div>    
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Add User">
            </div>';
             if (isset($_SESSION['success_message'])) { 
                        echo '<div class="success-message" style="margin-bottom: 20px; font-size:16px; color: green;">' .$_SESSION['success_message'].'</br><a href="adduser2.php">Add new User</a></div>';
                    
                        unset($_SESSION['success_message']);
                    }
                    
          echo '</form>
    </div>  
</div>  
</body>
</html>';
include "footer.php";
}
?>