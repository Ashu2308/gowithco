<?php

require_once "DatabaseDAO.php";

class UserDAO {

    // Function for generate four digit OTP
    function getOTP($mobile_number) {
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
	    $database->query("select * from " . TBL_USERS . " where mobile_number = :mobile_number");
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
    function SignupUser($fname, $lname = '', $email='', $password, $mobile_number, $mobile_verification_code, $device_id, $gcm_id) {
	try {
	    $database = new Database();
	    $database->query("INSERT INTO " . TBL_USERS . " (`first_name`, `last_name`, `email_id`, `password`, `mobile_number`, `mobile_verification_code`, `device_id`, `gcm_id`, login_with, created_date) "
		    . "VALUES (:fname, :lname, :email, :password, :mobile_number, :mobile_verification_code, :device_id, :gcm_id, '1', now())");
	    $database->bind(':fname', $fname);
	    $database->bind(':lname', $lname);
	    $database->bind(':email', $email);
	    $database->bind(':password', md5($password));
	    $database->bind(':mobile_number', $mobile_number);
	    $database->bind(':mob_ver_code', $mobile_verification_code);
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

    //Function for getting the user information
    function getUserById($user_id) {
	try {
	    $database = new Database();
	    $database->query("select * from " . TBL_USERS . " where user_id= :user_id");
	    $database->bind(':user_id', $user_id);
	    $result = $database->resultset();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function updateUserInfo($user_id, $address1, $address2, $city, $state) {
	try {
	    $database = new Database();
	    $database->query("UPDATE users SET address1= '$address1', address2= '$address2', city= '$city', state= '$state' WHERE user_id= '$user_id'");
	    $database->bind(':user_id', $user_id);
	    $database->bind(':address1', $address1);
	    $database->bind(':address2', $address2);
	    $database->bind(':city', $city);
	    $database->bind(':state', $state);
	    $result = $database->execute();
	    $database = null;
	    return $result;
	} catch (Exception $e) {
	    // not a MySQL exception
	    $e->getMessage();
	}
    }

    function getUsersLogin($userinfo, $password) {
	try {
	    $database = new Database();
	    $database->query("select * from users where ( email_id= :userinfo OR mobile_number = :userinfo) and password= :password");
	    $database->bind(':userinfo', $userinfo);
	    $database->bind(':password', md5($password));
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

    function updateUserDetails($user_id, $first_name, $last_name, $email_id, $mobile_number) {
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