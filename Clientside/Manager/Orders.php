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
//----- GETTING FROM DB AND NAME VARIABLES --------------------------------------------------------------

$supplier = getSupplier();                   //Getting all suppliers info

$selected = $_GET['Selected'];               //Selected item
$SelectedItem = getSelectedStock($selected); //getting all info for selected item
$N = $SelectedItem[0][0];                    //Selected item name
$UnitPrice = $SelectedItem[0][2];            //Selected item price per unit

//-------------------------------------------------------------------------------------------------------
//----- ADD ITEM TO ORDER -------------------------------------------------------------------------------

//add item to Item_Order
if (isset($_POST['Add'])) {

    //Calculating total price of order
    $total = $UnitPrice * $_POST['OrderQuantity'];

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Item_Order 
            SET Order_Quantity=:Quantity, Total=:Total 
            WHERE Item_Name=:ItemName';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':ItemName', $N, SQLITE3_TEXT);
    $stmt->bindParam(':Quantity', $_POST['OrderQuantity'], SQLITE3_INTEGER);
    $stmt->bindParam(':Total',    $total, SQLITE3_INTEGER);
    $stmt->execute();
}

//-------------------------------------------------------------------------------------------------------
//----- PLACING ORDER -----------------------------------------------------------------------------------

//Placing Dairy order
if (isset($_POST['PlaceDO'])) {
    $AlertD = TRUE;
    $total = TotalIO("Dairy");
    placeOrder("Dairy",$total);
}
//Placing Meat / Fish order
if (isset($_POST['PlaceMO'])) {
    $AlertM = TRUE;
    $total = TotalIO("Meat / Fish");
    placeOrder("Meat / Fish",$total);
}
//Placing Fruit / Veg order
if (isset($_POST['PlaceFO'])) {
    $AlertF = TRUE;
    $total = TotalIO("Fruit / Veg");
    placeOrder("Fruit / Veg",$total);
}

//-------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------

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
                    <p>BELOW THRESHOLD ITEMS</p>
                    <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                        <thead>
                            <th>Item</th>
                            <th>Select</th> 
                            <th>Order Quantity</th>   
                        </thead>
                        <tbody>
                            <?php 
                            $IO = getItemOrder("Dairy");
                            for ($i=0; $i<count($IO); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $IO[$i][0]?></td>                                                           
                                <td><a href="?Selected=<?php echo $IO[$i][0];?>"><button class="btn-select">select</button></a></td>
                                <td><?php echo $IO[$i][1]?></td> 
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br><br>

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
                    
                    <div class="black-box">
                        <p>CURRENT ORDER WITH <?php echo $supplier[0][1] ?></p>
                        <?php 
                        $AIO = getAddedItemOrder("Dairy");
                        for ($i=0; $i<count($AIO); $i++):  
                        ?>
                        <p><?php echo $AIO[$i][1], " x ",$AIO[$i][0], " = £", number_format((($AIO[$i][3])/100),2);?></p>
                        <?php endfor; ?>
                        <br>
                        <p>TOTAL PRICE: £<?php echo number_format(((TotalIO("Dairy"))/100),2); ?></p>
                        <form method="post">
                            <div class="form-group">        
                                <input class="btn btn-main" style="width:50%" type="submit" value="PLACE ORDER" name="PlaceDO"></input> 
                            </div> 
                        </form>
                        <!-- ALERT: Order Placed -->
                        <?php if ($AlertD == TRUE): ?>
                            <br>
                            <div class="alert alert-success showcol-10" role="alert" style="font-weight: bold;">
                                Dairy Order Placed
                            </div>
                        <?php endif; ?>
                    </div>

                </div>


                <!-- MEAT / FISH -->
                <div class="col-md-4">
                                      
                    <h><?php echo strtoupper($supplier[1][0]) ?></h>
                    <br><br><br>
                    <p>BELOW THRESHOLD ITEMS</p>
                    <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                        <thead>
                            <th>Item</th>
                            <th>Select</th> 
                            <th>Order Quantity</th>   
                        </thead>
                        <tbody>
                            <?php 
                            $IO = getItemOrder("Meat / Fish");
                            for ($i=0; $i<count($IO); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $IO[$i][0]?></td>                                                           
                                <td><a href="?Selected=<?php echo $IO[$i][0];?>"><button class="btn-select">select</button></a></td>
                                <td><?php echo $IO[$i][1]?></td> 
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br><br>

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
                    
                    <div class="black-box">
                        <p>CURRENT ORDER WITH <?php echo $supplier[1][1] ?></p>
                        <?php 
                        $AIO = getAddedItemOrder("Meat / Fish");
                        for ($i=0; $i<count($AIO); $i++):  
                        ?>
                        <p><?php echo $AIO[$i][1], " x ",$AIO[$i][0], " = £", number_format((($AIO[$i][3])/100),2);?></p>
                        <?php endfor; ?>
                        <br>
                        <p>TOTAL PRICE: £<?php echo number_format(((TotalIO("Meat / Fish"))/100),2); ?></p>
                        <form method="post">
                            <div class="form-group">        
                                <input class="btn btn-main" style="width:50%" type="submit" value="PLACE ORDER" name="PlaceMO"></input> 
                            </div> 
                        </form>
                        <!-- ALERT: Order Placed -->
                        <?php if ($AlertM == TRUE): ?>
                            <br>
                            <div class="alert alert-success showcol-10" role="alert" style="font-weight: bold;">
                                Meat / Fish Order Placed
                            </div>
                        <?php endif; ?>
                    </div>

                </div>


                <!-- FRUIT / VEG -->
                <div class="col-md-4">
                                      
                    <h><?php echo strtoupper($supplier[2][0]) ?></h>
                    <br><br><br>
                    <p>BELOW THRESHOLD ITEMS</p>
                    <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                        <thead>
                            <th>Item</th>
                            <th>Select</th> 
                            <th>Order Quantity</th>   
                        </thead>
                        <tbody>
                            <?php 
                            $IO = getItemOrder("Fruit / Veg");
                            for ($i=0; $i<count($IO); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $IO[$i][0]?></td>                                                           
                                <td><a href="?Selected=<?php echo $IO[$i][0];?>"><button class="btn-select">select</button></a></td>
                                <td><?php echo $IO[$i][1]?></td> 
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br><br>

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
                        <!-- ALERT: Order Placed -->
                        <?php if ($AlertF == TRUE): ?>
                            <br>
                            <div class="alert alert-success showcol-10" role="alert" style="font-weight: bold;">
                                Fruit / Veg Order Placed
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>   
        </div> 
    </div>
    
</body>