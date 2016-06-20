<?php
require_once "DatabaseDAO.php";

class ChatDAO {
    
    //Change Status of the sender from seeker to requestor for this request
    function changeSenderStatus($request_id){
	try {
	    $database = new Database();
	    $database->query("UPDATE " . TBL_USER_REQUEST . " SET `request_type` = '1' WHERE `request_id` =:request_id AND request_type ='0'");
	    $database->bind(':request_id', $request_id);
	    
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    //Check if accept user is open or already patched-up with someone else for the requested destination
    function checkAcceptStatus($request_id){
	try {
	    $database = new Database();
	    $database->query("SELECT request_type FROM " . TBL_USER_REQUEST . " WHERE `request_id` =:request_id");
	    $database->bind(':request_id', $request_id);
	    
	    $result = $database->resultset();
	    $database = null;
	    return $result['request_type'];
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }
    
}

//end of class
?>