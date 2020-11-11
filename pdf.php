<?php
    include('connect.php');
    $sql = "SELECT * FROM `exports` ORDER BY `id` DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();
    $export = $stmt->fetch(PDO::FETCH_ASSOC);

echo '<html>
<head>
<link href="bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="pdf.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <h1>Project Summary</h1>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                API Summary 
                <div class="pull-right">'.$export['name'].'</div>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                    Total Cost: <b>'.$export['total_cost'].'</b>
                </div>
                <div class="col-xs-6 pull-right text-right">
                    Total Steps: <b>'.$export['total_stages'].'</b>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    Density: <b>'.$export['density'].'</b>
                </div>
                <div class="col-xs-3">
                    mw: <b>'.$export['mw'].'</b>
                </div>
                <div class="col-xs-3">
                    Qty Out: <b>'.$export['qty_out'].'</b>
                </div>
                <div class="col-xs-3">
                    w/w yield: <b>'.$export['w_w_yield'].'</b>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    cc/kg OUTPUT: <b>'.$export['cc_kg_output'].'</b>
                </div>
                <div class="col-xs-3">
                    API cc: <b>'.$export['api_cc'].'</b>
                </div>
                <div class="col-xs-3">
                    actual cc/kg API: <b>'.$export['actual_cc_kg_api'].'</b>
                </div>
                <div class="col-xs-3">
                    mol: <b>'.$export['mol'].'</b>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    mol yield: <b>'.$export['mol_yield'].'</b>
                </div>
                <div class="col-xs-3">
                    contribution: <b>'.$export['contribution'].'</b>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered">'.$export['content'].'</table>
</div>
</body>
</html>';
?>