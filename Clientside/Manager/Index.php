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

//-------------------------------------------------------------------------------------------------------
//----- INSERT USED STOCK -------------------------------------------------------------------------------

if (isset($_POST['apply'])) {
    //dairy insert
    $insert = InsertUsed();
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Stock SET Quantity =:qt WHERE Item_Name =:in AND Category=:ct' ;
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':qt',$_POST[''], SQLITE3_TEXT);

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

$selected = $_GET['Selected'];

//$_GET for edit/delete
$db = new SQLite3('/Applications/MAMP/db/IMS.db');
$sql = "SELECT * FROM Stock WHERE Item_name = :Itemname";
$stmt = $db->prepare($sql);
$stmt->bindParam(':Itemname', $selected, SQLITE3_TEXT); 
$result = $stmt->execute();
$SelectedItem = [];
while($row=$result->fetchArray(SQLITE3_NUM)){$SelectedItem [] = $row;}

//update selected
if (isset($_POST['edit'])) {updateSelected();}
//delete selectd
if (isset($_POST['delete'])){deleteSelected();}

?>

<body>
    <?php require("ManagerNavbar.php");?>
    <div class="container">

    <div class="row">

        <!-- Insert Used Stock Tab -->
        <div class="col-md-3" style="text-align:center">  
            <div class="w1-tab">
                <h>INSERT USED STOCK</h>
            </div> 
        </div>

        <div class="col-md-6"></div>

        <!-- Apply Tab -->
        <div class="col-md-3">  
            <form method="post">
                <input type="submit" value="APPLY" class="w1-tab-unselected" name="apply">
            </form>  
        </div>
       

    </div>  

    <div style="text-align:center">  
        <div class="w1-box">

            <!-- Column Titles -->
            <div class="row">
                <div class="col"><h>DAIRY</h></div>
                <div class="col"><h>MEAT / FISH</h></div>
                <div class="col"><h>FRUIT / VEG</h></div>
            </div>   
            <br> 

            <!-- Insert Used stock -->
            <div class="row">

                <!-- Dairy -->
                <div class="col-md-4">                      
                    <table class="styled-table" style="display:block; height:140px; overflow:auto;">
                        <tbody>
                            <?php 
                            $stock = getCurrentDairyStock();
                            for ($i=0; $i<count($stock); $i++):     
                            ?>
                            <tr> 
                                <td style="width:100px"><?php echo $stock[$i]['Item_Name']?></td>
                                <td style="padding-top:4px">
                                    <form method="post">
                                        <div class="form-group" ></div>
                                            <input type="hidden" name="dairyInput" value = "<?php $stock[$i]['Item_Name'] ?>">
                                            <input type="text" name="quantityDairyInput" placeholder="Insert quantity used">
                                        </div>
                                    </form>
                                </td>                                
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>                        
                </div>
                <!-- Meat/Fish -->
                <div class="col-md-4">                      
                    <table class="styled-table" style="display:block; height:140px; overflow:auto;">
                        <tbody>
                            <?php 
                            $stock = getCurrentMeatStock();
                            for ($i=0; $i<count($stock); $i++):     
                            ?>
                            <tr> 
                                <td style="width:100px"><?php echo $stock[$i]['Item_Name']?></td>
                                <td style="padding-top:4px">
                                    <form method="post">
                                        <div class="form-group" ></div>
                                            <input type="hidden" name="meatInput" value = "<?php $stock[$i]['Item_Name'] ?>">
                                            <input type="text" name="quantityMeatInput" placeholder="Insert quantity used">
                                        </div>
                                    </form>
                                </td>                                
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Fruit/Veg -->
                <div class="col-md-4">                      
                    <table class="styled-table" style="display:block; height:140px; overflow:auto;">
                        <tbody>
                            <?php 
                            $stock = getCurrentVegStock();
                            for ($i=0; $i<count($stock); $i++):     
                            ?>
                            <tr> 
                                <td style="width:100px"><?php echo $stock[$i]['Item_Name']?></td>
                                <td style="padding-top:4px">
                                    <form method="post">
                                        <div class="form-group" ></div>
                                            <input type="hidden" name="vegInput" value = "<?php $stock[$i]['Item_Name'] ?>">
                                            <input type="text" name="quantityVegInput" placeholder="Insert quantity used">
                                        </div>
                                    </form>
                                </td>                                
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>  
        </div> 
    </div>

    <br><br>

    <!-- Current Stock and Table ------------------------------------------------------------------------------------>
    
    <?php if($AddNew == FALSE): ?>  
    
    <div class="row">
        <!-- Current Stock Tab -->
        <div class="col-md-3" style="text-align:center">  
            <div class="w1-tab">
                <h>CURRENT STOCK</h>
            </div> 
        </div>

        <div class="col-md-6"></div>

        <!-- Add New Item-->
        <div class="col-md-3">  
            <form method="post">
                <input type="submit" value="ADD NEW" class="w1-tab-unselected" name="AddNew">
            </form>  
        </div>
    </div>  

    <div style="text-align:center">  
        <div class="w1-box row">

            <div class="col-md-8">
                <!-- CURRENT STOCK  -->
                <table class="styled-table" style="display:block; height:335px; overflow:auto;">
                    <thead>
                        <th>Item Name</th>
                        <th>In Stock</th>
                        <th>Category</th>
                        <th>Threshold</th>    
                        <th>Unit Price</th>
                        <th>Select</th>
                    </thead>
                    <tbody>
                        <?php 
                        $stock = getCurrentStock();
                        for ($i=0; $i<count($stock); $i++):     
                        ?>
                            <tr> 
                                <td><?php echo $stock[$i]['Item_Name']?></td>
                                <td><?php echo $stock[$i]['Quantity']?></td>  
                                <td><?php echo $stock[$i]['Category']?></td>
                                <td><?php echo $stock[$i]['Threshold']?>
                                <td>£<?php echo number_format((($stock[$i]['Unit_Price'])/100),2)?></td>
                                <td><a href="Index.php?Selected=<?php echo $stock[$i]['Item_Name'];?>"><button class="btn-select">select</button></a></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <!-- UPDATE SELECTED STOCK -->

            <?php if($_GET['Selected']!=""): ?>

            <div class="col-md-4 black-box">
                
                <form method="post">

                    <div class="form-group row">
                        <label class="control-label labelFont col">NAME</label>
                        <input class="col" type="text" name="UpdateItemName" value="<?php echo $SelectedItem[0][0]; ?>">
                    </div>

                    <div class="form-group row">
                        <label class="control-label labelFont col">IN STOCK</label>
                        <input class="col" type="text" name="UpdateQuantity" value="<?php echo $SelectedItem[0][4]; ?>">
                    </div>
                    <div class="form-group row">
                        <label class="control-label labelFont col">CATEGORY</label>
                        <select class="col" name="UpdateCategory"> 
                            <option value="Meat / Fish" <?php echo($SelectedItem[0][1]=="Meat / Fish") ? "selected" : ""; ?> >Meat / Fish</option> 
                            <option value="Dairy"       <?php echo($SelectedItem[0][1]=="Dairy")       ? "selected" : ""; ?> >Dairy</option> 
                            <option value="Fruit / Veg" <?php echo($SelectedItem[0][1]=="Fruit / Veg") ? "selected" : ""; ?> >Fruit / Veg</option> 
                        </select>    
                    </div>
                    <div class="form-group row">
                        <label class="control-label labelFont col">THRESHOLD</label>
                        <input class="col" type="text" name="UpdateThreshold" value="<?php echo $SelectedItem[0][3]; ?>">
                    </div>
                    <div class="form-group row">
                        <label class="control-label labelFont col-md-6">PRICE £</label>
                        <?php //variables for price display
                            $selectedPrice = $SelectedItem[0][2];
                            $selectedPence = substr($selectedPrice, -2);
                            $selectedPound = substr($selectedPrice, 0, -2);
                        ?>
                        <input class="col" type="text" name="UpdateUnitPounds" value="<?php echo $selectedPound; ?>">
                        <div class="col-md-1">.</div>
                        <input class="col" type="text" name="UpdateUnitPence" value="<?php echo $selectedPence; ?>">
                    </div>
        
                    <!-- apply and delete button -->
                    <br>
                    <div class="row">
                        <div class="col" style="text-align:center"> 
                            <form method="post">
                                <input type="hidden" name="SelectedUpdateItemName" value="<?php echo $_GET['Selected'] ?>">   
                                <input type="submit" value="APPLY" class="btn btn-main" name="edit">
                            </form>
                        </div>
                        
                        <div class="col" style="text-align:center"> 
                            <form method="post">    
                                <input type="hidden" name="selected" value ="<?php echo $_GET['Selected'] ?>">                       
                                <input type="submit" value="DELETE" class="btn btn-danger" name="delete">
                            </form>
                        </div>
                    </div> 

                </form>

            </div>
            
            <?php endif ?> 
        </div>            
    </div>        

    <?php endif; ?>      

    <!-- ADD NEW ------------------------------------------------------------------------------------>
            
    <?php if($AddNew == TRUE): ?>

    <div class="row">
        
        <div class="col-md-3">  
            <form method="post">
                <input type="submit" value="CURRENT STOCK" class="w1-tab-unselected" name="CurrentStock">
            </form>  
        </div>

        <div class="col-md-6"></div>

        <div class="col-md-3" style="text-align:center">  
            <div class="w1-tab">
                <h>ADD NEW</h>
            </div> 
        </div>
    </div>      

    <div class="w1-box">
        <form method="post">

            <div class="row" style="text-align:center">
                <div class="col">
                    <h>NAME</h>
                    <br><br>
                    <div class="form-group">
                        <input type="text" name="ItemName">
                        <span class="text-danger"><?php echo $ItemNameError; ?></span>
                    </div>
                </div>
                <div class="col">
                    <h>CATEGORY</h>
                    <br><br>
                    <div class="form-group">
                        <select class="col" name="Category" multiple="multiple" size=3> 
                            <option value="Meat / Fish">Meat / Fish</option> 
                            <option value="Dairy">      Dairy      </option> 
                            <option value="Fruit / Veg">Fruit / Veg</option> 
                        </select> 
                        <span class="text-danger"><?php echo $CategoryError; ?></span>
                    </div>
                </div>
                <div class="col">
                    <h>UNIT PRICE £</h>
                    <br><br>
                    <div class="row">
                    
                        <div class="col">
                            <div class="form-group a">
                                <input class="a" type="text" name="UnitPounds" placeholder="Pounds">
                                <span class="text-danger"><?php echo $UnitPoundsError; ?></span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <input type="text" name="UnitPence" placeholder="Pence">
                                <span class="text-danger"><?php echo $UnitPenceError; ?></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col">
                    <h>THRESHOLD</h>
                    <br><br>
                    <div class="form-group">
                        <input type="text" name="Threshold">
                        <span class="text-danger"><?php echo $ThresholdError; ?></span>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group" style="height: 100%">
                        <br><br>
                        <input class="btn btn-main" type="submit" value="ADD" name="add"></input> 
                    </div>
                </div>
            </div> 
        </form>
    </div>

    <?php endif; ?> 

    </div><!-- container -->
    <br><br> 
            
</body>
