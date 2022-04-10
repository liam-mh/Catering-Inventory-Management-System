<?php 

include("../../Serverside/Sessions.php");
include("../../Serverside/Functions.php");

//session
$path = "ManagerLogin.php";
session_start(); 
if (!isset($_SESSION['Username'])) {
    session_unset();
    session_destroy();
    header("Location:".$path);
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

    //Checking all fields are inputted
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

$selected = $_GET['Selected'];                   //Selected item
$SelectedItem = getSelectedStock($selected);     //getting all info for selected item

if (isset($_POST['edit'])) {updateSelected();}   //update selected
if (isset($_POST['delete'])) {deleteSelected();} //delete selected

$AlertQ = FALSE;                                 //Set alert boolean 
$AlertT = FALSE;                                 //Set alert boolean

//insert quantity into selected
if (isset($_POST['insert'])) {

    //Subtracting insert quantity
    $CurrentQ = $SelectedItem[0][4];
    $InsertQ = $_POST['InsertQuantity'];
    $NewQ = $CurrentQ-$InsertQ;

    //Selected item data
    $N = $SelectedItem[0][0];
    $Threshold = $SelectedItem[0][3];
    $Cat = $SelectedItem[0][1];

    if ($NewQ < 0) {

        //insert number larger than current stock alert
        $AlertQ = TRUE; 

    } else {

        //Updating quantity
        $db = new SQLite3('/Applications/MAMP/db/IMS.db');
        $sql = 'UPDATE Stock SET Quantity=:NewQ WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name', $N, SQLITE3_TEXT);
        $stmt->bindParam(':NewQ', $NewQ, SQLITE3_INTEGER);
        $stmt->execute();  

        //Add item to Item_Order table if below threshold
        if ($NewQ < $Threshold) {

            $AlertT = TRUE;

            $db = new SQLite3('/Applications/MAMP/db/IMS.db');
            $sql = 'INSERT INTO Item_Order (Item_Name, Category) 
                    VALUES (:ItemName, :Category)';
            $stmt = $db->prepare($sql); 
            $stmt->bindParam(':ItemName', $N, SQLITE3_TEXT);
            $stmt->bindParam(':Category', $Cat, SQLITE3_TEXT);
            $stmt->execute();  
        }

        header("Refresh:0");
    }
}

//-------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------

?>

<body>
    <?php require("ManagerNavbar.php");?>
    <div class="container">

    <!-- Current Stock and Table ------------------------------------------------------------------------------------>
    
    <?php if($AddNew == FALSE): ?>  
    
    <div class="row">
        <!-- Current Stock Tab -->
        <div class="col-md-3" style="text-align:center">  
            <div class="w1-tab">
                <h>CURRENT STOCK</h>
            </div> 
        </div>

        <!-- ALERTS -->
        <div class="col-md-6">
            <!-- ALERT: insert larger than quantity -->
            <?php if ($AlertQ == TRUE): ?>
                <div class="alert alert-danger alert-dismissible showcol-10" role="alert" style="font-weight: bold;">
                    Insert used quantity larger than current stock level
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <!-- ALERT: Item below threshold -->
            <?php if ($AlertT == TRUE): ?>
                <div class="alert alert-danger alert-dismissible showcol-10" role="alert" style="font-weight: bold;">
                    <?php echo $selected; ?> is below set threshold, and has been added to orders page.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>

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
                <table class="styled-table" style="display:block; height:570px; overflow:auto;">
                    <thead>
                        <th>Item Name</th>
                        <th>In Stock</th>
                        <th>Threshold</th> 
                        <th>Category</th>   
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
                                <td><?php echo $stock[$i]['Threshold']?>
                                <td><?php echo $stock[$i]['Category']?></td>
                                <td>£<?php echo number_format((($stock[$i]['Unit_Price'])/100),2)?></td>
                                <td><a href="Index.php?Selected=<?php echo $stock[$i]['Item_Name'];?>"><button class="btn-select">select</button></a></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <!-- SELECTED STOCK -->

            <?php if($_GET['Selected']!=""): ?>

            <div class="col-md-4">

                <div style="text-align:center">
                    <h style="color: #E8175D">STOCK ITEM SELECTED: <?php echo $_GET['Selected']?></h>
                </div>

                <br>

                <div class="black-box">

                    <p>INSERT USED STOCK</p>

                    <form method="post">

                        <div class="form-group row">
                            <input class="col" type="text" name="InsertQuantity" placeholder="Quantity">
                            <div class="col" style="text-align:center">                             
                                <input type="hidden" name="SelectedUpdateItemName" value="<?php echo $_GET['Selected'] ?>">   
                                <input type="submit" value="INSERT" class="btn btn-main" name="insert">
                            </div>
                        </div>
    
                    </form>
                </div>   

                <br>

                <!-- UPDATE SELECTED STOCK -->
                <div class="black-box">

                    <p>UPDATE / DELETE</p>

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
