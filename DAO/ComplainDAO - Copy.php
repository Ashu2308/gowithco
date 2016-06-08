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
            $database->query("SELECT * FROM `hr_complain` WHERE area_id=:area_id AND resolve_status='0'");
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
            $database->query("SELECT * FROM `hr_complain` WHERE area_id=:area_id AND resolve_status='1'");
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