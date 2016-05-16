<?php
session_start();
error_reporting(E_ALL);
include("functions.php");
include("inStock.php");
include("onOrder.php");
include("sold.php");

$inStockData = getInStockData($api);
$onOrderData = getOnOrderData($api);
$soldData = getSoldData($api);

//print "<pre>";
//print_r($soldData);
//print  "</pre>";

// inStock, OnOrder and sold reports

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Reports</title>
    <script src="Bootstrap/js/jquery-2.2.1.js"></script>
    <link href = "Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "Bootstrap/js/bootstrap.min.js"></script>

</head>

<body>
<div class="container-fluid"><br>
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

    <!--//inStock data table-->
    <br>
    <h1>Product in Stock Net Sales Value - by Product</h1><br>
    <table class="table table-striped table-condensed" border="1" style="width:80%">

    <tr>
        <th>Code</th>
        <th>Product</th>
        <th>Group</th>
        <th>Available</th>
        <th>Layaway</th>
        <th>Unit</th>
        <th>Net Sales Price</th>
        <th>Net Sales Value</th>
    </tr>
    <?php foreach($inStockData as $value){?>
        <tr>
            <td><?php echo $value['code']?></td>
            <td><?php echo $value['productName']?></td>
            <td><?php echo $value['groupName']?></td>
            <td><?php echo $value['availableStock']?></td>
            <td><?php echo $value['layaway']?></td>
            <td><?php echo $value['unitName']?></td>
            <td><?php echo $value['price']?></td>
            <td><?php echo $value['netSalesValue']?></td>

        </tr>
    <?php }?>
</table>

<!--//onOrder data table-->
    <br>
    <h1>Unfullfied Purchase Order Net Sales Value -  by Product</h1><br>
    <table class="table table-striped table-condensed" border="1" style="width:80%">
        <br>
        <tr>
            <th>Supplier</th>
            <th>Code</th>
            <th>Name</th>
            <th>Purchased quantity</th>
            <th>Net Sales Price</th>
            <th>Total net Sales Value</th>
        </tr>
        <?php for($i=0;$i<count($onOrderData);$i++){?>
            <tr>
                <td><?php echo $onOrderData[$i]['supplier']?></td>
                <td><?php echo $onOrderData[$i]['code']?></td>
                <td><?php echo $onOrderData[$i]['itemName']?></td>
                <td><?php echo $onOrderData[$i]['amount']?></td>
                <td><?php echo $onOrderData[$i]['price']?></td>
                <td><?php echo $onOrderData[$i]['totalNetSalesValue']?></td>
            </tr>
        <?php }?>
    </table>

<!--//sold data table-->
    <br>
    <h1>Product Sales Net Sales Value - by Product</h1><br>
    <table class="table table-striped table-condensed" border="1" style="width:80%">
        <br><br>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Group</th>
            <th>Sold quantity</th>
            <th>Net Sales Price</th>
            <th>Unit</th>
            <th>Total net Sales Value</th>
            <th>Net Discout Totals</th>
        </tr>
        <?php foreach($soldData as $value){?>
            <tr>
                <td><?php echo $value['code']?></td>
                <td><?php echo $value['productName']?></td>
                <td><?php echo $value['group']?></td>
                <td><?php echo $value['soldQuantity']?></td>
                <td><?php echo $value['price']?></td>
                <td><?php echo $value['unit']?></td>
                <td><?php echo $value['totalNetSales']?></td>
                <td><?php echo $value['discountTotal']?></td>
            </tr>
        <?php }?>
    </table>

</div>
</body>
</html>

