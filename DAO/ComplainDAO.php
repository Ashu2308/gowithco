<?php

require_once "DatabaseDAO.php";

class ComplainDAO {

    function resolveComplain($user_id, $complain_id, $complain_category_id, $resolve_latitude, $resolve_longitude) {
        try {
            $database = new Database();
            $database->query("UPDATE `hr_complain` SET `complain_category_id` = :complain_category_id, `resolve_status` = '1', `resolve_latitude` = :resolve_latitude, `resolve_longitude` = :resolve_longitude, `resolve_by` = :user_id, `last_updated_by` = :user_id, `resolved_at` = now(), `updated_at` = now() 
                            WHERE `complain_id` = :complain_id");
            $database->bind(':user_id', $user_id);
            $database->bind(':complain_id', $complain_id);
            $database->bind(':complain_category_id', $complain_category_id);
            $database->bind(':resolve_latitude', $resolve_latitude);
            $database->bind(':resolve_longitude', $resolve_longitude);
            $result = $database->execute();
            $database = null;
            return $result;
        } catch (Exception $e) {
            // not a MySQL exception
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
    }

    function getAllComplainCategory() {
        try {
            $database = new Database();
            $database->query("SELECT * FROM `hr_complain_category`");
            $result = $database->resultset();
            $database = null;
            return $result;
        } catch (Exception $e) {
            // not a MySQL exception
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
    }

    function getAllPendingComplain($area_id) {
        try {
            $database = new Database();
            $database->query("SELECT comp.complain_id, comp.resolve_status, cust.customer_id, cust.name as customer_name, cust.phone as customer_phone, cust.email as customer_email, cust.address as customer_address, comp.created_at as complain_date, user.name as assignee_name
                            FROM hr_complain as comp 
                            INNER JOIN hr_customer as cust ON comp.customer_id=cust.customer_id 
                            INNER JOIN hr_user as user ON user.user_id=comp.assignee_id 
                            WHERE resolve_status='0' AND comp.area_id=:area_id");
            $database->bind(':area_id', $area_id);
            $result = $database->resultset();
            $database = null;
            return $result;
        } catch (Exception $e) {
            // not a MySQL exception
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
    }

    function getAllResolvedComplain($area_id) {
        try {
            $database = new Database();
            $database->query("SELECT comp_cat.desciption as complain_category_name, comp.complain_id, comp.resolve_status, cust.customer_id, cust.name as customer_name, cust.phone as customer_phone, cust.email as customer_email, cust.address as customer_address, comp.created_at as complain_date, comp.resolved_at as resolve_date, user.name as assignee_name
                            FROM hr_complain as comp 
                            INNER JOIN hr_customer as cust ON comp.customer_id=cust.customer_id 
                            INNER JOIN hr_user as user ON user.user_id=comp.assignee_id 
							INNER JOIN hr_complain_category as comp_cat ON comp.complain_category_id=comp_cat.complain_category_id
                            WHERE resolve_status='1' AND comp.area_id=:area_id");
            $database->bind(':area_id', $area_id);
            $result = $database->resultset();
            $database = null;
            return $result;
        } catch (Exception $e) {
            // not a MySQL exception
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
    }

}

//end of class
?>