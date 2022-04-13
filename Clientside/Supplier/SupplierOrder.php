<?php 

//error_reporting(0);

include("../../Serverside/Sessions.php");
include("../../Serverside/Functions.php");

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

//accept
if (isset($_POST['accept'])) {

    $AD = "Accepted";
    AcceptOrder($c, $total, $PIO, $AD);
}


function AcceptOrder($category, $total, $PIO, $AD) {

    //current date 
    $date  = new DateTime(); 
    $d = $date->format('d/m/y');
    
    //Inserting result into PDF table
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO PDF (PDF_Date, Category, Order_Total, Accept_Decline) 
            VALUES (:D, :Cat, :Total, :AD)';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':D',     $d, SQLITE3_TEXT);
    $stmt->bindParam(':Cat',   $category, SQLITE3_TEXT);
    $stmt->bindParam(':Total', $total, SQLITE3_INTEGER);
    $stmt->bindParam(':AD',    $AD, SQLITE3_TEXT);
    $stmt->execute();

    //Adding stock to Stock table 
    for ($i=0; $i<count($PIO); $i++) {

        //variables for name and quantity of placed order
        $Name = $PIO[$i][0];
        $AcceptQuantity = $PIO[$i][1];

        //Getting remaining stock from stock table
        $db = new SQLite3('/Applications/MAMP/db/IMS.db');
        $sql = 'SELECT Quantity FROM Stock WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name', $Name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $oldQ = [];
        while($row=$result->fetchArray(SQLITE3_NUM)){$oldQ [] = $row;}
        $OQ = $oldQ[0][0];

        //Adding new quantity onto remaining stock
        $total = $OQ + $AcceptQuantity;

        //Inserting new qauntity into Stock table
        $sql = 'UPDATE Stock 
                SET Quantity=:Quantity 
                WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name',     $Name, SQLITE3_TEXT);
        $stmt->bindParam(':Quantity', $total, SQLITE3_INTEGER);
        $stmt->execute();

        //Removing items from Item_Order table as back above threshold
        $sql = 'DELETE FROM Item_Order WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name', $Name, SQLITE3_TEXT);
        $stmt->execute();
    }     

    //Updating whole order
    updateWhole_Order("NO CURRENT ORDERS", $total, $category);

    header("Refresh:0");
    header("Location:SupplierOrder.php?Order=A");
}





//decline
if (isset($_POST['decline'])) {

    $AD = "Declined";
    DeclineOrder($c, $total, $AD, $PIO);
}

function DeclineOrder($c, $t, $AD, $PIO) {

    //current date 
    $date  = new DateTime(); 
    $d = $date->format('d/m/y');

    //Inserting result into PDF table
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO PDF (PDF_Date, Category, Order_Total, Accept_Decline) 
            VALUES (:D, :Cat, :Total, :AD)';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':D',     $d, SQLITE3_TEXT);
    $stmt->bindParam(':Cat',   $c, SQLITE3_TEXT);
    $stmt->bindParam(':Total', $t, SQLITE3_INTEGER);
    $stmt->bindParam(':AD',    $AD, SQLITE3_TEXT);
    $stmt->execute();

    //Changing placed order items to 0 in Item_Order table
    for ($i=0; $i<count($PIO); $i++) {

        //variables for name of placed order
        $Name = $PIO[$i][0];
        echo $Name;

        //updating Order_Item table
        $db = new SQLite3('/Applications/MAMP/db/IMS.db');
        $sql = 'UPDATE Item_Order 
                SET Order_Quantity=NULL, Placed=0   
                WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name', $Name, SQLITE3_TEXT);
        $stmt->execute();
    }

    //Updating whole order
    updateWhole_Order("NO CURRENT ORDERS", $total, $c);

    header("Refresh:0");
    header("Location:SupplierOrder.php?Order=D"); 
    
}

function updateWhole_Order($d, $t, $c) {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Whole_Order 
            SET Order_Date=:D, Order_Total=:T
            WHERE Category=:C';
       
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':D',  $d, SQLITE3_TEXT);
    $stmt->bindParam(':T',  $t, SQLITE3_INTEGER);
    $stmt->bindParam(':C',  $c, SQLITE3_TEXT);
    $stmt->execute();
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
                <?php if ($_GET['Order'] == "A"): ?>
                    <div class="alert alert-success showcol-10" role="alert" style="font-weight: bold;">
                        Order Accepted
                    </div>
                <?php endif; ?>
                <!-- ALERT: Declined order -->
                <?php if ($_GET['Order'] == "D"): ?>
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

                    <a class="nav-link" href="../../Serverside/OrderPDF.php?Category=<?php echo $category ?>" target="_blank">(PDF)</a>
                </div>
            </div> 
            
            <div class="col"></div>
        </div> 
    </div>
</body>