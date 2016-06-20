<?php

require_once "DatabaseDAO.php";

class UserDAO {

    // Function for generate four digit OTP
    function getOTP() {
	try {
	    $result = mt_rand(1000, 9999);
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    // Function for checking mobile number already registered or not in our database
    function isMobileExists($mobile_number) {
	try {
	    $database = new Database();
	    $database->query("SELECT * FROM " . TBL_USERS . " WHERE mobile_number = :mobile_number");
	    $database->bind(':mobile_number', $mobile_number);
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    //Function for signup the user
    function SignupUser($fname, $gender, $birth_date, $password, $mobile_number, $mobile_verification_code, $device_id, $gcm_id, $lname = '', $email = '') {
	try {
	    $database = new Database();
	    $database->query("INSERT INTO " . TBL_USERS . " (`first_name`, `last_name`, `gender`, `birth_date`, `email_id`, `password`, `mobile_number`, `mobile_verification_code`, `device_id`, `gcm_id`, login_with, created_at) "
		    . "VALUES (:fname, :lname, :gender, :birth_date, :email, :password, :mobile_number, :mobile_verification_code, :device_id, :gcm_id, '1', now())");
	    $database->bind(':fname', $fname);
	    $database->bind(':lname', $lname);
	    $database->bind(':gender', $gender);
	    $database->bind(':birth_date', $birth_date);
	    $database->bind(':email', $email);
	    $database->bind(':password', md5($password));
	    $database->bind(':mobile_number', $mobile_number);
	    $database->bind(':mob_ver_code', $mobile_verification_code);
	    $database->bind(':device_id', $device_id);
	    $database->bind(':gcm_id', $gcm_id);
	    $result = $database->execute();
	    if (!empty($result)) {
		$insert_Id = $database->lastInsertId();
		return $insert_Id;
	    } else {
		return null;
	    }
	    $database = null;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    //Function for getting the travellers information
    function getUserById($user_id) {
	try {
	    $database = new Database();
	    $database->query("SELECT * FROM " . TBL_USERS . " WHERE user_id= :user_id");
	    $database->bind(':user_id', $user_id);
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    //Function for user login
    function getUserLogin($mobile_number, $password) {
	try {
	    $database = new Database();
	    $database->query("SELECT * FROM " . TBL_USERS . " WHERE mobile_number = :mobile_number and password= :password");
	    $database->bind(':mobile_number', $mobile_number);
	    $database->bind(':password', md5($password));
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    // Inactive all previous requests of this user
    function inactivePreviousRequest($user_id) {
	try {
	    $database = new Database();
	    $database->query("UPDATE " . TBL_USER_REQUEST . " SET `request_type` = '3' WHERE `user_id` =:user_id AND request_type ='0'");

	    $database->bind(':user_id', $user_id);
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    //Function for save travellers search request
    function saveSearchRequest($user_id, $source_lat, $source_lng, $destination_lat, $destination_lng, $distance) {
	try {
	    $database = new Database();
	    $database->query("INSERT INTO " . TBL_USER_REQUEST . " (`user_id`, `source_lat`, `source_lng`, `destination_lat`, `destination_lng`, `distance`, `request_type`, `requested_at`) "
		    . "VALUES (:user_id, :source_lat, :source_lng, :destination_lat, :destination_lng, :distance, '0', now())");
	    $database->bind(':user_id', $user_id);
	    $database->bind(':source_lat', $source_lat);
	    $database->bind(':source_lng', $source_lng);
	    $database->bind(':destination_lat', $destination_lat);
	    $database->bind(':destination_lng', $destination_lng);
	    $database->bind(':distance', $distance);
	    $result = $database->execute();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    // Function for getting list of all open request where source points are same(within 500 Mtrs)
    function getOpenRequest($user_id, $source_lat_start, $source_lat_end, $source_lng_start, $source_lng_end) {
	try {
	    $database = new Database();
	    $database->query("SELECT * FROM " . TBL_USER_REQUEST . " WHERE user_id!=:user_id AND request_type=:request_type AND ( source_lat BETWEEN '" . $source_lat_start . "' AND '" . $source_lat_end . "') AND (source_lng BETWEEN '" . $source_lng_start . "' AND '" . $source_lng_end . "') "
		    . " AND DATE_ADD(requested_at, INTERVAL 30 MINUTE) >= NOW(); ");
	    $database->bind(':user_id', $user_id);
	    $database->bind(':request_type', '0');

	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function verifyMobCode($user_id, $code) {
	try {
	    $database = new Database();
	    $database->query("select * from users where user_id=:user_id and mobile_verification_code=:code");
	    $database->bind(':user_id', $user_id);
	    $database->bind(':code', $code);
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function isEmailExists($email) {
	try {
	    $database = new Database();
	    $database->query("select * from " . TBL_USERS . " where email_id= :email");
	    $database->bind(':email', $email);
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function updateMobVerificationStatus($user_id) {
	try {
	    $database = new Database();
	    $database->query("UPDATE `users` SET `mobile_verification` = '1' WHERE `users`.`user_id` =:user_id");
	    $database->bind(':user_id', $user_id);
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function getUserDetails($user_id) {
	try {
	    $database = new Database();
	    $database->query("select * from users where user_id= :user_id");
	    $database->bind(':user_id', $user_id);
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function updateUserDetail($user_id, $first_name, $last_name, $email_id, $mobile_number) {
	try {
	    $database = new Database();
	    $database->query("UPDATE `users` SET `first_name` =:first_name, last_name=:last_name, email_id=:email_id, mobile_number=:mobile_number WHERE `user_id` =:user_id");
	    $database->bind(':user_id', $user_id);
	    $database->bind(':first_name', $first_name);
	    $database->bind(':last_name', $last_name);
	    $database->bind(':email_id', $email_id);
	    $database->bind(':mobile_number', $mobile_number);
	    $result = $database->execute();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function editProfilePic($imageurl, $user_id) {
	try {
	    $database = new Database();
	    $database->query("UPDATE `users` SET `photo_url` = :imageurl WHERE `user_id` = :user_id");
	    $database->bind(':user_id', $user_id);
	    $database->bind(':imageurl', $imageurl);
	    $result = $database->execute();
	    //$insert_Id = $database->lastInsertId();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function SignupUserViaFB($fb_user_id, $email, $name, $image_url, $access_token, $device_id, $gcm_id) {
	try {
	    $database = new Database();
	    $database->query("INSERT INTO `users` (`fb_user_id`, `first_name`, `email_id`, `photo_url`, `profile_access_token`, `device_id`, `gcm_id`, login_with, `created_date`) VALUES (:fb_user_id, :name, :email, :photo_url, :profile_access_token, :device_id, :gcm_id, '2', now())");
	    $database->bind(':fb_user_id', $fb_user_id);
	    $database->bind(':name', $name);
	    $database->bind(':email', $email);
	    $database->bind(':photo_url', $image_url);
	    $database->bind(':profile_access_token', $access_token);
	    $database->bind(':device_id', $device_id);
	    $database->bind(':gcm_id', $gcm_id);
	    $result = $database->execute();
	    $insert_Id = $database->lastInsertId();
	    $database = null;
	    return $insert_Id;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function UpdateUserInfoViaFB($user_id, $fb_user_id, $email, $name, $image_url, $access_token, $device_id, $gcm_id) {
	try {
	    $database = new Database();
	    $database->query("UPDATE `users` SET `fb_user_id` = :fb_user_id, `first_name` = :name, `photo_url` = :photo_url, `profile_access_token` = :profile_access_token, `device_id` = :device_id, `gcm_id` = :gcm_id WHERE `user_id` = :user_id");
	    $database->bind(':user_id', $user_id);
	    $database->bind(':fb_user_id', $fb_user_id);
	    $database->bind(':name', $name);
	    // $database->bind(':email', $email);
	    $database->bind(':photo_url', $image_url);
	    $database->bind(':profile_access_token', $access_token);
	    $database->bind(':device_id', $device_id);
	    $database->bind(':gcm_id', $gcm_id);
	    $result = $database->execute();
	    //$insert_Id = $database->lastInsertId();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    echo '{"error":{"text":' . $e->getMessage() . '}}';
	}
    }

    function SignupUserViaGoogle($google_ID, $email, $name, $image_url, $access_token, $device_id, $gcm_id) {
	try {
	    $database = new Database();
	    $database->query("INSERT INTO `users` (`gm_user_id`, `first_name`, `email_id`, `photo_url`, `profile_access_token`, `device_id`, `gcm_id`, login_with, `created_date`) VALUES (:google_ID, :name, :email, :photo_url, :profile_access_token, :device_id, :gcm_id, '3',  now())");
	    $database->bind(':google_ID', $google_ID);
	    $database->bind(':name', $name);
	    $database->bind(':email', $email);
	    $database->bind(':photo_url', $image_url);
	    $database->bind(':profile_access_token', $access_token);
	    $database->bind(':device_id', $device_id);
	    $database->bind(':gcm_id', $gcm_id);
	    $result = $database->execute();
	    $insert_Id = $database->lastInsertId();
	    $database = null;
	    return $insert_Id;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function UpdateUserInfoViaGoogle($user_id, $google_ID, $email, $name, $image_url, $access_token, $device_id, $gcm_id) {
	try {
	    $database = new Database();
	    $database->query("UPDATE `users` SET `gm_user_id` = :google_ID, `first_name` = :name, `photo_url` = :photo_url, `profile_access_token` = :profile_access_token, `device_id` = :device_id, `gcm_id` = :gcm_id WHERE `user_id` = :user_id");
	    $database->bind(':user_id', $user_id);
	    $database->bind(':google_ID', $google_ID);
	    $database->bind(':name', $name);
	    //$database->bind(':email', $email);
	    $database->bind(':photo_url', $image_url);
	    $database->bind(':profile_access_token', $access_token);
	    $database->bind(':device_id', $device_id);
	    $database->bind(':gcm_id', $gcm_id);
	    $result = $database->execute();
	    $insert_Id = $database->lastInsertId();
	    $database = null;
	    return $insert_Id;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

}

//end of class
?>