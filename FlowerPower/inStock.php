<?php
session_start();
error_reporting(E_ALL);

//Product in Stock Net Sales Value - by Product

function getInStockData($api){

    $allWarehouses = $_SESSION['allWarehouses'];
    $showData = array();

    //if selected only one warehouse
    if($_SESSION['warehouse'] != null && $_SESSION['warehouse'] != "all"){

        $w = $_SESSION['warehouse'];
        $warehouse = explode(",", $w);
        $warehouse = $warehouse[0];
        $params =array("warehouseID" => $warehouse, "getAmountReserved" => 1);
        $output = apiSendRequest($api,"getProductStock",$params);
        $data = getproductsData($output);
        $ids = idString($data);
        //gets all products with collected ids
        if(count($ids)>1){
            foreach($ids as $id){
                $products[] = getProducts($api,$id);
            }
        }else{
            $products[] = getProducts($api,$ids[0]);
        }
        $productsValues = sortProductValues($products);
        $showData = showInStockData($data,$productsValues);
        $showData = getCompoundData($showData);
//        print "<pre>";
//        print_r($showData);
//        print  "</pre>";

    }else{
        //selected all warehouses
        $allProductStock =array();

        foreach($allWarehouses as $warehouse) {
            $params = array("warehouseID" => $warehouse['id'], "getAmountReserved" => 1);
            $outputTemp = apiSendRequest($api, "getProductStock", $params);

            if ($outputTemp['status']['recordsTotal'] == 0)continue;
            $data = getproductsData($outputTemp);
            $allProductStock = array_merge($allProductStock,$data);
        }
        $ids = idString($allProductStock);
        $products = null;

        foreach($ids as $id){
            $products1 = getProducts($api, $id);
            $products[] = $products1;
        }
        //collect requested data from products request
        $productsValues = sortProductValues($products);
        $showData1 = showInStockData($allProductStock, $productsValues);

        $compoundData = getCompoundData($showData1);
        $showData = array_merge($showData,$compoundData);
    }
    return  $showData;
}

function getCompoundData($data){

    $showData = array();
    for($i = 0; $i<count($data);$i++){
        $temp = $data[$i];

        if(array_key_exists($temp['productID'], $showData)){

            $existData = $showData[$temp['productID']];
            $availableStock = $existData['availableStock']+$temp['availableStock'];
            $netSalesValue = $existData['netSalesValue']+$temp['netSalesValue'];
            $layaway = $existData['layaway']+$temp['layaway'];
            $showData[$temp['productID']] = array("productID" => $temp['productID'],"productName" => $temp['productName'],"layaway" => $layaway,"groupName" =>$temp['groupName'],"price" => $temp['price'],"unitName" => $temp['unitName'],"code" => $temp['code'],"availableStock" => $availableStock,"netSalesValue" =>$netSalesValue );
        }else{
            $availableStock = $temp['availableStock'];
            $netSalesValue = $temp['price'];
            $netSalesValue = $availableStock * $netSalesValue;
            $showData[$temp['productID']] = array("productID" => $temp['productID'],"productName" => $temp['productName'],"layaway" => $temp['amountReserved'],"groupName" =>$temp['groupName'],"price" => $temp['price'],"unitName" => $temp['unitName'],"code" => $temp['code'],"availableStock" => $availableStock,"netSalesValue" =>$netSalesValue );
        }
    }
    return $showData;

}


//collect requested products data
function getproductsData($output){

    $data = array();
    $counter = $output['status']['recordsTotal'];
    //gets productids and availble stock amount
    for($i = 0; $i<$counter;$i++){
        $data[] =  array("productID" =>$output['records'][$i]['productID'],"amountInStock" => $output['records'][$i]['amountInStock'], "amountReserved" => $output['records'][$i]['amountReserved']);
    }
    return $data;
}



//sum collected datas (products and productstock
function showInStockData($data,$productsValues){

    $showData = array();
    foreach($data as $value ){
        $availableStock = $value['amountInStock']; //['amountInStock']
        $netSalesValue = $productsValues[$value['productID']]['price'];
        $netSalesValue = $availableStock * $netSalesValue;
        $showData[] = array("productID" => $value['productID'],"productName" => $productsValues[$value['productID']]['productName'],"layaway" => $value ['amountReserved'],"groupName" =>$productsValues[$value['productID']]['groupName'],"price" => $productsValues[$value['productID']]['price'],"unitName" => $productsValues[$value['productID']]['unitName'],"code" => $productsValues[$value['productID']]['code'],"availableStock" =>$availableStock,"netSalesValue" =>$netSalesValue );
    }
    return $showData;
}


if($_SESSION['stock'] == "stock"){
    include("functions.php");
    $showData1 = getInStockData($api);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Stock</title>
        <script src="Bootstrap/js/jquery-2.2.1.js"></script>
        <link href = "Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
        <script src = "Bootstrap/js/bootstrap.min.js"></script>
    </head>
    <body>
    <div class="container-fluid"><br>
        <h1>Product in Stock Net Sales Value - by Product</h1><br>
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
            <th>Code</th>
            <th>Product</th>
            <th>Group</th>
            <th>Available</th>
            <th>Layaway</th>
            <th>Unit</th>
            <th>Net Sales Price</th>
            <th>Net Sales Value</th>
        </tr>
    <?php foreach($showData1 as $value){?>
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
    <?php }    ?>
</table>
</div>
</body>
</html>
<?php }    ?>

