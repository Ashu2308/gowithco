<?php
/* * ********** Database Calls Files Inclusion *********** */
require_once "DAO/DatabaseDAO.php";
require_once "DAO/UserDAO.php";
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
$app->post('/getotp', 'getOTP'); //   OTP Generation process
$app->post('/signup', 'signup'); //   User Signup Process

/* * **********   Get Messages *********** */
$app->post('/messages', 'messages'); //   Get All messages from the Database

/* * ********** Run The SLIM Application *********** */
$app->run(); // Run the app

?>