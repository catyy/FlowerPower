<?php
session_start();
error_reporting(E_ALL);
include("conf.php");


//find warehouses
function getAllWarehouses($api) {

    $output = apiSendRequest($api,"getWarehouses",[]);
    $counter = count($output['records']);
    $warehouses = array();

    for($i=0;$i<$counter;$i++){
        $name =htmlspecialchars($output['records'][$i]["name"]);
        $nameID =htmlspecialchars($output['records'][$i]["warehouseID"]);
        $warehouses[] =array("name" =>$name,"id" =>$nameID);
    }
    asort($warehouses);
    return $warehouses;
}


//check api errors
function apiErrorCheck($output){
    if($output['status']['errorCode'] != 0){
        die("Could not get data!! Error code: ".$output['status']['errorCode']);
    }
}

//sendrequests, returns output
function apiSendRequest($api,$query,$params){

    $result = $api->sendRequest($query,$params);
    $output = json_decode($result, true);
    apiErrorCheck($output);
    return $output;
}


//get products
function getProducts($api,$ids){

    $params =array("productIDs" => $ids,"recordsOnPage" => 100);
    $product = apiSendRequest($api,"getProducts",$params);
    return $product;
}


//gets all ids from data and make string for multiple request
function idString($data){

    $arrayIDs = array();
    $ids = array();
    foreach ($data as $value) {
        $id = $value['productID'];

        if(in_array($id,$ids) || !is_numeric($id)){
           continue;
        }else{
            $ids[] = $id;
        }
    }

    //if there is more than 100 ids, then make subarrays for getproducts request
    $counter = 0;
    while(true){

        if(count($ids)<=100){
            $idsString = implode(",", $ids);
            $arrayIDs[] = $idsString;

            break;
        }elseif(count($ids)>$counter+100){
            $idsString = array_slice($ids,$counter,100);
            $idsString = implode(",", $idsString);
            $arrayIDs[] = $idsString;
            $counter = $counter + 100;
        }else{
            $idsString = array_slice($ids,$counter);
            $idsString = implode(",", $idsString);
            $arrayIDs[] = $idsString;
            break;
        }
    }
    return $arrayIDs;
    }



//collect products values
function sortProductValues($data){

    $showD = array();
    if(count($data)>1){
        for($i=0; $i<count($data);$i++){

            foreach ($data[$i]['records'] as $item) {
                $showD[$item['productID']] =array("productID"=> $item['productID'],"productName" => $item['name'],"layaway" => "","groupName" => $item['groupName'],"price" => $item['price'],"unitName" => $item['unitName'],"code" => $item['code'],"availableStock" =>"","netSalesValue" =>"" );
            }
        }
    }else{
        $dataTemp = $data[0]['records'];
        foreach ($dataTemp as $item) {
            $showD[$item['productID']] =array("productID"=> $item['productID'],"productName" => $item['name'],"layaway" => "","groupName" => $item['groupName'],"price" => $item['price'],"unitName" => $item['unitName'],"code" => $item['code'],"availableStock" =>"","netSalesValue" =>"" );
        }
    }
    return $showD;
}

//sort data by date
function getCompoundPOS($output){

    $showData = array();
    foreach($output as $value){
        $showData[$value['date']][] = $value;
    }
    return $showData;
}



//sort data by date
function getCompound($output){

    $showData = array();
    foreach($output as $value){
        $showData[$value['date']][] = $value;
    }
    return $showData;
}