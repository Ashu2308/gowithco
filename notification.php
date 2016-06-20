<?php

// API access key from Google API's Console
define('API_ACCESS_KEY', 'AIzaSyDhJSKdHziV3BmXyXZuceeP4dDI8PoNBuM');
include_once('DAO/UserDAO.php');

$user_id = $_GET['user_id'];
$UserDAO = new UserDAO();
$userArray = $UserDAO->getUserById($user_id);

$registrationIds = array($userArray['gcm_id']);
// prep the bundle
$msg = array
    (
    'message' => 'Someone wants to go with you at sme location. Please accept request.',
);
$fields = array
    (
    'registration_ids' => $registrationIds,
    'data' => $msg
);

$headers = array
    (
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
$result = curl_exec($ch);
curl_close($ch);
echo $result;
?>