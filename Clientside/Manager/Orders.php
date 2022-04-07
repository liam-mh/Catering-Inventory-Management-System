<?php 

//error_reporting(0);

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


$selected = $_GET['Selected'];

//Getting selected stock items details
$db = new SQLite3('/Applications/MAMP/db/IMS.db');
$sql = "SELECT * FROM Stock WHERE Item_name = :Itemname";
$stmt = $db->prepare($sql);
$stmt->bindParam(':Itemname', $selected, SQLITE3_TEXT); 
$result = $stmt->execute();
$SelectedItem = [];
while($row=$result->fetchArray(SQLITE3_NUM)){$SelectedItem [] = $row;}

$N = $SelectedItem[0][0];
$UnitPrice = $SelectedItem[0][2];

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

//Placing dairy order
$Dsum=dairyTP();
if (isset($_POST['PlaceDO'])) {

    /*
    //updating Order_Item table
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Item_Order 
            SET Order_Placed = 1  
            WHERE Category = "Dairy"';
    $stmt = $db->prepare($sql); 
    $stmt->execute();
    */

    //Adding details to Order table

    //current date 
    $date  = new DateTime(); 
    $formatDate = $date->format('d/m/y');

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Order 
            SET Date = "test"  
            WHERE Category = "Dairy"'; 
           
    $stmt = $db->prepare($sql); 
    //$stmt->bindParam(':Date', $formatDate, SQLITE3_TEXT);
    $stmt->execute();

    echo $formatDate;
    echo $Dsum;
}

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
                            $DIO = dairyIO();
                            for ($i=0; $i<count($DIO); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $DIO[$i]['Item_Name']?></td>                                                           
                                <td><a href="?Selected=<?php echo $DIO[$i]['Item_Name'];?>"><button class="btn-select">select</button></a></td>
                                <td><?php echo $DIO[$i]['Order_Quantity']?></td> 
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
                            <input class="btn btn-main" style="width:50%" type="submit" value="ADD TO ORDER" name="AddDO"></input> 
                        </div> 
                    </form>
                    <br>
                    
                    <div class="black-box">
                        <p>CURRENT ORDER WITH <?php echo $supplier[0][1] ?></p>
                        <?php 
                        $DIO = dairyIO();
                        for ($i=0; $i<count($DIO); $i++):  
                        ?>
                        <p><?php echo $DIO[$i][1], " x ",$DIO[$i][0], " = £", number_format((($DIO[$i][3])/100),2);?></p>
                        <?php endfor; ?>
                        <br>
                        <p>TOTAL PRICE: £<?php echo number_format((($Dsum)/100),2); ?></p>
                        <form method="post">
                            <div class="form-group">        
                                <input class="btn btn-main" style="width:50%" type="submit" value="PLACE ORDER" name="PlaceDO"></input> 
                            </div> 
                        </form>
                    </div>

                </div>




                <!-- MEAT / FISH -->
                <div class="col-md-4">
                    <form method="post">                   
                        <h><?php echo strtoupper($supplier[1][0]) ?></h>
                        <br><br><br>
                        <p><?php echo $supplier[1][1]?></p>
                        <div class="w1-box">
                            <h>NAME</h>
                            <br>
                            <input type="text" name="dairyNameInput" value="<?php $supplier[0][1] ?>">
                        </div>
                        <br>
                        <div class="w1-box">
                            <h>EMAIL</h>
                            <br>
                            <input type="text" name="dairyEmailInput" value="<?php $supplier[0][2] ?>">
                        </div>
                        <br>
                        <div class="form-group">
                            <input class="btn btn-main" style="width:50%" type="submit" value="APPLY" name="dairyApply"></input> 
                        </div> 
                    </form> 
                    <br><br>
                    <h>RECENT ORDERS</h>
                    <br>
                    <table class="styled-table" style="display:block; height:100px; overflow:auto;">
                        <thead>
                            <th>Date</th>
                            <th>Accept/Decline</th>
                            <th>Link</th>    
                        </thead>
                        <tbody>
                            <?php 
                            $PDF = getMeatPDF();
                            for ($i=0; $i<count($PDF); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $PDF[$i]['Date']?></td>                                                           
                                <td><?php echo $PDF[$i]['Accept_Decline']?></td>
                                <td><?php echo $PDF[$i]['Accept_Decline']?></td>                                                             
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

                <!-- FRUIT / VEG -->
                <div class="col-md-4">
                    <form method="post">                   
                        <h><?php echo strtoupper($supplier[2][0]) ?></h>
                        <br><br><br>
                        <p><?php echo $supplier[2][1]?></p>
                        <div class="w1-box">
                            <h>NAME</h>
                            <br>
                            <input type="text" name="dairyNameInput" value="<?php $supplier[0][1] ?>">
                        </div>
                        <br>
                        <div class="w1-box">
                            <h>EMAIL</h>
                            <br>
                            <input type="text" name="dairyEmailInput" value="<?php $supplier[0][2] ?>">
                        </div>
                        <br>
                        <div class="form-group">
                            <input class="btn btn-main" style="width:50%" type="submit" value="APPLY" name="dairyApply"></input> 
                        </div> 
                    </form> 
                    <br><br>
                    <h>RECENT ORDERS</h>
                    <br>
                    <table class="styled-table" style="display:block; height:100px; overflow:auto;">
                        <thead>
                            <th>Date</th>
                            <th>Accept/Decline</th>
                            <th>Link</th>    
                        </thead>
                        <tbody>
                            <?php 
                            $PDF = getVegPDF();
                            for ($i=0; $i<count($PDF); $i++):     
                            ?>
                            <tr> 
                                <td><?php echo $PDF[$i]['Date']?></td>                                                           
                                <td><?php echo $PDF[$i]['Accept_Decline']?></td>
                                <td><a href="<?php echo $PDF[$i]['PDF_Link']?>" target="_blank" rel="noopener noreferrer">click</a></td>                                                                                                                          
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

            </div>   
        </div> 
    </div>
    
</body>