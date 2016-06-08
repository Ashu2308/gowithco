<?php
/* * ********** Database Calls Files Inclusion *********** */

require_once "DAO/DatabaseDAO.php";
//require_once "DAO/UserDAO.php";
require_once "DAO/MessageDAO.php";

/* * ********** API Calls Files Inclusion *********** */

require_once "users.php";
require_once "message.php";

error_reporting(E_ALL);

/* * **** Php Slim Loader  ***** */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

/* * ***********************     Define Urls Here   ****************************** */

/* * **********   Users *********** */
$app->post('/Signup', 'Signup'); //   User Login
//$app->get('/getUserLogout/:user_id/:latitude/:longitude', 'getUserLogout'); //   User Logout
//$app->get('/updateGCMID/:user_id/:gcm_id', 'updateGCMID'); //   Upadate GCM

/* * **********   Get Messages *********** */
$app->post('/Messages', 'Messages'); //   Get All messages from the Database

/* * **********   Complain  *********** 
$app->get('/resolveComplain/:user_id/:complain_id/:complain_category_id/:latitude/:longitude', 'resolveComplain'); //   Resolve complain
$app->get('/getAllComplainCategory', 'getAllComplainCategory'); //   Get all complain category
$app->get('/getAllPendingComplain/:area_id', 'getAllPendingComplain'); //   Get all pending complains
$app->get('/getAllResolvedComplain/:area_id', 'getAllResolvedComplain'); //   Get all pending complains

/* * ********** Run The SLIM Application *********** */

$app->run(); // Run the app

?>