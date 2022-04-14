<?php 

//error_reporting(0);

include("../../Serverside/Sessions.php");
include("../../Serverside/Functions.php");
//include("../../Serverside/OrderPDF.php");

//session
$path = "SupplierLogin.php";
session_start(); 
if (!isset($_SESSION['Username'])) {
    session_unset();
    session_destroy();
    header("Location:".$path);
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING VALUES FROM DB --------------------------------------------------------------------------

$SupplierName = $_SESSION['Username'];             //Logged in supplier
$category = getLoggedInSupplierCat($SupplierName); //Getting logged in supplier category
$c = $category[0][0];                              //Current category from array
$orderDate = getOrderDate ($category);             //Getting Order date from Whole_Order for logged in supplier

$total = TotalPIO($category[0][0]);                //order total for logged in category
$PIO = getPlacedItemOrder($category[0][0]);        //Getting all times placed in order 

//-------------------------------------------------------------------------------------------------------
//----- ACCEPT / DECLINE ORDERS -------------------------------------------------------------------------

if (isset($_POST['accept'])) {                     //Accept
    $AD = "Accepted";
    OrderAD($c, $total, $PIO, $AD);
}

if (isset($_POST['decline'])) {                    //Decline
    $AD = "Declined";
    OrderAD($c, $total, $PIO, $AD);
}

//-------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------

?>

<body>
    <?php require("SupplierNavbar.php");?>
    <div class="container">
  
        <div class="row">

            <div class="col"></div>

            <div class="col-md-5">
                
                <!-- ALERT: Accepted order -->
                <?php if ($_GET['Order'] == "Accepted"): ?>
                    <div class="alert alert-success showcol-10" role="alert" style="font-weight: bold;">
                        Order Accepted
                    </div>
                <?php endif; ?>
                <!-- ALERT: Declined order -->
                <?php if ($_GET['Order'] == "Declined"): ?>
                    <div class="alert alert-danger showcol-10" role="alert" style="font-weight: bold;">
                        Order Declined
                    </div>
                <?php endif; ?>

                <div class="w1-box">
                    <p style="text-align:center"><?php echo $SupplierName ?> Current Order</p>
                    <br>           
                    <p>ORDER DATE: <?php echo $orderDate[0][0] ?></p>
                    <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                        <thead>
                            <th>Item</th> 
                            <th>Order Quantity</th>  
                            <th>Total</th> 
                        </thead>
                        <tbody>
                            <?php 
                            for ($i=0; $i<count($PIO); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $PIO[$i][0]?></td>                                                           
                                <td><?php echo $PIO[$i][1]?></td> 
                                <td>£<?php echo number_format((($PIO[$i][3])/100),2)?></td> 
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br>
                    <p>ORDER TOTAL: £<?php echo number_format((($total)/100),2); ?></p>

                    <!-- Accept and Decline buttons -->                    
                    <form method="post">
                        <div class="row">
                            <div class="col" style="text-align:center">  
                                <input type="submit" value="ACCEPT" class="btn btn-main" name="accept">
                            </div>                            
                            <div class="col" style="text-align:center">                                                                      
                                <input type="submit" value="DECLINE" class="btn btn-danger" name="decline">
                            </div>
                        </div> 
                    </form>

                    <p>Accepting or Declining an order will generate a confirmation PDF and will log you out.</p>
                
                </div>
            </div> 
            
            <div class="col"></div>
        </div> 
    </div>
</body>