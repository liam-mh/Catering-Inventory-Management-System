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
$orderDate = getOrderDate ($category);             //Getting Order date from Whole_Order for logged in supplier

$total = TotalPIO($category[0][0]);                //order total for logged in category
$IO = getPlacedItemOrder($category[0][0]);         //Getting all times placed in order 

//-------------------------------------------------------------------------------------------------------
//----- ACCEPT / DECLINE ORDERS -------------------------------------------------------------------------

//Current date variable
$date  = new DateTime(); 
$formatDate = $date->format('d/m/y');

//accept
if (isset($_POST['accept'])) {

    AcceptOrder($category, $total, $IO);
}


function AcceptOrder($category, $total, $IO) {

    echo "accepted order";
    //Inserting result into PDF table
    //current date 
    $date  = new DateTime(); 
    $formatDate = $date->format('d/m/y');
    
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO PDF (Date, Category, Order_Total, Accept_Decline) 
            VALUES ("today", :Cat, :Total, "Accepted")';
    $stmt = $db->prepare($sql); 
    //$stmt->bindParam(':D',     $formatDate, SQLITE3_TEXT);
    $stmt->bindParam(':Cat',   $category, SQLITE3_TEXT);
    $stmt->bindParam(':Total', $total, SQLITE3_INTEGER);
    $stmt->execute();

    //Adding stock to Stock table 
    for ($i=0; $i<count($IO); $i++) {

        //variables for name and quantity of placed order
        $Name = $IO[$i][0];
        $AcceptQuantity = $IO[$i][1];

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

        /*
        //Removing date from Whole_Order
        $sql = 'UPDATE Whole_Order 
                SET Order_Date="NO CURRENT ORDERS" 
                WHERE Category=:Cat';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Cat', $category, SQLITE3_TEXT);
        $stmt->execute();
        */
    }     

    header("Refresh:0");
    header("Location:SupplierOrder.php?Order=A");
}



$c = $category[0][0];

/*
//decline
if (isset($_POST['decline'])) {

    DeclineOrder($c, $total, $formatDate);
}

function DeclineOrder($c, $t, $d) {

    //Inserting result into PDF table
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO PDF (Date, Category, Order_Total, Accept_Decline) 
            VALUES (:Date, :Cat, :Total, "Declined")';

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Date',  $d, SQLITE3_TEXT);
    $stmt->bindParam(':Cat',   $c, SQLITE3_TEXT);
    $stmt->bindParam(':Total', $t, SQLITE3_INTEGER);
    $stmt->execute();

    //Updating Whole_Order table
    $sql = 'UPDATE Whole_Order 
            SET Order_Date = "NO CURRENT ORDERS"
            WHERE Category = $Cat';

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Cat', $c, SQLITE3_TEXT);
    $stmt->execute();

    header("Refresh:0");
    header("Location:SupplierOrder.php?Order=D"); 
    
}
*/

if (isset($_POST['decline'])) {

    //Inserting result into PDF table
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO PDF (Date, Category, Order_Total, Accept_Decline) 
            VALUES (:Date, :Cat, :Total, "Declined")';

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Date',  $formatDate, SQLITE3_TEXT);
    $stmt->bindParam(':Cat',   $c, SQLITE3_TEXT);
    $stmt->bindParam(':Total', $total, SQLITE3_INTEGER);
    $stmt->execute();

    //Updating Whole_Order table
    $sql = 'UPDATE Whole_Order 
            SET Order_Date = "NO CURRENT ORDERS"
            WHERE Category = $Cat';

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Cat', $c, SQLITE3_TEXT);
    $stmt->execute();

    header("Refresh:0");
    header("Location:SupplierOrder.php?Order=D"); 
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
                            for ($i=0; $i<count($IO); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $IO[$i][0]?></td>                                                           
                                <td><?php echo $IO[$i][1]?></td> 
                                <td>£<?php echo number_format((($IO[$i][3])/100),2)?></td> 
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