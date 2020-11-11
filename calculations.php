<?php

$system_message = ['req' => 'error', 'message' => 'Κάτι πήγε στραβά', 'calculation' => null];
if ( !empty($_POST['stage']) && !empty($_POST['position']) && !empty($_POST['field']) && !empty($_POST['inputs']) ){
    $stage = $_POST['stage'];
    $stage_length = $_POST['stage_length'];
    $position = $_POST['position'];
    $field = $_POST['field'];
    $inputs = $_POST['inputs'];
    $total = @$_POST['total'] ? ((float) $_POST['total']) : null; 
    $calculation = [];
    $inputs['footer'] = isset($inputs['footer_'.$stage]) ? $inputs['footer_'.$stage] : null;

    #file_put_contents('.log', json_encode($total)."\n", FILE_APPEND);

    #BODY CONTRIBUTION
    if ( $field == 'contribution' ){
        $material_cost = $inputs['body']['stage_'.$stage]['stage_length_'.$stage_length]['cost'];
        if ( !isset($material_cost) || !isset($total) ){
            $system_message['message'] = 'Yπολογισμός: <b>'.$field.'</b><br/>Στάδιο: '.$stage.'<br/>Τα: <b>cost</b> είναι απαιτούμενα';
        } else{
            #ACTUAL CALCULATION
            $system_message['req'] = 'success';
            $calculation = ($material_cost / $total) * 100;

            #file_put_contents('.log', $material_cost."\n", FILE_APPEND);

            $system_message['calculation'] = number_format($calculation, 2, '.', '');
        }
    } else{
        if ( $position == 'body' ){
            $requirements = areRequirementsMetForBody($position, 'stage_'.$stage, 'stage_length_'.$stage_length, $field, $inputs);
        } else if ( $position == 'footer' ){
            $requirements = areRequirementsMetForFooter($position, 'stage_'.$stage, $field, $inputs);
        }

        #file_put_contents('.log', $field.' | '.json_encode($requirements)."\n", FILE_APPEND);

        if ( $requirements['req'] !== true ){
            $system_message['message'] = 'Yπολογισμός: <b>'.$field.'</b><br/>Στάδιο: '.$stage.'<br/>Τα: <b>'.$requirements['fields'].'</b> είναι απαιτούμενα';
        } else{
            $system_message['req'] = 'success';
            if ( $position == 'body' ){
                $parts = explode('.', $field);
                if ( count($parts) == 3 ){
                    $field = $parts[2];
                }
            }
            $system_message['calculation'] = calculateField($position, 'stage_'.$stage, $field, $requirements['fields'], $inputs, 'stage_length_'.$stage_length);
        }
    }

    echo json_encode($system_message);
}

function areRequirementsMetForBody(string $position, string $stage, string $stage_length, string $field, array $inputs){
    $repeatReq1 = [
        'mol' => ['mw', 'cc/kg input'],
        'cc/kg output' => ['cc/kg input', 'footer.w/w yield'],
        'cc/kg api' => ['cc/kg output', 'footer.api cc'],
        'actual cc/kg api' => ['cc/kg output', 'footer.api cc'],
        'cost' => ['price/kg', 'actual cc/kg api'],
    ];
    $repeatReq2 = [
        'cc/kg input' => ['qty', 'density', 'stage_length_1.qty'],
        'mol ratio' => ['mol', 'stage_length_1.mol'],
    ];
    $repeatReq2 += $repeatReq1;
    $stagesArr = [
        'stage_length_1' => $repeatReq1, 
        'stage_length_2' => $repeatReq2,
        'stage_length_3' => $repeatReq2, 
        'stage_length_4' => $repeatReq2, 
        'stage_length_5' => $repeatReq2,
        'stage_length_6' => $repeatReq2, 
        'stage_length_7' => $repeatReq2, 
        'stage_length_8' => $repeatReq2, 
        'stage_length_9' => $repeatReq2, 
        'stage_length_10' => $repeatReq2, 
    ];
    $requirements = [
        'stage_1' => $stagesArr,
        'stage_2' => $stagesArr,
        'stage_3' => $stagesArr,
        'stage_4' => $stagesArr,
        'stage_5' => $stagesArr,
    ];
    #file_put_contents('.log', json_encode($requirements)."\n", FILE_APPEND);
    $fields_to_calculate = $keys_to_calculate = [];
    $areRequirementsMet = true;
    if ( isset($requirements[$stage][$stage_length][$field]) ){
        $requirement = $requirements[$stage][$stage_length][$field];
        foreach($requirement as $req){
            #file_put_contents('.log', json_encode($req)."\n", FILE_APPEND);
            if ( stripos($req, '.') !== false ){
                $parts = explode('.', $req);
                if ( count($parts) == 2 ){
                    if ( $parts[0] == 'footer' ){
                        $position = $parts[0];
                        $k = $parts[1];
                        $key = $inputs[$position][$k];
                    } else{
                        $sl = $parts[0];
                        $k = $parts[1];
                        $key = $inputs[$position][$stage][$sl][$k];
                    }
                }
            } else{
                $key = $inputs[$position][$stage][$stage_length][$req];
            }
            
            if ( !isset($key) ){
                $areRequirementsMet = false;
                $keys_to_calculate[] = $req;
            } else{
                $fields_to_calculate[$req] = $key;
            }
        }
    }
    if ( $areRequirementsMet === false ){ $fields_to_calculate = implode(', ', $keys_to_calculate); }
    return ['req' => $areRequirementsMet, 'fields' => $fields_to_calculate];
}

function areRequirementsMetForFooter(string $position, string $stage, string $field, array $inputs){
    $requirements = [
        'w/w yield' => ['qty out', 'density', 'body.stage_length_1.qty'],
        'mol' => ['mw', 'w/w yield'],
        'mol yield' => ['mol', 'body.stage_length_1.mol'],
        'actual cc/kg api' => ['api cc'],
    ];
    $fields_to_calculate = $keys_to_calculate = [];
    $areRequirementsMet = true;
    if ( isset($requirements[$field]) ){
        $requirement = $requirements[$field];
        foreach($requirement as $req){
            if ( stripos($req, '.') !== false ){
                $parts = explode('.', $req);
                if ( count($parts) == 3 ){
                    $pos = $parts[0];
                    $sl = $parts[1];
                    $k = $parts[2];
                    $key = $inputs[$pos][$stage][$sl][$k];
                }
            } else{
                $key = $inputs[$position][$req];
            }

            if ( !isset($key) ){
                $areRequirementsMet = false;
                $keys_to_calculate[] = $req;
            } else{
                $fields_to_calculate[$req] = $key;
            }
        }
    }
    if ( $areRequirementsMet === false ){ $fields_to_calculate = implode(', ', $keys_to_calculate); }
    return ['req' => $areRequirementsMet, 'fields' => $fields_to_calculate];
}


function calculateField($position, $stage, $fieldToCalculate, $dependentFields, $inputs, $stage_length){
    $calculation = null;
    #BODY
    if ( $position == 'body' ){
        if ( $fieldToCalculate == 'mol' ){
            $calculation = $dependentFields['cc/kg input'] * (1000 / $dependentFields['mw']);
        } 
        else if ( $fieldToCalculate == 'mol ratio' ){
            $calculation = $dependentFields['mol'] / $inputs[$position][$stage]['stage_length_1']['mol'];
        } 
        else if ( $fieldToCalculate == 'cc/kg input' ){
            $calculation = ($dependentFields['qty'] * $dependentFields['density']) / $inputs[$position][$stage]['stage_length_1']['qty'];
        } 
        else if ( $fieldToCalculate == 'cc/kg output' ){
            if ( $inputs['footer']['w/w yield'] > 0 ){
                $calculation = $dependentFields['cc/kg input'] / $inputs['footer']['w/w yield'];
            }
        } 
        else if ( $fieldToCalculate == 'cc/kg api' ){
            $calculation = $dependentFields['cc/kg output'] * $inputs['footer']['api cc'];
        }
        else if ( $fieldToCalculate == 'actual cc/kg api' ){
            $calculation = $dependentFields['cc/kg output'] * $inputs['footer']['api cc'];
        }
        else if ( $fieldToCalculate == 'cost' ){
            $calculation = $dependentFields['price/kg'] * $dependentFields['actual cc/kg api'];
        } 
    }
    #FOOTER
    else if ( $position == 'footer' ){
        if ( $fieldToCalculate == 'w/w yield' ){
            $calculation = ($dependentFields['qty out'] * $dependentFields['density']) / $inputs['body'][$stage]['stage_length_1']['qty'];
        } 
        else if ( $fieldToCalculate == 'mol' ){
            $calculation = $dependentFields['w/w yield'] * (1000 / $dependentFields['mw']);
        } 
        else if ( $fieldToCalculate == 'mol yield' ){
            $calculation = $dependentFields['mol'] / $inputs['body'][$stage]['stage_length_1']['mol'];
        }
        else if ( $fieldToCalculate == 'actual cc/kg api' ){
            #file_put_contents('.log', json_encode($dependentFields)."\n", FILE_APPEND);
            $calculation = $dependentFields['api cc'];
        }
    }
    return number_format($calculation, 2, '.', '');
}