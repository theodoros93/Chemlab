<?php

include('crpdf.php');


require_once __DIR__ . '/mpdf/vendor/autoload.php';


include('connect.php');

if ( !empty($_POST) ){
    $sql = "INSERT INTO `exports` 
    (`content`, `name`, `total_stages`, `total_cost`, `density`, `mw`, `qty_out`, `w_w_yield`, `cc_kg_output`, `api_cc`, `actual_cc_kg_api`, `mol`, `mol_yield`, `contribution`) 
    VALUES (:content, :name, :total_stages, :total_cost, :density, :mw, :qty_out, :w_w_yield, :cc_kg_output, :api_cc, :actual_cc_kg_api, :mol, :mol_yield, :contribution)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":content", $_POST['content'], PDO::PARAM_STR);
    $stmt->bindParam(":name", $_POST['name'], PDO::PARAM_STR);
    $stmt->bindParam(":total_stages", $_POST['total_stages'], PDO::PARAM_STR);
    $stmt->bindParam(":total_cost", $_POST['total_cost'], PDO::PARAM_STR);
    $stmt->bindParam(":density", $_POST['density'], PDO::PARAM_STR);
    $stmt->bindParam(":mw", $_POST['mw'], PDO::PARAM_STR);
    $stmt->bindParam(":qty_out", $_POST['qty_out'], PDO::PARAM_STR);
    $stmt->bindParam(":w_w_yield", $_POST['w_w_yield'], PDO::PARAM_STR);
    $stmt->bindParam(":cc_kg_output", $_POST['cc_kg_output'], PDO::PARAM_STR);
    $stmt->bindParam(":api_cc", $_POST['api_cc'], PDO::PARAM_STR);
    $stmt->bindParam(":actual_cc_kg_api", $_POST['actual_cc_kg_api'], PDO::PARAM_STR);
    $stmt->bindParam(":mol", $_POST['mol'], PDO::PARAM_STR);
    $stmt->bindParam(":mol_yield", $_POST['mol_yield'], PDO::PARAM_STR);
    $stmt->bindParam(":contribution", $_POST['contribution'], PDO::PARAM_STR);
    $stmt->execute();
}

$mpdf = new \Mpdf\Mpdf();
//$mpdf->SetHTMLHeader('<div style="text-align: right; font-weight: bold;">Lab Report  '.date ('d/m/Y').'</div>','O');
$mpdf->SetHeader('Lab Report  '.date ('d/m/Y').'');
$mpdf->setFooter('{PAGENO}');
$content = '';  
$content .= fetch_data(); 
$mpdf->WriteHTML($content);

$mpdf->Output('Lab-exp.pdf', 'I');

?>