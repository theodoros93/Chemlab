<?php
include "checkuser.php";
include "library.php";
include "imptable2.php";
include('connect.php');
echo '<html>
<header>
<title>Lab Experiments</title>
<meta name="viewport" content="initial-scale=1.0, width=device-width">
</header>
<body>

<div class="LoginBox2">
<div class="headertext"><h2>Lab Experiments Reports</h2></div>
<table id="labTable" class="display responsive" width="100%">

  <thead>
    <tr>
      <th>Lab ID</th>
      <th>API Name</th>
      <th>Total Stages</th>
      <th>Total Cost (&euro;)</th>
      <th>Density</th>
      <th>MW</th>
      <th>Quantity(kg)</th>
      <th>W/W Yield</th>
      <th>CC/Kg Output</th>
      <th>API cc</th>
      <th>Actual cc/kg API</th>
      <th>Mol(kg)</th>
      <th>Mol Yield</th>
      <th>Contribution %</th>
      <th>Date Created (YY/MM/DD)</th>
      <th>Options</th>
    </tr>

  </thead>

</table>
</div>
</body>
</html>';
include "footer.php"; 
?>