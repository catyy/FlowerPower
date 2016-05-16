<?php
session_start();
error_reporting(E_ALL);
include("functions.php");



$date1 = null;
$date2 = null;
$interval = null;

if(isset($_POST['dateFrom'])){
    $dateFrom = strtotime($_POST['dateFrom']);
    $_SESSION['dateFrom']= $_POST['dateFrom'];
    $date1=date_create($_SESSION['dateFrom']);
}

if(isset($_POST['dateTo'])){
    $dateTo = strtotime($_POST['dateTo']);
    $_SESSION['dateTo']= $_POST['dateTo'];
    $date2=date_create($_SESSION['dateTo']);
    $interval = date_diff($date1,$date2);
}


if($interval != null && ($interval->format('%m') + $interval->format('%y')*12)>4){
    $_SESSION['datewarning']= "warning1";
    header('Location: index.php');
    die();
}


if($interval != null && ($interval->format('%m') + $interval->format('%y')*12)>1 &&$_POST['report'] == "POS"){
    $_SESSION['datewarning']= "warning2";
    header('Location: index.php');
    die();
}


if ($dateFrom > $dateTo ) {
    $_SESSION['datewarning']= "warning";
    header('Location: index.php');
    die();
}


if(isset($_POST['submit'])){
    $warehouse = array();
    $warehouse = $_POST['warehouse'];
    $_SESSION['warehouse']= $_POST['warehouse'];
}



if(isset($_POST['report'])){
    $report = $_POST['report'];
    switch ($report) {
        case "All":
            $_SESSION['stock'] = null;
            header('Location:allReports.php');
            exit();
        case "In":
            $_SESSION['inOut'] = "in";
            header('Location:transferInOutValue.php');
            exit();
        case "Out":
            $_SESSION['inOut'] = "out";
            header('Location:transferInOutValue.php');
            exit();
        case "POS":
            header('Location:pos.php');
            exit();
        case "Stock":
            $_SESSION['stock'] = "stock";
            header('Location:inStock.php');
            exit();
        default:
            echo "You have to pick report!";
            header('Location:index.php');
            exit();
    }
}


