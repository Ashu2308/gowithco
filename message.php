<?php

/* * *****************************************************************************************
 * User Signup                                                                           
 * url - /Signup 																		  
 * method - POST																		   
 * params - fname, lname, email, password, contact_no, mob_ver_code, device_id, gcm_id  
 * ***************************************************************************************** */

function messages() {
    try {
        $MessageDAO = new MessageDAO();
	$dataArray = $MessageDAO->getMessage();
	
        if (!empty($dataArray)) {
            $result['success'] = '1';
            $result['response'] = $dataArray;
	    echo json_encode($result);
        } else {
            $result['success'] = '0';
	    $result['message_id'] = '1';
	    $result['message_display'] = '1';
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

?>