<?php 

include("../../Serverside/Sessions.php");
include("../../Serverside/Functions.php");

$path = "ManagerLogin.php";
session_start(); 
if (!isset($_SESSION['Username'])) {
    session_unset();
    session_destroy();
    header("Location:".$path);
}
$Name = $_SESSION['Username'];


function update() {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Stock SET Item_Name=:ItemName, Category=:Category, Unit_Price=:UnitPrice, Threshold=:Threshold, Quantity=:Quantity WHERE Item_Name=:Name' ;
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Name',      $_GET['Selected'], SQLITE3_TEXT);
    $stmt->bindParam(':ItemName',  $_POST['UpdateItemName'], SQLITE3_TEXT);
    $stmt->bindParam(':Category',  $_POST['UpdateCategory'], SQLITE3_TEXT);
    $stmt->bindParam(':UnitPrice', $_POST['UpdateUnitPrice'], SQLITE3_INTEGER);
    $stmt->bindParam(':Threshold', $_POST['UpdateThreshold'], SQLITE3_INTEGER);
    $stmt->bindParam(':Quantity',  $_POST['UpdateQuantity'], SQLITE3_INTEGER);
    $stmt->execute();
    header('Location:Index.php?updated=true"');
}


//-------------------------------------------------------------------------------------------------------
//----- INSERT USED STOCK -------------------------------------------------------------------------------

if (isset($_POST['apply'])) {
    $insert = InsertUsed();
}

//-------------------------------------------------------------------------------------------------------
//----- ADD NEW TAB -------------------------------------------------------------------------------------

//Add new tab being selected
$AddNew = FALSE;
if (isset($_POST['AddNew'])) $AddNew = TRUE;

//Setting error variables
$ItemNameError = $CategoryError = $UnitPoundsError = $UnitPenceError = $ThresholdError = ""; 

//add new item 
$allFields = "yes";
if (isset($_POST['add'])) {

    if ($_POST['ItemName']=="")   {$ItemNameError = "Please enter the item name"; $allFields = "no";}
    if ($_POST['Category']=="")   {$CategoryError = "Please select a category";   $allFields = "no";}
    if ($_POST['UnitPounds']=="") {$UnitPoundsError = "Please enter pounds";      $allFields = "no";}
    if ($_POST['UnitPence']=="")  {$UnitPenceError = "Please enter pence";        $allFields = "no";}
    if ($_POST['Threshold']=="")  {$ThresholdError = "Please enter a threshold";  $allFields = "no";}
    if ($allFields == "yes") {
        addNew();
    }
}

//-------------------------------------------------------------------------------------------------------
//----- CURRENT STOCK / EDIT & DELETE -------------------------------------------------------------------

//Updating database with 'apply' button
if (isset($_POST['edit'])) {
    update();
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING FROM SUPLLIERS --------------------------------------------------------------------------

$supplier = getSupplier();

?>

<body>
    <?php require("ManagerNavbar.php");?>
    <div class="container">

    <div style="text-align:center">  
        <div class="w1-box">
            <div class="row">

                <!-- DAIRY -->
                <div class="col-md-4">
                    <h><?php echo strtoupper($supplier[0][0]) ?></h>
                    <br><br><br>         
                    <h>LOW QUANTITY ITEMS</h>                
                    <br><br>
                    <table class="styled-table" style="display:block; height:300px; overflow:auto;">
                        <thead>
                            <th>ITEM</th>
                            <th>ORDER QUANTITY</th>    
                        </thead>
                        <tbody>
                            <?php 
                            $dairy = getDairyOrder();
                            for ($i=0; $i<count($dairy); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $dairy[$i]['Item_Name']?></td>                                                           
                                <td style="padding-top:4px">
                                    <form method="post">
                                        <div class="form-group" ></div>
                                            <input type="hidden" name="dairyInput" value = "<?php $dairy[$i]['Item_Name'] ?>">
                                            <input type="text" name="quantityDairyInput">
                                        </div>
                                    </form>
                                </td>   
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br>
                    <p><?php echo strtoupper($supplier[0][1]) ?></p>
                    <p>ORDER TOTAL: £<?php ?></p>
                    <div class="form-group">
                        <input class="btn btn-main" style="width:50%" type="submit" value="ORDER" name="dairyOrder"></input> 
                    </div> 
                </div>

                <!-- MEAT / FISH -->
                <div class="col-md-4">
                    <h><?php echo strtoupper($supplier[1][0]) ?></h>
                    <br><br><br>         
                    <h>LOW QUANTITY ITEMS</h>                
                    <br><br>
                    <table class="styled-table" style="display:block; height:300px; overflow:auto;">
                        <thead>
                            <th>ITEM</th>
                            <th>ORDER QUANTITY</th>    
                        </thead>
                        <tbody>
                            <?php 
                            $meat = getMeatOrder();
                            for ($i=0; $i<count($meat); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $meat[$i]['Item_Name']?></td>                                                           
                                <td style="padding-top:4px">
                                    <form method="post">
                                        <div class="form-group" ></div>
                                            <input type="hidden" name="MeatInput" value = "<?php $meat[$i]['Item_Name'] ?>">
                                            <input type="text" name="quantityMeatInput">
                                        </div>
                                    </form>
                                </td>   
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br>
                    <p><?php echo strtoupper($supplier[1][1]) ?></p>
                    <p>ORDER TOTAL: £<?php ?></p>
                    <div class="form-group">
                        <input class="btn btn-main" style="width:50%" type="submit" value="ORDER" name="meatOrder"></input> 
                    </div> 
                </div>

                <!-- FRUIT / VEG -->
                <div class="col-md-4">
                    <h><?php echo strtoupper($supplier[2][0]) ?></h>
                    <br><br><br>         
                    <h>LOW QUANTITY ITEMS</h>                
                    <br><br>
                    <table class="styled-table" style="display:block; height:300px; overflow:auto;">
                        <thead>
                            <th>ITEM</th>
                            <th>ORDER QUANTITY</th>    
                        </thead>
                        <tbody>
                            <?php 
                            $veg = getVegOrder();
                            for ($i=0; $i<count($veg); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $dairy[$i]['Item_Name']?></td>                                                           
                                <td style="padding-top:4px">
                                    <form method="post">
                                        <div class="form-group" ></div>
                                            <input type="hidden" name="vegInput" value = "<?php $veg[$i]['Item_Name'] ?>">
                                            <input type="text" name="quantityVegInput">
                                        </div>
                                    </form>
                                </td>   
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br>
                    <p><?php echo strtoupper($supplier[2][1]) ?></p>
                    <p>ORDER TOTAL: £<?php ?></p>
                    <div class="form-group">
                        <input class="btn btn-main" style="width:50%" type="submit" value="ORDER" name="dairyOrder"></input> 
                    </div> 
                </div>

            </div>   
        </div> 
    </div>
    
</body>