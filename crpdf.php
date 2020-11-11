<?php
function fetch_data()  
{  

    include('connect.php');
    $output ='';
    $sql = "SELECT * FROM `exports` ORDER BY `id` DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();
    $export = $stmt->fetch(PDO::FETCH_ASSOC);


      
$output .= '<html>
<head>

<link href="pdf2.css" rel="stylesheet" type="text/css">
</head>
<body>
</br><h1>Project Summary</h1>
    
<div class="container">
    
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                Active Pharmaceutical Ingredient Summary : <b style="color:#003d99;">'.$export['name'].'</b></h3> </div>
            
        </div></div>
        <div class="container2">
        <div class="column">
            
                    Total Cost: <b>'.$export['total_cost'].'</b>
                </div>
                <div class="column">
                    Total Steps: <b>'.$export['total_stages'].'</b>
                </div>
<div class="column">
            
                    Density: <b>'.$export['density'].'</b>
               </div>
               <div class="column">
                    mw: <b>'.$export['mw'].'</b>
                </div>
                <div class="column">
                    Qty Out: <b>'.$export['qty_out'].'</b>
                </div>
                <div class="column">
                    w/w yield: <b>'.$export['w_w_yield'].'</b>
                </div>
           
           <div class="column">
                    cc/kg OUTPUT: <b>'.$export['cc_kg_output'].'</b>
                </div>
                <div class="column">
                    API cc: <b>'.$export['api_cc'].'</b>
                 </div>
                 <div class="column">
                    actual cc/kg API: <b>'.$export['actual_cc_kg_api'].'</b>
                 </div>
                 <div class="column">
                    mol: <b>'.$export['mol'].'</b>
                </div>
                <div class="column">
                    mol yield: <b>'.$export['mol_yield'].'</b>
                </div>
                <div class="column">
                    contribution: <b>'.$export['contribution'].'</b>
                </div>
           
    </div>
   
</div> 
<h2>Table Stages:</h2>
<table class="table">'.$export['content'].'</table>
</body>
</html>'; 
return $output;  
}   
?>