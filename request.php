<?php
include "checkuser.php";
$sestype = $_SESSION['utype'];
if ($sestype == 0){ 
include "libraries.php";
include "connect.php";
echo '<!doctype html>
<html lang="en">
  <head>
    <title>Pending Request System in PHP and MySql</title>
  </head>
  <body>       
    <main role="main">
      <section class="LoginBox2">
        <div class="wrapper">';
               try {
                     $stmt = $pdo->prepare("SELECT * FROM requests");
                     $stmt->execute(array());
                     $row = $stmt->fetch(PDO::FETCH_ASSOC);
                     $count = $stmt->rowCount();
                     if ($count>0) {       
                      echo '<p class="lead text-muted">' .$row["message"]. '</p>
                      <p class="lead text">' .$row['email']. '</p>
                      <p>
                        <a href="accept.php?id=' .$row["id"]. '" class="btn btn-primary my-2"style="background-color: green;">Accept</a>
                        <a href="reject.php?id=' .$row["id"]. '" class="btn btn-danger my-2">Reject</a>
                      </p>
                    <small><i>' .$row["date"]. '</i></small>';
            
                    }
                    else{
                          echo '--------------------------------------------------------------------------';
                          echo '<h3>NO PENDING REQUESTS</h3>';
                          echo '--------------------------------------------------------------------------';
                }
              }
                     catch(PDOException $e){
                     echo $e->getMessage();
                      }  
            
        echo '</div> 
      </section>
    </main>
  </body>
</html>';
include "footer.php";
}
?>