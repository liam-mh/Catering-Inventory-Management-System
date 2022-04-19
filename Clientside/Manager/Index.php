<?php 

//error_reporting(0);

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

$AddNew = FALSE;                                //Boolean if add new tab is selected
if (isset($_POST['AddNew'])) {$AddNew = TRUE;}  //Add New tab
if (isset($_POST['CurrentStock'])) {            //Current stock tab
    $AddNew = FALSE;
    $allFields = "";
}

$AlertA = FALSE;                                //Set alert boolean - Stock added 

//Setting error variables
$ItemNameError = $CategoryError = $UnitPoundsError = $UnitPenceError = $ThresholdError = ""; 

//add new item 
$allFields = "yes";
if (isset($_POST['add'])) {

    //Checking all fields are inputted
    if ($_POST['ItemName']=="")   {$ItemNameError   = "EMPTY!"; $allFields = "no";}
    if ($_POST['Category']=="")   {$CategoryError   = "EMPTY!"; $allFields = "no";}
    if ($_POST['UnitPounds']=="") {$UnitPoundsError = "EMPTY!"; $allFields = "no";}
    if ($_POST['UnitPence']=="")  {$UnitPenceError  = "EMPTY!"; $allFields = "no";}
    if ($_POST['Threshold']=="")  {$ThresholdError  = "EMPTY!"; $allFields = "no";}

    if ($allFields == "yes") {
        addNew();
        $AlertA = TRUE;
    }
    $AddNew = TRUE;
}

//-------------------------------------------------------------------------------------------------------
//----- CURRENT STOCK / EDIT & DELETE -------------------------------------------------------------------

$selected = $_GET['Selected'];                   //Selected item
$SelectedItem = getSelectedStock($selected);     //getting all info for selected item

if (isset($_POST['edit'])) {updateSelected();}   //update selected
if (isset($_POST['delete'])) {deleteSelected();} //delete selected

$AlertQ = FALSE;                                 //Set alert boolean - Quantity inserted larger than in stock
$AlertT = FALSE;                                 //Set alert boolean - item now below threshold

if (isset($_POST['insert'])) {                   

    $alert = insertStock($SelectedItem);         //insert used quantity into selected item
    $AlertQ = $alert[0];                         //Item quantity too large alert                      
    $AlertT = $alert[1];                         //Item below threshold alert                                
}

//----- FILTERS FOR STOCK -------------------------------------------------------------------------------

$sortBy = $_SESSION['sortBy'];                            //Retrieving last filter selected before url change

if (isset($_POST['Name']))      {$sortBy = "Name";}       //Filters for table
if (isset($_POST['Cat']))       {$sortBy = "Category";}   //^
if (isset($_POST['Threshold'])) {$sortBy = "Threshold";}  //^

$_SESSION['sortBy'] = $sortBy;                            //Passing last selected filter when selecting an item

//-------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------

?>

<body>
    <?php nav("home");?>
    <div class="container">

    <!-- Current Stock and Table
    ---------------------------------------------------------------------------------------------->
    
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
                <div class="alert alert-danger" role="alert" style="font-weight: bold;">
                    Insert used quantity larger than current stock level
                </div>
            <?php endif; ?>
            <!-- ALERT: Item below threshold -->
            <?php if ($AlertT == TRUE): ?>
                <div class="alert alert-danger" role="alert" style="font-weight: bold;">
                    <?php echo $selected; ?> is below set threshold, and has been added to orders page.
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
    
    <!-- CURRENT STOCK  -->
    <div style="text-align:center">  
        <div class="w1-box row">

            <div class="col-md-8">

                <div class="row" style="text-align:left">

                    <div class="col-md-3">
                        <p>SORT TABLE ITEMS :</p>
                    </div>
                    <div class="col-md-2">
                        <form method="post">
                            <input type="submit" value="NAME" style="font-size:10" class="btn-select" name="Name">
                        </form> 
                    </div>
                    <div class="col-md-2">
                        <form method="post">
                            <input type="submit" value="CATEGORY" style="font-size:10" class="btn-select" name="Cat">
                        </form> 
                    </div>
                    <div class="col-md-3">
                        <form method="post">
                            <input type="submit" value="BELOW THRESHOLD" style="font-size:10" class="btn-select" name="Threshold">
                        </form> 
                    </div>
                    
                </div>

                <table class="styled-table" style="display:block; height:530px; overflow:auto;">
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
                        //get stock loop for table
                        $stock = getCurrentStock();  
                        if ($sortBy == "Category")  {$stock = getCurrentStockByCat();}        
                        if ($sortBy == "Threshold") {$stock = getCurrentStockBelow();}        
                        for ($i=0; $i<count($stock); $i++): 

                        //if below threshold set colour to highlight
                        $below = "";   
                        if ($stock[$i]['Quantity'] < $stock[$i]['Threshold']) {
                            $below = "style=color:#E8175D";  
                        }    
                        ?>
                            <tr> 
                                <td <?php echo $below ?> ><?php echo $stock[$i]['Item_Name']?></td>
                                <td <?php echo $below ?> ><?php echo $stock[$i]['Quantity']?></td>  
                                <td><?php echo $stock[$i]['Threshold']?>
                                <td><?php echo $stock[$i]['Category']?></td>
                                <td>£<?php echo number_format((($stock[$i]['Unit_Price'])/100),2)?></td>
                                <td><a href="Index.php?Selected=<?php echo $stock[$i]['Item_Name'];?>"><button class="btn-select">select</button></a></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <?php if($_GET['Selected']==""): ?>
                <div class="col-md-4">
                    <b>The White Horse Inn</b><br>
                    <b>Inventory Management System</b><br>
                    <hr>
                    <p>Please select an item of stock to Insert Used stock or edit / delete an item.</p>
                    <br>
                    <p>Or press the 'ADD NEW' tab to insert a new stock item into your current inventory.</p>
                    <hr>
                    <p>Items displayed on the left that are highlighted with their 'in stock' number are below the assigned threshold.</p>
                </div>
            <?php endif ?>

            <!-- SELECTED STOCK -->

            <?php if($_GET['Selected']!=""): ?>

            <div class="col-md-4">

                <div style="text-align:center">
                    <h style="color: #E8175D">STOCK ITEM SELECTED: <?php echo $_GET['Selected']?></h>
                </div>

                <br>

                <?php if ($SelectedItem[0][4] != 0): ?>
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
                <?php endif ?>

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
                            <?php 
                                //variables for price display
                                $selectedPrice = $SelectedItem[0][2];
                                $selectedPound = substr($selectedPrice, 0, -2);
                                $selectedPence = substr($selectedPrice, -2);
                    
                                //changing null fields to 0
                                $selectedPound = ($SelectedItem[0][2] < 100) ? '0' : substr($selectedPrice, 0, -2);
                                $selectedPence = (substr($selectedPrice, 0, -2) == 0) ? '0' : substr($selectedPrice, -2);
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


    <!-- Add New
    ---------------------------------------------------------------------------------------------->
            
    <?php if($AddNew == TRUE): ?>

    <div class="row">
        
        <div class="col-md-3">  
            <form method="post">
                <input type="submit" value="CURRENT STOCK" class="w1-tab-unselected" name="CurrentStock">
            </form>  
        </div>

        <div class="col-md-6">
            <!-- ALERT: New stock item added -->
            <?php if ($AlertA == TRUE): ?>
                <div class="alert alert-success" role="alert" style="font-weight: bold;">
                    New item successfully added to stock table.
                </div>
            <?php endif; ?>
            <!-- ALERT: Invalid inputs -->
            <?php if ($allFields == "no"): ?>
                <div class="alert alert-danger" role="alert" style="font-weight: bold;">
                    All fields need to be filled out to insert new stock.
                </div>
            <?php endif; ?>
        </div>

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
                            <div class="form-group">
                                <input type="text" name="UnitPounds" placeholder="Pounds">
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
