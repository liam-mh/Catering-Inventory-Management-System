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
$Name = $_SESSION['Username'];

$supplier = getSupplier();

if (isset($_POST['AddDO'])) {

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


if (isset($_POST['PlaceDO'])) {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Item_Order 
            SET Order_Placed = 1  
            WHERE Category = "Dairy"';
    $stmt = $db->prepare($sql); 
    $stmt->execute();
}

?>

<body>
    <?php require("SupplierNavbar.php");?>
    <div class="container">
  
        <div class="row">

            <div class="col"></div>

            <div class="col-md-5">
                <div class="w1-box">
                                
                    <p>ORDER DATE: </p>
                    <table class="styled-table" style="display:block; height:200px; overflow:auto;">
                        <thead>
                            <th>Item</th> 
                            <th>Order Quantity</th>  
                            <th>Total</th> 
                        </thead>
                        <tbody>
                            <?php 
                            $DIO = PlacedDairyIO();
                            for ($i=0; $i<count($DIO); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $DIO[$i]['Item_Name']?></td>                                                           
                                <td><?php echo $DIO[$i]['Order_Quantity']?></td> 
                                <td>£<?php echo number_format((($DIO[$i]['Total'])/100),2)?></td> 
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <br>
                    <p>ORDER TOTAL: £<?php $Dsum=PlacedDairyTP(); echo number_format((($Dsum)/100),2); ?></p>

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
                </div>
            </div> 
            
            <div class="col"></div>
        </div> 
    </div>
</body>