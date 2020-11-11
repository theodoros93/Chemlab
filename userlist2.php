<?php
include "checkuser.php";
$sestype = $_SESSION['utype'];
if ($sestype == 0){ 
include "library.php";
include "imptable.php";
include('connect.php');
echo '<html>
<header>
<title>User Edit</title>
<meta name="viewport" content="initial-scale=1.0, width=device-width">
</header>
<body>

<div class="LoginBox2">
<div class="headertext"><h2>Registered Users List</h2></div>
<table id="empTable" class="display responsive" width="100%">

  <thead>
    <tr>
      <th>User ID</th>
      <th>User Name</th>
      <th>Password</th>
      <th>Email</th>
      <th>User Decription</th>
      <th>User Type</th>
      <th>Options</th>
    </tr>

  </thead>

</table>
</div>
</body>
</html>';
include "footer.php"; 
}
?>