<?php
session_start();
// include ERPLY API class
//include("EAPIclass.php");
include("EAPI.class.php");

// Initialise class
$api = new EAPI();

$api->clientCode = "";
$api->username = "";
$api->password = "";


/*
if (isset($_GET['clientCode'])) {

    $api->clientCode = $_GET['clientCode'];
    $sessionKey = $_GET['apiSessionKey'] ;
    $_SESSION['clientCode'] = $_GET['clientCode'];
    $_SESSION['apiSessionKey'] = $_GET['apiSessionKey'];

}else{
    $api->clientCode = $_SESSION['clientCode'];
    $sessionKey = $_SESSION['apiSessionKey'] ;
  

}
*/


$api->url = "https://".$api->clientCode.".erply.com/api/";

