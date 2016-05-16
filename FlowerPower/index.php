<?php
session_start();
error_reporting(E_ALL);
include("functions.php");


//if date_to is smaller than date_from
if(isset($_SESSION['datewarning']) && !empty($_SESSION['datewarning'])){

    if($_SESSION['datewarning'] == "warning"){
        echo "<div style='color: red'> <br>&nbsp; Date to can not be bigger than date from!</div>";
        $_SESSION['datewarning'] = null;
    }elseif($_SESSION['datewarning'] == "warning1"){
        echo "<div style='color: red'> <br>&nbsp; Date difference can be only four months!</div>";
        $_SESSION['datewarning'] = null;
    }elseif($_SESSION['datewarning'] == "warning2"){
        echo "<div style='color: red'> <br>&nbsp; Date difference can be only one month!</div>";
        $_SESSION['datewarning'] = null;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main</title>
    <script src="Bootstrap/js/jquery-2.2.1.js"></script>
    <link href = "Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "Bootstrap/js/bootstrap.min.js"></script>
<script>

   $(document).ready(function(){

       //if pressed back button, then it saves disabled
       var value = document.getElementById('reportValue').value;
       if(value == "Stock"){
           document.getElementById("dateFrom").disabled = true;
           document.getElementById("dateTo").disabled = true;
       }else{
           document.getElementById("dateFrom").disabled = false;
           document.getElementById("dateTo").disabled = false;
       }

        //if stock is selected, date inputs are disabled
        document.getElementById('reportValue').addEventListener('change', function () {
            var select_val = this.value;
            console.log(select_val);

            if(select_val == "Stock"){
                document.getElementById("dateFrom").disabled = true;
                document.getElementById("dateTo").disabled = true;
            }else{
                document.getElementById("dateFrom").disabled = false;
                document.getElementById("dateTo").disabled = false;
            }
        }, false);
    });
</script>
</head>
<body>
<div class="container-fluid"><br>
    <h1>Reports </h1><br>

    <form action="main.php" method="post">
        <label>Warehouses:
            <select class="selectpicker" name='warehouse'>

                <?php
                $warehousesToList = array();
                $warehousesToList = getAllWarehouses($api);
                $_SESSION['allWarehouses'] = $warehousesToList;
                ?>
                <option value="all">All</option>
                <?php
                foreach($warehousesToList as $value){
                    $id = $value['id'];
                    $category = htmlspecialchars($value['name']);
                    echo "<option value='$id,$category'>$category</option>";

                }
                  ?>
            </select><br></label>
            <br><br>

                <label>Choose report:
                    <select  class="selectpicker" name='report' id="reportValue">
                        <option value="All">All</option>
                        <option value="In">In</option>
                        <option value="Out">Out</option>
                        <option value="POS">POS</option>
                        <option value="Stock" id="stock">Stock</option>
                    </select><br></label><br><br>

                        <label>Date from:
                        <br><input type="date" name="dateFrom" id="dateFrom" required>  --- <br><br></label>

                        <label>Date to:
                        <br><input type="date" name="dateTo" id="dateTo"required><br><br></label><br>

                        <input type="submit" name="submit" value="Find">
                </form>
</div>
</body>
</html>
