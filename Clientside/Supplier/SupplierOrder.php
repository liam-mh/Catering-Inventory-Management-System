<?php 

//error_reporting(0);

include("../../Serverside/Sessions.php");
include("../../Serverside/Functions.php");

$path = "SupplierLogin.php";
session_start(); 
if (!isset($_SESSION['Username'])) {
    session_unset();
    session_destroy();
    header("Location:".$path);
}
$SupplierName = $_SESSION['Username'];

//-------------------------------------------------------------------------------------------------------
//----- Getting values from db --------------------------------------------------------------------------

//Getting logged in supplier category
$db = new SQLite3('/Applications/MAMP/db/IMS.db');
$sql = "SELECT * FROM Supplier WHERE Name = :Name";
$stmt = $db->prepare($sql);
$stmt->bindParam(':Name', $SupplierName, SQLITE3_TEXT); 
$result = $stmt->execute();
$supplier = [];
while($row=$result->fetchArray(SQLITE3_NUM)){$supplier [] = $row;}
$category = $supplier[0][0];

//Getting Order date from Whole_Order for logged in supplier
$db = new SQLite3('/Applications/MAMP/db/IMS.db');
$sql = "SELECT * FROM Whole_Order WHERE Category = :Cat";
$stmt = $db->prepare($sql);
$stmt->bindParam(':Cat', $category, SQLITE3_TEXT); 
$result = $stmt->execute();
$OD = [];
while($row=$result->fetchArray(SQLITE3_NUM)){$OD [] = $row;}
$orderDate = $OD[0][0];

//order total
$Dsum=TotalPIO($category);
//placed order dairy items
$DIO = getPlacedItemOrder($category);

//-------------------------------------------------------------------------------------------------------
//----- ACCEPT / DECLINE ORDERS -------------------------------------------------------------------------

//accept
if (isset($_POST['accept'])) {

    //Inseting into PDF table
    //current date 
    $date  = new DateTime(); 
    $formatDate = $date->format('d/m/y');
    
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO PDF (Date, Category, Order_Total, Accept_Decline) 
            VALUES (:Date, :Cat, :Total, "Accepted")';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Date',  $formatDate, SQLITE3_TEXT);
    $stmt->bindParam(':Cat',   $category, SQLITE3_TEXT);
    $stmt->bindParam(':Total', $Dsum, SQLITE3_INTEGER);
    $stmt->execute();

    //Adding stock to Stock table
    for ($i=0; $i<count($DIO); $i++) {

        //variables for name and quantity of placed order
        $Name = $DIO[$i][0];
        $AcceptQuantity = $DIO[$i][1];

        //Getting remaining stock from stock table
        $db = new SQLite3('/Applications/MAMP/db/IMS.db');
        $sql = 'SELECT Quantity FROM Stock WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name',     $Name, SQLITE3_TEXT);
        $stmt->bindParam(':Quantity', $_POST['UpdateQuantity'], SQLITE3_INTEGER);
        $result = $stmt->execute();
        $oldQ = [];
        while($row=$result->fetchArray(SQLITE3_NUM)){$oldQ [] = $row;}
        $OQ = $oldQ[0][0];

        //Adding new quantity onto remaining stock
        $NewTotal = $OQ + $AcceptQuantity;

        //Inserting new qauntity into Stock table
        $sql = 'UPDATE Stock 
                SET Quantity=:Quantity 
                WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name',     $Name, SQLITE3_TEXT);
        $stmt->bindParam(':Quantity', $NewTotal, SQLITE3_INTEGER);
        $stmt->execute();

        //Removing items from Item_Order table as back above threshold
        $sql = 'DELETE FROM Item_Order WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name', $Name, SQLITE3_TEXT);
        $stmt->execute();

        //Removing date from Whole_Order
        $sql = 'UPDATE Whole_Order 
                SET Order_Date="NO CURRENT ORDERS" 
                WHERE Category=:Cat';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Cat', $category, SQLITE3_TEXT);
        $stmt->execute();
        
        header('Refresh:0');
    }

}

?>

<body>
    <?php require("SupplierNavbar.php");?>
    <div class="container">
  
        <div class="row">

            <div class="col"></div>

            <div class="col-md-5">
                <div class="w1-box">
                    <p style="text-align:center"><?php echo $SupplierName ?> Current Order</p>
                    <br>           
                    <p>ORDER DATE: <?php echo $orderDate ?></p>
                    <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                        <thead>
                            <th>Item</th> 
                            <th>Order Quantity</th>  
                            <th>Total</th> 
                        </thead>
                        <tbody>
                            <?php 
                            for ($i=0; $i<count($DIO); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $DIO[$i][0]?></td>                                                           
                                <td><?php echo $DIO[$i][1]?></td> 
                                <td>£<?php echo number_format((($DIO[$i][3])/100),2)?></td> 
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br>
                    <p>ORDER TOTAL: £<?php echo number_format((($Dsum)/100),2); ?></p>

                    <!-- Accept and Decline buttons -->
                    <form method="post">
                        <div class="row">
                            <div class="col" style="text-align:center"> 
                                <form method="post">  
                                    <input type="submit" value="ACCEPT" class="btn btn-main" name="accept">
                                </form>
                            </div>
                            
                            <div class="col" style="text-align:center"> 
                                <form method="post">                                        
                                    <input type="submit" value="DECLINE" class="btn btn-danger" name="decline">
                                </form>
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