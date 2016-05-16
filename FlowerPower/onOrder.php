<?php
session_start();
error_reporting(E_ALL);
//include("functions.php");


function getOnOrderData($api){

    //$allWarehouses = $_SESSION['allWarehouses'];
    $dateFrom =  $_SESSION['dateFrom'];
    $dateTo =  $_SESSION['dateTo'];

    //if selected only one warehouse
    if($_SESSION['warehouse'] != null && $_SESSION['warehouse'] != "all"){

        $w = $_SESSION['warehouse'];
        $warehouse = explode(",", $w);
        $warehouse = $warehouse[0];
        $params =array("warehouseID" =>$warehouse,"type" => "PRCORDER", "status" => "READY","dateFrom"=>$dateFrom,"dateTo"=> $dateTo,"getRowsForAllInvoices" => 1);
        $output = apiSendRequest($api,"getPurchaseDocuments",$params);
        $showData = getPurchaseDocData($output);
    }else {
        //selected all warehouses
        $params =array("type" => "PRCORDER", "status" => "READY","dateFrom"=>$dateFrom,"dateTo"=> $dateTo,"getRowsForAllInvoices" => 1);
        $output = apiSendRequest($api,"getPurchaseDocuments",$params);
        $showData = getPurchaseDocData($output);
    }
    return $showData;
}


function getPurchaseDocData($data){

    $showData = array();
    if($data['status']['recordsTotal']<1)return;

    $purcheDocData = $data['records'];
    foreach($purcheDocData as $value){

        foreach($value['rows'] as $v){
            $totalNetSalesValue = $v['amount']*$v['price'];
            $showData[] = array("supplierID" => $value['supplierID'],"supplier" => $value['supplierName'],"code" => $v['code'],"itemName" => $v['itemName'], "amount" =>$v['amount'],"price" => $v['price'], "totalNetSalesValue" =>$totalNetSalesValue );
        }
    }
    return $showData;
}


function sumOrderData($data){

    $showData=array();

    foreach($data as $value){
        $id = $value["productID"];
        if(array_key_exists($id,$showData)){
            if($showData[$id]['price'] == $value['price']){
                $sold = $showData[$id]['soldQuantity'] + $value['soldQuantity'];
                $discount = $showData[$id]['discountTotal'] + $value['discountTotal'];
                $totalNetSales = $showData[$id]['totalNetSales'] + $value['totalNetSales'];

             //   $showData[$id] = array("supplierID" => $value['supplierID'],"supplier" => $value['supplierName'],"code" => $v['code'],"itemName" => $v['itemName'], "amount" =>$v['amount'],"price" => $v['price'], "totalNetSalesValue" =>$totalNetSalesValue );
            }

        }else{
           // $showData[$id] = array("supplierID" => $value['supplierID'],"supplier" => $value['supplierName'],"code" => $v['code'],"itemName" => $v['itemName'], "amount" =>$v['amount'],"price" => $v['price'], "totalNetSalesValue" =>$totalNetSalesValue );
        }
    }
    return $showData;
}

/*?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>On Order</title>
    <script src="Bootstrap/js/jquery-2.2.1.js"></script>
    <link href = "Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "Bootstrap/js/bootstrap.min.js"></script>

</head>

<body>
<div class="container-fluid"><br>
    <h1>Unfullfied Purchase Order Net Sales Value -  by Product</h1><br>

<table>
    <tr>
        <th>Locatin:</th>

        <?php
        if($_SESSION['warehouse'] =="all"){
            echo "<th>&nbsp;All warehouses</th></tr>";

        }else{
            $w = $_SESSION['warehouse'];
            $warehouse = explode(",", $w);
            echo "<th>&nbsp;".$warehouse[1]."</th></tr>";
        }  ?>
<br>
<tr>
    <th>Period:</th>
    <?php echo "<th>&nbsp;".$_SESSION['dateFrom']." - ".$_SESSION['dateTo']."</th></tr>" ?>
</tr>
</table>

<table class="table table-striped table-condensed" border="1" style="width:80%">
    <br><br>
    <tr>
        <th>Supplier</th>
        <th>Code</th>
        <th>Name</th>
        <th>Purchased quantity</th>
        <th>Net Sales Price</th>
        <th>Total net Sales Value</th>
    </tr>
    <?php for($i=0;$i<count($showData);$i++){?>
        <tr>
            <td><?php echo $showData[$i]['supplier']?></td>
            <td><?php echo $showData[$i]['code']?></td>
            <td><?php echo $showData[$i]['itemName']?></td>
            <td><?php echo $showData[$i]['amount']?></td>
            <td><?php echo $showData[$i]['price']?></td>
            <td><?php echo $showData[$i]['totalNetSalesValue']?></td>
        </tr>
    <?php } */?>
<!--/table>
</div>
</body>
</html-->

