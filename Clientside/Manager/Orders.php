<?php 

//error_reporting(0);
$ErrorMessage = "";

include("../../Serverside/Sessions.php");
include("../../Serverside/Functions.php");
include("../../Serverside/phpmailer/email.php");

//session
$path = "ManagerLogin.php";
session_start(); 
if (!isset($_SESSION['Username'])) {
    session_unset();
    session_destroy();
    header("Location:".$path);
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING FROM DB AND NAME VARIABLES --------------------------------------------------------------

$supplier = getSupplier();                   //Getting all suppliers info

$selected = $_GET['Selected'];               //Selected item
$SelectedItem = getSelectedStock($selected); //getting all info for selected item
$N = $SelectedItem[0][0];                    //Selected item name
$UnitPrice = $SelectedItem[0][2];            //Selected item price per unit

//-------------------------------------------------------------------------------------------------------
//----- ADD ITEM TO ORDER -------------------------------------------------------------------------------

if (isset($_POST['Add'])) {                  //add item to Item_Order
    try {
        addToOrder($UnitPrice);
    } catch(exception $e) {
        $ErrorMessage = $e->getMessage();
    }
}

//-------------------------------------------------------------------------------------------------------
//----- PLACING ORDER -----------------------------------------------------------------------------------

$AlertD = FALSE;                        //Setting alerts
$AlertM = FALSE;
$AlertF = FALSE;

if (isset($_POST['PlaceDO'])) {         //Placing Dairy order
    $AlertD = TRUE;
    $total = TotalIO($supplier[0][0]);
    placeOrder($supplier[0][0],$total);
    sendEmail($supplier[0][0], $supplier[0][1]);
}

if (isset($_POST['PlaceMO'])) {         //Placing Meat / Fish order
    $AlertM = TRUE;
    $total = TotalIO($supplier[1][0]);
    placeOrder($supplier[1][0],$total);
    sendEmail($supplier[1][0], $supplier[1][1]);
}

if (isset($_POST['PlaceFO'])) {         //Placing Fruit / Veg order
    $AlertF = TRUE;
    $total = TotalIO($supplier[2][0]);
    placeOrder($supplier[2][0],$total);
    sendEmail($supplier[2][0], $supplier[2][1]);
}

//-------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------

echo $AlertM;

?>

<body>
    <?php nav("order");?>
    <div class="container">

    <!-- ALERT: ERROR MESSAGE -->
    <?php if ($ErrorMessage != ""): ?>
        <div class="alert alert-danger" role="alert" style="font-weight:bold; width:fit-content">
            ERROR MESSAGE: <?php echo $ErrorMessage ?>
        </div>
    <?php endif; ?>

    <div style="text-align:center">  
        <div class="w1-box">
            <div class="row">

                <!-- Dairy
                ---------------------------------------------------------------------------------------------->
                <div class="col-md-4">
                                      
                    <h><?php echo strtoupper($supplier[0][0]) ?></h>
                    <br><br><br>
                    <p>BELOW THRESHOLD ITEMS</p>

                    <!-- below threshold items -->
                    <?php 
                    $IO = getItemOrder("Dairy");
                    if ($IO[0][0] != "") :
                    ?>
                        <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                            <thead>
                                <th>Item</th>
                                <th>Select</th> 
                                <th>Order Quantity</th>   
                            </thead>
                            <tbody>
                                <?php for ($i=0; $i<count($IO); $i++):?>
                                <tr> 
                                    <td><?php echo $IO[$i][0]?></td>                                                           
                                    <td><a href="?Selected=<?php echo $IO[$i][0];?>"><button class="btn-select">select</button></a></td>
                                    <td><?php echo $IO[$i][1]?></td> 
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                        <br>
                    <?php else: ?>  
                        <hr>
                        <p>All Dairy items are currently above the set threshold.</p>
                        <!-- ALERT: Order Placed -->
                        <?php if ($AlertD == TRUE): ?>
                                <br>
                                <div class="alert alert-success showcol-10" role="alert" style="font-weight: bold;">
                                    Dairy Order Placed
                                </div>
                            <?php endif ?>
                    <?php endif ?>

                    <!-- Add item to order -->
                    <?php if (($SelectedItem[0][1] == "Dairy") && ($IO[0][0] != "")) :?>
                        <br>
                        <form method="post"> 
                            <div class="row">
                                <div class="col-md-2">
                                    <p>Order</p>
                                </div>
                                <div class="col-md-2" style="text-align:center">
                                    <div class="form-group">
                                        <input type="text" style="width:100%" name="OrderQuantity" placeholder="">
                                    </div> 
                                </div>
                                <div class="col" style="text-align:left">
                                    <p>number of <?php echo $selected ?> <?php ?></p>
                                </div>                               
                            </div>
                            <div class="form-group" style="text-align:left">
                                <input class="btn btn-main" style="width:50%" type="submit" value="ADD TO ORDER" name="Add"></input> 
                            </div> 
                        </form>
                        <br>
                    <?php endif ?>
                    
                    <!-- current order -->
                    <?php if (TotalIO("Dairy") != 0) :?>
                        <div class="black-box">
                            <p>CURRENT ORDER WITH <?php echo $supplier[0][1] ?></p>
                            <?php 
                            $AIO = getAddedItemOrder("Dairy");
                            for ($i=0; $i<count($AIO); $i++):  
                            ?>
                            <p><?php echo $AIO[$i][1], " x ",$AIO[$i][0], " = £", number_format((($AIO[$i][3])/100),2);?></p>
                            <?php endfor ?>
                            <br>
                            <p>TOTAL PRICE: £<?php echo number_format(((TotalIO("Dairy"))/100),2); ?></p>
                            <form method="post">
                                <div class="form-group">        
                                    <input class="btn btn-main" style="width:50%" type="submit" value="PLACE ORDER" name="PlaceDO"></input> 
                                </div> 
                            </form>
                        </div>
                    <?php endif ?>    

                </div>


                <!-- MEAT / FISH 
                ---------------------------------------------------------------------------------------------->
                <div class="col-md-4">
                                      
                    <h><?php echo strtoupper($supplier[1][0]) ?></h>
                    <br><br><br>
                    <p>BELOW THRESHOLD ITEMS</p>

                    <!-- below threshold items -->
                    <?php 
                    $IO = getItemOrder("Meat / Fish");
                    if ($IO[0][0] != "") :
                    ?>
                        <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                            <thead>
                                <th>Item</th>
                                <th>Select</th> 
                                <th>Order Quantity</th>   
                            </thead>
                            <tbody>
                                <?php for ($i=0; $i<count($IO); $i++): ?>
                                <tr> 
                                    <td><?php echo $IO[$i][0]?></td>                                                           
                                    <td><a href="?Selected=<?php echo $IO[$i][0];?>"><button class="btn-select">select</button></a></td>
                                    <td><?php echo $IO[$i][1]?></td> 
                                </tr>
                                <?php endfor ?>
                            </tbody>
                        </table>
                        <br>
                    <?php else: ?>  
                        <hr>
                        <p>All Meat / Fish items are currently above the set threshold.</p>
                        <!-- ALERT: Order Placed -->
                        <?php if ($AlertM == TRUE): ?>
                                <br>
                                <div class="alert alert-success showcol-10" role="alert" style="font-weight: bold;">
                                    Meat / Fish Order Placed
                                </div>
                            <?php endif ?>
                    <?php endif ?>   

                    <!-- Add item to order -->
                    <?php if (($SelectedItem[0][1] == "Meat / Fish") && ($IO[0][0] != "")) :?>
                        <form method="post"> 
                            <div class="row">
                                <div class="col-md-2">
                                    <p>Order</p>
                                </div>
                                <div class="col-md-2" style="text-align:center">
                                    <div class="form-group">
                                        <input type="text" style="width:100%" name="OrderQuantity" placeholder="">
                                    </div> 
                                </div>
                                <div class="col" style="text-align:left">
                                    <p>number of <?php echo $selected ?> <?php ?></p>
                                </div>                               
                            </div>
                            <div class="form-group" style="text-align:left">
                                <input class="btn btn-main" style="width:50%" type="submit" value="ADD TO ORDER" name="Add"></input> 
                            </div> 
                        </form>
                        <br>
                    <?php endif ?>   
                    
                    <!-- current order -->
                    <?php if (TotalIO("Meat / Fish") != 0) :?>
                        <div class="black-box">
                            <p>CURRENT ORDER WITH <?php echo $supplier[1][1] ?></p>
                            <?php 
                            $AIO = getAddedItemOrder("Meat / Fish");
                            for ($i=0; $i<count($AIO); $i++):  
                            ?>
                            <p><?php echo $AIO[$i][1], " x ",$AIO[$i][0], " = £", number_format((($AIO[$i][3])/100),2);?></p>
                            <?php endfor ?>
                            <br>
                            <p>TOTAL PRICE: £<?php echo number_format(((TotalIO("Meat / Fish"))/100),2); ?></p>
                            <form method="post">
                                <div class="form-group">        
                                    <input class="btn btn-main" style="width:50%" type="submit" value="PLACE ORDER" name="PlaceMO"></input> 
                                </div> 
                            </form>
                        </div>
                    <?php endif ?>
                </div>


                <!-- FRUIT / VEG 
                ---------------------------------------------------------------------------------------------->
                <div class="col-md-4">
                                      
                    <h><?php echo strtoupper($supplier[2][0]) ?></h>
                    <br><br><br>
                    <p>BELOW THRESHOLD ITEMS</p>

                    <!-- below threshold items -->
                    <?php 
                    $IO = getItemOrder("Fruit / Veg");
                    if ($IO[0][0] != "") :
                    ?>
                        <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                            <thead>
                                <th>Item</th>
                                <th>Select</th> 
                                <th>Order Quantity</th>   
                            </thead>
                            <tbody>
                                <?php for ($i=0; $i<count($IO); $i++): ?>
                                <tr> 
                                    <td><?php echo $IO[$i][0]?></td>                                                           
                                    <td><a href="?Selected=<?php echo $IO[$i][0];?>"><button class="btn-select">select</button></a></td>
                                    <td><?php echo $IO[$i][1]?></td> 
                                </tr>
                                <?php endfor ?>
                            </tbody>
                        </table>
                        <br>
                    <?php else: ?>  
                        <hr>
                        <p>All Fruit / Veg items are currently above the set threshold.</p>
                        <!-- ALERT: Order Placed -->
                        <?php if ($AlertF == TRUE): ?>
                            <br>
                            <div class="alert alert-success showcol-10" role="alert" style="font-weight: bold;">
                                Fruit / Veg Order Placed
                            </div>
                        <?php endif ?>
                    <?php endif ?>  
                    
                    <!-- Add item to order -->
                    <?php if (($SelectedItem[0][1] == "Fruit / Veg") && ($IO[0][0] != "")) :?>
                        <form method="post"> 
                            <div class="row">
                                <div class="col-md-2">
                                    <p>Order</p>
                                </div>
                                <div class="col-md-2" style="text-align:center">
                                    <div class="form-group">
                                        <input type="text" style="width:100%" name="OrderQuantity" placeholder="">
                                    </div> 
                                </div>
                                <div class="col" style="text-align:left">
                                    <p>number of <?php echo $selected ?> <?php ?></p>
                                </div>                               
                            </div>
                            <div class="form-group" style="text-align:left">
                                <input class="btn btn-main" style="width:50%" type="submit" value="ADD TO ORDER" name="Add"></input> 
                            </div> 
                        </form>
                        <br>
                    <?php endif ?>  
                    
                    <!-- current order -->
                    <?php if (TotalIO("Fruit / Veg") != 0) :?>
                        <div class="black-box">
                            <p>CURRENT ORDER WITH <?php echo $supplier[2][1] ?></p>
                            <?php 
                            $AIO = getAddedItemOrder("Fruit / Veg");
                            for ($i=0; $i<count($AIO); $i++):  
                            ?>
                            <p><?php echo $AIO[$i][1], " x ",$AIO[$i][0], " = £", number_format((($AIO[$i][3])/100),2);?></p>
                            <?php endfor; ?>
                            <br>
                            <p>TOTAL PRICE: £<?php echo number_format(((TotalIO("Fruit / Veg"))/100),2); ?></p>
                            <form method="post">
                                <div class="form-group">        
                                    <input class="btn btn-main" style="width:50%" type="submit" value="PLACE ORDER" name="PlaceFO"></input> 
                                </div> 
                            </form>
                        </div>
                    <?php endif ?>
                </div>

            </div>   
        </div> 
    </div>
    
</body>