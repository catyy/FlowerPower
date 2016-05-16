<?php
session_start();
// include ERPLY API class
//include("EAPIclass.php");
include("EAPI.class.php");

// Initialise class
$api = new EAPI();

$api->clientCode = "338589";
$api->username = "support";
$api->password = "Nm1ev8mSN0w4j6WD";


/*
if (isset($_GET['clientCode'])) {

    $api->clientCode = $_GET['clientCode'];
    $sessionKey = $_GET['apiSessionKey'] ;
    $_SESSION['clientCode'] = $_GET['clientCode'];
    $_SESSION['apiSessionKey'] = $_GET['apiSessionKey'];

}else{
    $api->clientCode = $_SESSION['clientCode'];
    $sessionKey = $_SESSION['apiSessionKey'] ;
    //?apiSessionKey=WRfn22938c0f79e7ddf5a56ff53a000979a638103627&clientCode=338589

}
*/


$api->url = "https://".$api->clientCode.".erply.com/api/";

