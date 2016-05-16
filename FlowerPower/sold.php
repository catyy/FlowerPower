<?php
session_start();
error_reporting(E_ALL);
//include("functions.php");

//Product Sales Net Sales Value - by Product

function getSoldData($api){

    $allWarehouses = $_SESSION['allWarehouses'];
    $dateFrom =  $_SESSION['dateFrom'];
    $dateTo =  $_SESSION['dateTo'];

    //if selected only one warehouse
    if($_SESSION['warehouse'] != null && $_SESSION['warehouse'] != "all"){

        $w = $_SESSION['warehouse'];
        $warehouse = explode(",", $w);
        $warehouse = $warehouse[0];
        $params = array("reportType" => "SALES_BY_PRODUCT","dateStart"=>$dateFrom,"dateEnd"=> $dateTo, "warehouseID" => $warehouse);
        $output = apiSendRequest($api,"getSalesReport",$params);
        $fileData = getDataFromFile($output);
        $ids = idString($fileData);

        if(count($ids)>1){
            foreach($ids as $id){
                $products[] = getProducts($api,$id);
            }
        }else{
            $products[] = getProducts($api,$ids[0]);
        }
        $productsValues = sortProductValues($products);
        $showData = showStockData($fileData,$productsValues);

    }else {
        //selected all warehouses
        foreach($allWarehouses as $warehouse) {
            $params = array("warehouseID" => $warehouse['id'],"reportType" => "SALES_BY_PRODUCT","dateStart"=>$dateFrom,"dateEnd"=> $dateTo,);
            $outputTemp[] = apiSendRequest($api, "getSalesReport", $params);

        }

        $fileData = getDataFromFile($outputTemp);
        $ids = idString($fileData);
        foreach($ids as  $id){
            $products[] = getProducts($api,$id);
        }
        $productsValues = sortProductValues($products);
        $showData = showStockData($fileData,$productsValues);
    }

    $showData = sumData($showData);
    return $showData;
}

function getDataFromFile($output){

    $counter = count($output);
    $data =null;

    if($counter>2){
        foreach($output as $value){

            foreach($value['records'] as $v){
                $fileName = htmlspecialchars($v['reportLink']);
                $csvFile = file($fileName);

                foreach ($csvFile as $line) {
                    $data = str_getcsv($line, "\n");
                    $data = explode(",",$data[0]);
                    $data = str_ireplace('"', '', $data);

                    if(is_numeric($data[6])){
                        $soldq = $data[6];
                        $productName=$data[5];
                        $unit=$data[7];
                        $totalNetSales=$data[8];
                        $discountTotal=$data[10];
                    }else{
                        $soldq = $data[7];
                        $productName=$data[6];
                        $unit=$data[8];
                        $totalNetSales=$data[9];
                        $discountTotal=$data[11];
                    }
                    $showData[] = array("productID"=>$data[1],"productName"=>$productName,"code"=>$data[3],"group"=>"","soldQuantity"=>$soldq,"price"=>"","unit"=>$unit,"totalNetSales"=>$totalNetSales,"discountTotal"=>$discountTotal);
                }
            }
        }
    }else{

        $fileName = htmlspecialchars($output['records'][0]['reportLink']);
        $csvFile = file($fileName);

        foreach ($csvFile as $line) {
            $data = str_getcsv($line, "\n");
            $data = explode(",",$data[0]);
            $data = str_ireplace('"', '', $data);
            if(is_numeric($data[6])){
                $soldq = $data[6];
                $productName=$data[5];
                $unit=$data[7];
                $totalNetSales=$data[8];
                $discountTotal=$data[10];
            }else{
                $soldq = $data[7];
                $productName=$data[6];
                $unit=$data[8];
                $totalNetSales=$data[9];
                $discountTotal=$data[11];
            }
            $showData[] = array("productID"=>$data[1],"productName"=>$productName,"code"=>$data[3],"group"=>"","soldQuantity"=>$soldq,"price"=>"","unit"=>$unit,"totalNetSales"=>$totalNetSales,"discountTotal"=>$discountTotal);
        }
    }
    return $showData;
}


function showStockData($fileData,$productsValues){

    foreach($fileData as $value){

        if(is_numeric($value['productID']) && $value['productID'] != 0){
            $showData[] = array("productID"=>$value['productID'],"productName"=>$value['productName'],"code"=>$value['code'],"group"=>$productsValues[$value['productID']]['groupName'],"soldQuantity"=>$value['soldQuantity'],"price"=>$productsValues[$value['productID']]['price'],"unit"=>$value['unitName'],"totalNetSales"=>$value['totalNetSales'],"discountTotal"=>$value['discountTotal']);
        }
    }
    return $showData;
}


function sumData($data){

    $showData=array();

    foreach($data as $value){
        $id = $value["productID"];
        if(array_key_exists($id,$showData)){

            $sold = $showData[$id]['soldQuantity'] + $value['soldQuantity'];
            $discount = $showData[$id]['discountTotal'] + $value['discountTotal'];
            $totalNetSales = $showData[$id]['totalNetSales'] + $value['totalNetSales'];
            $showData[$id] = array("productID"=>$id,"productName"=>$value['productName'],"code"=>$value['code'],"group"=>$value['group'],"soldQuantity"=>$sold,"price"=>$value['price'],"unit"=>$value['unit'],"totalNetSales"=>$totalNetSales,"discountTotal"=>$discount);


        }else{
            $showData[$id] = array("productID"=>$id,"productName"=>$value['productName'],"code"=>$value['code'],"group"=>$value['group'],"soldQuantity"=>$value['soldQuantity'],"price"=>$value['price'],"unit"=>$value['unit'],"totalNetSales"=>$value['totalNetSales'],"discountTotal"=>$value['discountTotal']);
        }
    }
    return $showData;
}

?>


<!--!DOCTYPE html>
<html-- lang="en">
<head>
    <meta charset="UTF-8">
    <title>On Order</title>
    <script src="Bootstrap/js/jquery-2.2.1.js"></script>
    <link href = "Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "Bootstrap/js/bootstrap.min.js"></script>

</head>

<body>
<div class="container-fluid"><br>
    <h1>Product Sales Net Sales Value - by Product</h1><br>

    <table>
        <tr>
            <th>Locatin:</th>

            <?php
     /*       if($_SESSION['warehouse'] =="all"){
                echo "<th>&nbsp;All warehouses</th></tr>";

            }else{
                $w = $_SESSION['warehouse'];
                $warehouse = explode(",", $w);
                echo "<th>&nbsp;".$warehouse[1]."</th></tr>";
            }*/  ?>
            <br>
        <tr>
            <th>Period:</th>
            <?php/* echo "<th>&nbsp;".$_SESSION['dateFrom']." - ".$_SESSION['dateTo']."</th></tr>" */?>
        </tr>
    </table>

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
        <?php/* for($i=0;$i<count($showData);$i++){ */?>
            <tr>
                <td>< /*?php echo $showData[$i]['code'] */?></td>
                <td><?php /* echo $showData[$i]['productName'] */?></td>
                <td><?php /*echo $showData[$i]['group'] */?></td>
                <td><?php /*echo $showData[$i]['soldQuantity'] */?></td>
                <td><?php /*echo $showData[$i]['price'] */?></td>
                <td><?php /*echo $showData[$i]['unit'] */?></td>
                <td><?php /*echo $showData[$i]['totalNetSales'] */?></td>
                <td><?php /*echo $showData[$i]['discountTotal'] */?></td>
            </tr>
        <?php// }?>
    </table>
</div>
</body>
</html-->

