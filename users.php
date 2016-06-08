/<?php

/* * *****************************************************************************************
 * User Signup                                                                           
 * url - /Signup 																		  
 * method - POST																		   
 * params - fname, lname, email, password, contact_no, mob_ver_code, device_id, gcm_id  
 * ***************************************************************************************** */

function Signup() {

    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $fname = $jsondata->fname;
    $lname = $jsondata->lname;
    $email = $jsondata->email;
    $password = $jsondata->password;
    $contact_no = $jsondata->contact_no;
    $mob_ver_code = $jsondata->mob_ver_code;
    $device_id = $jsondata->device_id;
    $gcm_id = $jsondata->gcm_id;

    try {
        $UserDAO = new UserDAO();
        $isEmail = $UserDAO->isEmailExists($email);
        $isMobile = $UserDAO->isMobileExists($contact_no);

        if (!empty($isEmail)) {
            $result['error_status'] = '1';
            $result['status_message'] = "Email already exists.";
            $result['display_message'] = "1";
            $result['error_code'] = "001";
            echo json_encode($result);
        } elseif (!empty($isMobile)) {
            $result['error_status'] = '1';
            $result['status_message'] = "Mobile no already exists.";
            $result['display_message'] = "1";
            $result['error_code'] = "002";
            echo json_encode($result);
        } else {
            $inserted_Id = $UserDAO->SignupUser($fname, $lname, $email, $password, $contact_no, $mob_ver_code, $device_id, $gcm_id);
            $dataArray = $UserDAO->getUserById($inserted_Id);

            $result['response'] = $dataArray;
            $result['error_status'] = '0';
            $result['status_message'] = "Register Successfully";
            $result['display_message'] = "1";
            $result['error_code'] = "000";
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * User Information Updation                                                                 
 * url - /Signup 																		  
 * method - POST																		 
 * params - fname, lname, email, password, contact_no, verification code, device_id, gcm_id   
 * ***************************************************************************************** */

function updateUserInfo() {

    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $user_id = $jsondata->user_id;
    $address1 = $jsondata->address1;
    $address2 = $jsondata->address2;
    $city = $jsondata->city;
    $state = $jsondata->state;

    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->updateUserInfo($user_id, $address1, $address2, $city, $state);

        if (!$dataArray) {
            $result['error_status'] = '1';
            $result['status_message'] = "Information not updated";
            $result['display_message'] = "1";
            $result['error_code'] = "005";
            echo json_encode($result);
        } else {
            $result['response'] = true;
            $result['error_status'] = '0';
            $result['status_message'] = "Information updated successfully";
            $result['display_message'] = "0";
            $result['error_code'] = "000";
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Get Users Login                                                                       
 * url - /getUsersLogin 																  
 * method - POST																		 
 * params - email or mobile, password                                                     
 * ***************************************************************************************** */

function getUsersLogin() {
	$app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $userinfo = $jsondata->userinfo;
    $password = $jsondata->password;
    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->getUsersLogin($userinfo, $password);
        $result['response'] = $dataArray;
        if (empty($dataArray)) {
            $result['error_status'] = '1';
            $result['status_message'] = "Invalid Login Details";
            $result['display_message'] = "1";
            $result['error_code'] = "003";
            echo json_encode($result);
        } else {
            $result['error_status'] = '0';
            $result['status_message'] = "Login successful";
            $result['display_message'] = "1";
            $result['error_code'] = "000";
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Verify Mobile code                                                                       
 * url - /verifyMobCode 							
 * method - GET									
 * params - user_id, Verification Code                                                  
 * ***************************************************************************************** */

function verifyMobCode($user_id, $code) {
    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->verifyMobCode($user_id, $code);

        if (empty($dataArray)) {
            $result['error_status'] = '1';
            $result['status_message'] = "Code not match!";
            $result['display_message'] = "1";
            $result['error_code'] = "003";
            echo json_encode($result);
        } else {
            $UserDAO->updateMobVerificationStatus($user_id);
            $result['response'] = true;
            $result['error_status'] = '0';
            $result['status_message'] = "Mobile verify successfully.";
            $result['display_message'] = "0";
            $result['error_code'] = "000";
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Get Users Login                                                                       
 * url - /getUsersLogin 																  
 * method - POST																		 
 * params - email or mobile, password                                                     
 * ***************************************************************************************** */

function getUserDetails($user_id) {
    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->getUserDetails($user_id);
        $result['response'] = $dataArray;
        if (empty($dataArray)) {
            $result['error_status'] = '1';
            $result['status_message'] = "Error";
            $result['display_message'] = "1";
            $result['error_code'] = "004";
            echo json_encode($result);
        } else {
            $result['error_status'] = '0';
            $result['status_message'] = "Success";
            $result['display_message'] = "0";
            $result['error_code'] = "000";
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/* * *****************************************************************************************
 * Get Users Login                                                                       
 * url - /getUsersLogin 																  
 * method - POST																		 
 * params - email or mobile, password                                                     
 * ***************************************************************************************** */

function updateUserDetails() {
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
        $dataArray = $UserDAO->updateUserDetails($user_id, $first_name, $last_name, $email_id, $mobile_number);
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
 * Get Users Adresses List                                                                       
 * url - /userAddressesList 																  
 * method - GET																		 
 * params - user_id                                                  
 * ***************************************************************************************** */

function userAddressesList($user_id) {
    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->userAddressesList($user_id);
        $result['response'] = $dataArray;
        if (empty($dataArray)) {
            $result['error_status'] = '1';
            $result['status_message'] = "No Data";
            $result['display_message'] = "0";
            $result['error_code'] = "004";
            echo json_encode($result);
        } else {
            $result['error_status'] = '0';
            $result['status_message'] = "Success";
            $result['display_message'] = "0";
            $result['error_code'] = "000";
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}


/* * *****************************************************************************************
 * Add new address                                                                       
 * url - /addNewUserAddress 																  
 * method - POST																		 
 * params -  address1, address2, city, pincode, user_id                                                 
 * ***************************************************************************************** */

function addNewUserAddress() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $address1 = $jsondata->address1;
    $address2 = $jsondata->address2;
    $city = $jsondata->city;
    $pincode = $jsondata->pincode;
    $user_id = $jsondata->user_id;
    
    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->addNewUserAdress($address1, $address2, $city, $pincode, $user_id);
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
 * Remove address                                                                       
 * url - /removeUserAddress 																  
 * method - GET																		 
 * params - user_address_id                                                 
 * ***************************************************************************************** */

function removeUserAddress($user_address_id) {
    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->removeUserAddress($user_address_id);
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
 * Edit user address                                                                       
 * url - /editUserAddress 																  
 * method - POST																		 
 * params -  address1, address2, city, pincode, user_address_id                                                 
 * ***************************************************************************************** */

function editUserAddress() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $body = $request->getBody();
    $jsondata = json_decode($body);
    $address1 = $jsondata->address1;
    $address2 = $jsondata->address2;
    $city = $jsondata->city;
    $pincode = $jsondata->pincode;
    $user_address_id = $jsondata->user_address_id;
    
    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->editUserAddress($address1, $address2, $city, $pincode, $user_address_id);
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
 * Remove address                                                                       
 * url - /makeDefaultUserAddress 																  
 * method - GET																		 
 * params - user_address_id, user_id                                                
 * ***************************************************************************************** */

function makeDefaultUserAddress($user_address_id, $user_id) {
    try {
        $UserDAO = new UserDAO();
        $dataArray = $UserDAO->makeDefaultUserAddress($user_address_id, $user_id);
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
            $result['status_message'] = "Success";
            $result['display_message'] = "1";
            $result['error_code'] = "000";
            echo json_encode($result);
        }
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/*******************************************************************************************
 * Edit User Profile Image                                                                **
 * url - /editProfilePic																  **
 * method - POST																		  **
 * params - user_id,imageurl                                                              **
 *******************************************************************************************/


function editProfilePic()
{

    $app      = \Slim\Slim::getInstance();
    $request  = $app->request();
    $body     = $request->getBody();
    $jsondate = json_decode($body);
    $user_id  = $jsondate->user_id;
    $imageurl = $jsondate->image_url;

    try {
        $UserDAO            = new UserDAO();
        $status            = $UserDAO->editProfilePic($imageurl, $user_id);
        if (!$status) {
            $result['response'] = false;
            $result['error_status']    = '1';
            $result['status_message']  = "Unable To Update this time";
            $result['display_message'] = "1";
            $result['error_code']      = "004";
            echo json_encode($result);
        } else {
            $result['response'] = true;
            $result['error_status']    = '0';
            $result['status_message']  = "Profile Image Sucessfully Updated";
            $result['display_message'] = "1";
            $result['error_code']      = "000";
            echo json_encode($result);
        }
    }

    catch (PDOException $e) {
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