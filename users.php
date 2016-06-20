<?php

/* * *****************************************************************************************
 * Get OTP for Mobile Verification                                                                         
 * url - /getotp																		  
 * method - POST																		   
 * params - mobile_number  
 * ***************************************************************************************** */

function getOTP() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $mobile_number = $jsondata->mobile_number;
    try {
	$UserDAO = new UserDAO();
	$isMobileExists = $UserDAO->isMobileExists($mobile_number); // Check if mobile number already register or not

	if (!empty($isMobileExists)) {
	    $result['success'] = '0';
	    $result['message_id'] = "3";
	    $result['message_display'] = "1";
	    echo json_encode($result);
	} else {
	    $dataArray = $UserDAO->getOTP();
	    $result['success'] = '1';
	    $result['response'] = $dataArray;
	    echo json_encode($result);
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * User Signup                                                                           
 * url - /signup 																		  
 * method - POST																		   
 * params - fname, lname, email, password, mobile_number, mobile_verification_code, device_id, gcm_id  
 * ***************************************************************************************** */

function signup() {

    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $fname = $jsondata->fname;
    $lname = $jsondata->lname;
    $email = $jsondata->email;
    $password = $jsondata->password;
    $contact_no = $jsondata->mobile_number;
    $mob_ver_code = $jsondata->mobile_verification_code;
    $device_id = $jsondata->device_id;
    $gcm_id = $jsondata->gcm_id;

    try {
	$UserDAO = new UserDAO();
	$user_id = $UserDAO->SignupUser($fname, $lname, $email, $password, $contact_no, $mob_ver_code, $device_id, $gcm_id);

	if (!empty($user_id)) {
	    $dataArray = $UserDAO->getUserById($user_id);
	    $result['success'] = '1';
	    $result['message_id'] = "2";
	    $result['message_display'] = "1";
	    $result['response'] = $dataArray;
	    echo json_encode($result);
	} else {
	    $result['success'] = '0';
	    $result['message_id'] = "1";
	    $result['message_display'] = "1";
	    echo json_encode($result);
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Get Users Login                                                                       
 * url - /login 																  
 * method - POST																		 
 * params - mobile_number, password                                                     
 * ***************************************************************************************** */

function login() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $mobile_number = $jsondata->mobile_number;
    $password = $jsondata->password;
    try {
	$UserDAO = new UserDAO();
	$isMobileExists = $UserDAO->isMobileExists($mobile_number); // Check if mobile number register or not

	if (empty($isMobileExists)) {
	    $result['success'] = '0';
	    $result['message_id'] = "4";
	    $result['message_display'] = "1";
	    echo json_encode($result);
	} else {
	    $dataArray = $UserDAO->getUserLogin($mobile_number, $password);
	    if (empty($dataArray)) {
		$result['success'] = '0';
		$result['message_id'] = "5";
		$result['message_display'] = "1";
		echo json_encode($result);
	    } else {
		$result['success'] = '1';
		$result['message_id'] = "6";
		$result['message_display'] = "1";
		$result['response'] = $dataArray;
		echo json_encode($result);
	    }
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Save User's search Request                                                                       
 * url - /searchRequest 																  
 * method - POST																		 
 * params - user_id, source_lat, source_lng, destination_lat, destination_lng
 * ***************************************************************************************** */

function searchRequest() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $user_id = $jsondata->user_id;
    $source_lat = $jsondata->source_lat;
    $source_lng = $jsondata->source_lng;
    $destination_lat = $jsondata->destination_lat;
    $destination_lng = $jsondata->destination_lng;

    try {
	$UserDAO = new UserDAO();

	// Inactive all previous requests of this user
	//$inactivePreviousRequest = $UserDAO->inactivePreviousRequest($user_id);
	//Calculate the distance of the given coordinates
	$distance = getDrivingDistance($source_lat, $source_lng, $destination_lat, $destination_lng);

	// save this request in the database for the other app users
	$saveSearchRequest = $UserDAO->saveSearchRequest($user_id, $source_lat, $source_lng, $destination_lat, $destination_lng, $distance);
	$openRequestArray = array();

	// check the request has been saved in the database or not
	if ($saveSearchRequest == true) {
	    $source_lat_start = round($source_lat, 4);
	    $source_lat_end = $source_lat + 0.0005;
	    $source_lng_start = round($source_lng, 4);
	    $source_lng_end = $source_lng + 0.0005;
	    $openRequests = $UserDAO->getOpenRequest($user_id, $source_lat_start, $source_lat_end, $source_lng_start, $source_lng_end);
	    print_r($openRequests);
	    die;
	    foreach ($openRequests as $openRequest) {
		$destination_lat_start = round($openRequest['destination_lat'], 4);
		$destination_lat_end = $destination_lat_start + 0.0003;
		$destination_lng_start = round($openRequest['destination_lng'], 4);
		$destination_lng_end = $destination_lng_start + 0.0003;

		//Condtions for find Exact Match travellers
		//if ($destination_lat_start <= $destination_lat AND $destination_lat <= $destination_lat_end AND $destination_lng_start <= $destination_lng AND $destination_lng <= $destination_lng_end) {
		$openRequestArray[] = $openRequest;
		//} 
		//$openRequestArray[] = $request;
	    }
	    $result['success'] = '1';
	    $result['response'] = $openRequestArray;
	    echo json_encode($result);
	} else {
	    $result['success'] = '0';
	    $result['message_id'] = "1";
	    $result['message_display'] = "1";
	    echo json_encode($result, true);
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Save User's search Request                                                                       
 * url - /searchRequest 																  
 * method - POST																		 
 * params - user_id, source_lat, source_lng, destination_lat, destination_lng
 * ***************************************************************************************** */

function searchRequestRefresh() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $user_id = $jsondata->user_id;
    $source_lat = $jsondata->source_lat;
    $source_lng = $jsondata->source_lng;
    $destination_lat = $jsondata->destination_lat;
    $destination_lng = $jsondata->destination_lng;
    $refresh = $jsondata->refresh;

    try {
	$UserDAO = new UserDAO();


	//Calculate the distance of the given coordinates
	$distance = getDrivingDistance($source_lat, $source_lng, $destination_lat, $destination_lng);

	$openRequestArray = array();

	$source_lat_start = round($source_lat, 4);
	$source_lat_end = $source_lat + 0.0005;
	$source_lng_start = round($source_lng, 4);
	$source_lng_end = $source_lng + 0.0005;
	$openRequests = $UserDAO->getOpenRequest($user_id, $source_lat_start, $source_lat_end, $source_lng_start, $source_lng_end);
	print_r($openRequests);
	die;
	foreach ($openRequests as $openRequest) {
	    $destination_lat_start = round($openRequest['destination_lat'], 4);
	    $destination_lat_end = $destination_lat_start + 0.0003;
	    $destination_lng_start = round($openRequest['destination_lng'], 4);
	    $destination_lng_end = $destination_lng_start + 0.0003;

	    //Condtions for find Exact Match travellers
	    //if ($destination_lat_start <= $destination_lat AND $destination_lat <= $destination_lat_end AND $destination_lng_start <= $destination_lng AND $destination_lng <= $destination_lng_end) {
	    $openRequestArray[] = $openRequest;
	    //} 
	    //$openRequestArray[] = $request;
	}
	$result['success'] = '1';
	$result['response'] = $openRequestArray;
	echo json_encode($result);
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************                                                                     
 * Function for calculate driving distance and travel time duration:
 * params - source_lat, source_lng, destination_lat, destination_lng
 * ***************************************************************************************** */

function getDrivingDistance($source_lat, $source_lng, $destination_lat, $destination_lng) {
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyBZpNS8NFjSBgMa1UH6GPBSAvgRm4enJlA&sensor=true&libraries=places&origins=" . $source_lat . "," . $source_lng . "&destinations=" . $destination_lat . "," . $destination_lng . "&mode=driving&language=pl-PL";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
    $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
    $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
    return $dist;
}

/* * *****************************************************************************************                                                                     
 * Function for getting relative travellers on the same direction or route
 * params - user_id, source_lat, source_lng, destination_lat, destination_lng, distance, openrequest
 * ***************************************************************************************** */

function getRelativeMatch($user_id, $source_lat, $source_lng, $destination_lat, $destination_lng, $distance, $openRequest) {
    try {
	if ($distance > $openRequest['distance']) {

	    return '<script> initMap(' . $source_lat . ',' . $source_lng . ',' . $destination_lat . ',' . $destination_lng . ',' . $openRequest['destination_lat'] . ',' . $openRequest['destination_lng'] . ')</script>';

	    $result = "hiii";
	} else {
	    $result = "byee";
	}
	return $result;
    } catch (Exception $e) {
	// not a MySQL exception
	$e->getMessage();
    }
}

/* * *****************************************************************************************
 * Update User Information                                                                      
 * url - /updateUserDetail																  
 * method - POST																		 
 * params - user_id, first_name, last_name, email, mobile
 * ***************************************************************************************** */

function updateUserDetail() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $user_id = $jsondata->user_id;
    $first_name = $jsondata->first_name;
    $last_name = $jsondata->last_name;
    $email_id = $jsondata->email_id;
    $mobile_number = $jsondata->mobile_number;

    try {
	$UserDAO = new UserDAO();
	$dataArray = $UserDAO->updateUserDetail($user_id, $first_name, $last_name, $email_id, $mobile_number);
	if (!$dataArray) {
	    $result['response'] = false;
	    $result['error_status'] = '1';
	    $result['status_message'] = "Error";
	    $result['display_message'] = "1";
	    $result['error_code'] = "004";
	    echo json_encode($result);
	} else {
	    $result['response'] = true;
	    $result['error_status'] = '0';
	    $result['status_message'] = "Details updated successfully.";
	    $result['display_message'] = "1";
	    $result['error_code'] = "000";
	    echo json_encode($result);
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Edit User Profile Image                                                                **
 * url - /editProfilePic																  **
 * method - POST																		  **
 * params - user_id,imageurl                                                              **
 * ***************************************************************************************** */

function editProfilePic() {

    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondate = json_decode($body);
    $user_id = $jsondate->user_id;
    $imageurl = $jsondate->image_url;

    try {
	$UserDAO = new UserDAO();
	$status = $UserDAO->editProfilePic($imageurl, $user_id);
	if (!$status) {
	    $result['response'] = false;
	    $result['error_status'] = '1';
	    $result['status_message'] = "Unable To Update this time";
	    $result['display_message'] = "1";
	    $result['error_code'] = "004";
	    echo json_encode($result);
	} else {
	    $result['response'] = true;
	    $result['error_status'] = '0';
	    $result['status_message'] = "Profile Image Sucessfully Updated";
	    $result['display_message'] = "1";
	    $result['error_code'] = "000";
	    echo json_encode($result);
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Facebook Login                                                                           
 * url - /facebookLogin 																		  
 * method - POST																		   
 * params - facebookID, email, name, image_url, access_token, device_id, gcm_id
 * ***************************************************************************************** */

function facebookLogin() {

    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $facebookID = $jsondata->facebookID;
    $email = $jsondata->email;
    $name = $jsondata->name;
    $image_url = $jsondata->image_url;
    $access_token = $jsondata->access_token;
    $device_id = $jsondata->device_id;
    $gcm_id = $jsondata->gcm_id;

    try {
	$UserDAO = new UserDAO();
	$isEmail = $UserDAO->isEmailExists($email);
	if (!empty($isEmail)) {
	    $updateStatus = $UserDAO->UpdateUserInfoViaFB($isEmail[0]['user_id'], $facebookID, $email, $name, $image_url, $access_token, $device_id, $gcm_id);
	    $dataArray = $UserDAO->getUserById($isEmail[0]['user_id']);

	    $result['response'] = $dataArray;
	    $result['error_status'] = '0';
	    $result['status_message'] = "User info updated successfully.";
	    $result['display_message'] = "0";
	    $result['error_code'] = "000";
	    echo json_encode($result);
	} else {
	    $inserted_Id = $UserDAO->SignupUserViaFB($facebookID, $email, $name, $image_url, $access_token, $device_id, $gcm_id);
	    $dataArray = $UserDAO->getUserById($inserted_Id);

	    $result['response'] = $dataArray;
	    $result['error_status'] = '0';
	    $result['status_message'] = "Register Successfully";
	    $result['display_message'] = "0";
	    $result['error_code'] = "000";
	    echo json_encode($result);
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Google Plus Login                                                                           
 * url - /googlePlusLogin 																		  
 * method - POST																		   
 * params - google_ID, email, name, image_url, access_token, device_id, gcm_id
 * ***************************************************************************************** */

function googlePlusLogin() {

    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $google_ID = $jsondata->google_ID;
    $email = $jsondata->email;
    $name = $jsondata->name;
    $image_url = $jsondata->image_url;
    $access_token = $jsondata->access_token;
    $device_id = $jsondata->device_id;
    $gcm_id = $jsondata->gcm_id;

    try {
	$UserDAO = new UserDAO();
	$isEmail = $UserDAO->isEmailExists($email);
	if (!empty($isEmail)) {
	    $updateStatus = $UserDAO->UpdateUserInfoViaGoogle($isEmail[0]['user_id'], $google_ID, $email, $name, $image_url, $access_token, $device_id, $gcm_id);
	    $dataArray = $UserDAO->getUserById($isEmail[0]['user_id']);

	    $result['response'] = $dataArray;
	    $result['error_status'] = '0';
	    $result['status_message'] = "User info updated successfully.";
	    $result['display_message'] = "0";
	    $result['error_code'] = "000";
	    echo json_encode($result);
	} else {
	    $inserted_Id = $UserDAO->SignupUserViaGoogle($google_ID, $email, $name, $image_url, $access_token, $device_id, $gcm_id);
	    $dataArray = $UserDAO->getUserById($inserted_Id);

	    $result['response'] = $dataArray;
	    $result['error_status'] = '0';
	    $result['status_message'] = "Register Successfully";
	    $result['display_message'] = "0";
	    $result['error_code'] = "000";
	    echo json_encode($result);
	}
    } catch (PDOException $e) {
	echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}
?>