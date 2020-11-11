<?php

include('connect.php');

$materials = [
    'A',
    'B',
    'C',
    'D',
    'E',
    'F',
    'G',
    'H',
    'I',
    'J',
    'K',
    'L',
    'M',
    'N',
    'O',
    'P',
    'Q',
    'R',
    'S',
    'T',
    'U',
    'V',
    'W',
    'X',
    'Y',
    'Z',
];

foreach($materials as $material){
    $name = 'MATERIAL '.$material;
    $cas = rand(1, 100);
    $density = rand(1, 1000);
    $mw = rand(1, 1000);
    $type = $density == 1 ? 'SOLID' : 'LIQUID';
    $sql = "INSERT INTO materials (`cas`, `name`, `density`, `mw`, `type`) VALUES ($cas, '$name', $density, $mw, '$type')";
    $stmt = $pdo->prepare($sql);
    #$stmt->execute();
}

$sql = "SELECT * FROM materials";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();

foreach($rows as $row){
    $id = $row['id'];
    $final_date = rand(2, 31);
    $begin = new DateTime('2019-10-01');
    $end = new DateTime('2019-10-'.$final_date);
    $end = $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    #print_r($daterange);
    
    foreach($daterange as $date){
        $current_date = $date->format("Y-m-d");
        #echo '<br>'.$current_date;
        $price = rand(1, 100);
        $sql = "INSERT INTO material_price (`material_id`, `price`, `date`) VALUES ($id, $price, '$current_date')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
}