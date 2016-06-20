<?php

require_once "DatabaseDAO.php";

class MessageDAO {

    function getMessage() {
	try {
	    $database = new Database();
	    $database->query("select * from " . TBL_MESSAGES );
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

}
//end of class
?>