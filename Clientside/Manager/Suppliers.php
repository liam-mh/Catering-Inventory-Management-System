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
                    <form method="post">                   
                        <h><?php echo strtoupper($supplier[0][0]) ?></h>
                        <br><br><br>
                        <p><?php echo $supplier[0][1]?></p>
                        <div class="w1-box">
                            <h>NAME</h>
                            <br>
                            <input type="text" name="dairyNameInput" value="<?php $supplier[0][1] ?>">
                        </div>
                        <br>
                        <div class="w1-box">
                            <h>EMAIL</h>
                            <br>
                            <input type="text" name="dairyEmailInput" value="<?php $supplier[0][2] ?>">
                        </div>
                        <br>
                        <div class="form-group">
                            <input class="btn btn-main" style="width:50%" type="submit" value="APPLY" name="dairyApply"></input> 
                        </div> 
                    </form> 
                    <br><br>
                    <h>RECENT ORDERS</h>
                    <br>
                    <table class="styled-table" style="display:block; height:100px; overflow:auto;">
                        <thead>
                            <th>Date</th>
                            <th>Accept/Decline</th>
                            <th>Link</th>    
                        </thead>
                        <tbody>
                            <?php 
                            $PDF = getDairyPDF();
                            for ($i=0; $i<count($PDF); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $PDF[$i]['Date']?></td>                                                           
                                <td><?php echo $PDF[$i]['Accept_Decline']?></td>
                                <td><?php echo $PDF[$i]['PDF_Link']?></td>                                                             
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

                <!-- MEAT / FISH -->
                <div class="col-md-4">
                    <form method="post">                   
                        <h><?php echo strtoupper($supplier[1][0]) ?></h>
                        <br><br><br>
                        <p><?php echo $supplier[1][1]?></p>
                        <div class="w1-box">
                            <h>NAME</h>
                            <br>
                            <input type="text" name="dairyNameInput" value="<?php $supplier[0][1] ?>">
                        </div>
                        <br>
                        <div class="w1-box">
                            <h>EMAIL</h>
                            <br>
                            <input type="text" name="dairyEmailInput" value="<?php $supplier[0][2] ?>">
                        </div>
                        <br>
                        <div class="form-group">
                            <input class="btn btn-main" style="width:50%" type="submit" value="APPLY" name="dairyApply"></input> 
                        </div> 
                    </form> 
                    <br><br>
                    <h>RECENT ORDERS</h>
                    <br>
                    <table class="styled-table" style="display:block; height:100px; overflow:auto;">
                        <thead>
                            <th>Date</th>
                            <th>Accept/Decline</th>
                            <th>Link</th>    
                        </thead>
                        <tbody>
                            <?php 
                            $PDF = getMeatPDF();
                            for ($i=0; $i<count($PDF); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $PDF[$i]['Date']?></td>                                                           
                                <td><?php echo $PDF[$i]['Accept_Decline']?></td>
                                <td><?php echo $PDF[$i]['PDF_Link']?></td>                                                             
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

                <!-- FRUIT / VEG -->
                <div class="col-md-4">
                    <form method="post">                   
                        <h><?php echo strtoupper($supplier[2][0]) ?></h>
                        <br><br><br>
                        <p><?php echo $supplier[2][1]?></p>
                        <div class="w1-box">
                            <h>NAME</h>
                            <br>
                            <input type="text" name="dairyNameInput" value="<?php $supplier[0][1] ?>">
                        </div>
                        <br>
                        <div class="w1-box">
                            <h>EMAIL</h>
                            <br>
                            <input type="text" name="dairyEmailInput" value="<?php $supplier[0][2] ?>">
                        </div>
                        <br>
                        <div class="form-group">
                            <input class="btn btn-main" style="width:50%" type="submit" value="APPLY" name="dairyApply"></input> 
                        </div> 
                    </form> 
                    <br><br>
                    <h>RECENT ORDERS</h>
                    <br>
                    <table class="styled-table" style="display:block; height:100px; overflow:auto;">
                        <thead>
                            <th>Date</th>
                            <th>Accept/Decline</th>
                            <th>Link</th>    
                        </thead>
                        <tbody>
                            <?php 
                            $PDF = getVegPDF();
                            for ($i=0; $i<count($PDF); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $PDF[$i]['Date']?></td>                                                           
                                <td><?php echo $PDF[$i]['Accept_Decline']?></td>
                                <td><?php echo $PDF[$i]['PDF_Link']?></td>                                                             
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

            </div>   
        </div> 
    </div>
    
</body>