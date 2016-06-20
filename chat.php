<?php

/* * *****************************************************************************************
 * Get OTP for Mobile Verification                                                                         
 * url - /getotp																		  
 * method - POST																		   
 * params - mobile_number  
 * ***************************************************************************************** */

function chatRequest() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $sender_request_id = $jsondata->sender_request_id;
    $accept_request_id = $jsondata->accept_request_id;
    
    try {
	$ChatDAO = new ChatDAO();

	//Change Status of the sender from seeker to requestor
	$checkAcceptStatus = $ChatDAO->changeSenderStatus($sender_request_id);

	//Check if accept user is open or already patched-up with someone else
	$checkAcceptStatus = $ChatDAO->checkAcceptStatus($accept_request_id);

	switch ($checkAcceptStatus) {
	    case '0':
		$result['success'] = '1';
		$result['message_id'] = "10";
		$result['message_display'] = "1";
		echo json_encode($result);
		break;
	    
	     case '1':
		$result['success'] = '0';
		$result['message_id'] = "7";
		$result['message_display'] = "1";
		echo json_encode($result);
		break;
	    
	     case '2':
		$result['success'] = '0';
		$result['message_id'] = "8";
		$result['message_display'] = "1";
		echo json_encode($result);
		break;
	    
	     case '3':
		$result['success'] = '0';
		$result['message_id'] = "9";
		$result['message_display'] = "1";
		echo json_encode($result);
		break;
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

?>