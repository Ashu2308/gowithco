<?php
/* * ********** Database Calls Files Inclusion *********** */
require_once "DAO/DatabaseDAO.php";
require_once "DAO/UserDAO.php";
require_once "DAO/MessageDAO.php";
require_once "DAO/ChatDAO.php";

/* * ********** API Calls Files Inclusion *********** */
require_once "users.php";
require_once "message.php";
require_once "chat.php";

error_reporting(E_ALL);

/* * **** Php Slim Loader  ***** */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

/* * ***********************     Define Urls Here   ****************************** */

/* * **********   Get Messages *********** */
$app->get('/messages', 'messages'); //   Get All messages from the Database

/* * **********   Users *********** */
$app->post('/getotp', 'getOTP'); //   OTP Generation process
$app->post('/signup', 'signup'); //   User Signup Process
$app->post('/login', 'login'); //   User Login Process

/* * **********   User Post/Search Request *********** */
$app->post('/searchRequest', 'searchRequest'); //   Get All open request
$app->post('/searchRequestRefresh', 'searchRequestRefresh'); //   Refresh the previous search request for open request

/* * **********   User send Chat Request *********** */
//$app->post('/chatRequest', 'chatRequest'); //   send a request for chat
//$app->post('/chatAccept', 'chatAccept'); //   accept a request for chat

/* * ********** Run The SLIM Application *********** */
$app->run(); // Run the app

?>