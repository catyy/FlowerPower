<?php
session_start();
error_reporting(E_ALL);
include("functions.php");

//Payment summary report

$dateFrom =  $_SESSION['dateFrom'];
$dateTo =  $_SESSION['dateTo'];

if($_SESSION['warehouse'] != null && $_SESSION['warehouse'] != "all"){

    $w = $_SESSION['warehouse'];
    $warehouse = explode(",", $w);
    $warehouse = $warehouse[0];
    $params = array("type" => "ZReport","dateStart"=>$dateFrom,"dateEnd"=> $dateTo, "warehouseID" => $warehouse);
    $output = apiSendRequest($api,"getReports",$params);

}else {
    $params = array("type" => "ZReport","dateStart"=>$dateFrom,"dateEnd"=> $dateTo);
    $output = apiSendRequest($api,"getReports",$params);
}
$showData1 = getReportData($output);
$showData = getCompound($showData1);
$showData = finalSort($showData);


//sort data from Zreport
function getReportData($output){

    $showData = array();
    $data = null;
    $counter = count($output['records']['transfers']);
    if($counter >0){
        $data = $output['records']['transfers'][0];
    }else{
        return null;
    }

    foreach($data['groups'] as $value){
        $type = $value['type'];
        $days = $value['days'];
        $name = $value['name'];

        foreach($days as $day){
            $showData[] = array("date"=>$day['date'], "total"=>$day['total'],"type"=>$type,"name" => $name );
        }
    }
    return $showData;
}




//sorts data by card type and required fields
function finalSort($showData){

    $data = array();

    foreach($showData as $value){
        $cash = $card = $check =  $other = $giftcard = $visa = $master = $discover = $amex = $misch = 0;

        foreach($value as $v){

            $sum = str_replace( ',', '', $v['total'] );
            if($v['type'] == "CASH"){
                $cash += $sum;
            }elseif($v['type'] == "CARD"){
                $card += $sum;
            }elseif($v['type'] == "CHECK"){
                $check += $sum;
            }elseif($v['type'] == "GIFTCARD"){
                $giftcard += $sum;
            }

            if($v['type'] == "CARD"){

                if (strpos($v['name'], 'VISA') !== false){
                    $visa += $sum;
                }elseif(strpos($v['name'], 'MASTERCARD') !== false ){
                    $master += $sum;
                }elseif(strpos($v['name'], 'DISCOVER') !== false ){
                    $discover += $sum;
                }elseif(strpos($v['name'], 'AMEX') !== false ){
                    $amex += $sum;
                }elseif(strpos($v['name'], 'MISCELLANEOUS') !== false){
                    $misch += $sum;
                }else{
                    $other += $sum;
                }
            }
        }
        $date = $v['date'];
        $check_cash = $cash + $check;
        $total = $cash + $check + $card + $giftcard;
        $data[] = array("date"=>$date, "in cash"=>$cash,"in check"=>$check,"check+cash" => $check_cash,"by card"=>$card, "visa"=>$visa,"master"=>$master,"amex" =>$amex,"other"=>$other, "discover"=>$discover,"mischell"=>$misch,"giftcard" => $giftcard,"total"=>$total);
    }
    return $data;
}?>

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
        <h1>Payment Summary Report</h1><br>

        <table>
            <tr>
                <th>Location:</th>

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

        <table class="table table-striped table-condensed" border="1" style="width:90%">
            <br><br>
            <tr>
                <th>Date</th>
                <th>In cash</th>
                <th>In check</th>
                <th>In check + Cash</th>
                <th>By card</th>
                <th>By card - Visa</th>
                <th>By card - Mastercard</th>
                <th>By card - Amex</th>
                <th>By card - Other</th>
                <th>By card - Discover</th>
                <th>By card - Miscellaneous</th>
                <th>With gift card</th>
                <th>Total</th>
            </tr>
            <?php for($i=0;$i<count($showData);$i++){?>
                <tr>
                    <td><?php echo $showData[$i]['date']?></td>
                    <td><?php echo $showData[$i]['in cash']?></td>
                    <td><?php echo $showData[$i]['in check']?></td>
                    <td><?php echo $showData[$i]['check+cash']?></td>
                    <td><?php echo $showData[$i]['by card']?></td>
                    <td><?php echo $showData[$i]['visa']?></td>
                    <td><?php echo $showData[$i]['master']?></td>
                    <td><?php echo $showData[$i]['amex']?></td>
                    <td><?php echo $showData[$i]['other']?></td>
                    <td><?php echo $showData[$i]['discover']?></td>
                    <td><?php echo $showData[$i]['mischell']?></td>
                    <td><?php echo $showData[$i]['giftcard']?></td>
                    <td><?php echo $showData[$i]['total']?></td>

                </tr>
            <?php }    ?>
        </table>
    </div>
    </body>
    </html>


