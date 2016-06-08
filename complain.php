<?php

/* * *****************************************************************************************
 * Resove complain                                                               
 * url - /resolveComplain 																  
 * method - GET																		 
 * params - user_id, complain_id, complain_category_id, resolve_location                                                   
 * ***************************************************************************************** */

function resolveComplain($user_id, $complain_id, $complain_category_id, $resolve_latitude, $resolve_longitude) {
    try {
        $ComplainDAO = new ComplainDAO();
        $status = $ComplainDAO->resolveComplain($user_id, $complain_id, $complain_category_id, $resolve_latitude, $resolve_longitude);
        if (!$status) {
            $result['response'] = false;
            $result['error_status'] = '1';
            $result['status_message'] = "Error";
            $result['display_message'] = "1";
            $result['error_code'] = "004";
            echo json_encode($result);
        } else {
            $result['response'] = true;
            $result['error_status'] = '0';
            $result['status_message'] = "Success";
            $result['display_message'] = "1";
            $result['error_code'] = "000";
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Get all complain category                                                               
 * url - /getAllComplainCategory 																  
 * method - GET																		 
 * params - None                                                  
 * ***************************************************************************************** */

function getAllComplainCategory() {
    try {
        $ComplainDAO = new ComplainDAO();
        $dataArray = $ComplainDAO->getAllComplainCategory();

        $result['response'] = $dataArray;
        $result['error_status'] = '0';
        $result['status_message'] = "Success";
        $result['display_message'] = "1";
        $result['error_code'] = "000";
        echo json_encode($result);
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Get all pending complain                                                               
 * url - /getAllPendingComplain 																  
 * method - GET																		 
 * params - area_id                                                  
 * ***************************************************************************************** */

function getAllPendingComplain($area_id) {
    try {
        $ComplainDAO = new ComplainDAO();
        $dataArray = $ComplainDAO->getAllPendingComplain($area_id);

        $result['response'] = $dataArray;
        $result['error_status'] = '0';
        $result['status_message'] = "Success";
        $result['display_message'] = "1";
        $result['error_code'] = "000";
        echo json_encode($result);
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Get all resolved complain                                                               
 * url - /getAllResolvedComplain 																  
 * method - GET																		 
 * params - area_id                                                  
 * ***************************************************************************************** */

function getAllResolvedComplain($area_id) {
    try {
        $ComplainDAO = new ComplainDAO();
        $dataArray = $ComplainDAO->getAllResolvedComplain($area_id);

        $result['response'] = $dataArray;
        $result['error_status'] = '0';
        $result['status_message'] = "Success";
        $result['display_message'] = "1";
        $result['error_code'] = "000";
        echo json_encode($result);
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

?>