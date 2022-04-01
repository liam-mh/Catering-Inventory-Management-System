<?php 

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


//-------------------------------------------------------------------------------------------------------
//----- GETTING FROM SUPLLIERS --------------------------------------------------------------------------

$supplier = getSupplier();

?>

<body>
    <?php require("ManagerNavbar.php");?>
    <div class="container">

    <div style="text-align:center">  
        <div class="w1-box">
            <div class="row">

                <!-- DAIRY -->
                <div class="col-md-4">
                    <form method="post">                   
                        <h><?php echo strtoupper($supplier[0][0]) ?></h>
                        <br><br><br>
                        <p>LOW QUANTITY ITEMS</p>
                        <table class="styled-table" style="display:block; height:250px; overflow:auto;">
                            <thead>
                                <th>ITEM</th>
                                <th>IN STOCK</th>
                                <th>ORDER</th>    
                            </thead>
                            <tbody>
                                <?php 
                                $DIO = dairyIO();
                                for ($i=0; $i<count($DIO); $i++):     
                                ?>
                                <tr> 
                                    <td><?php echo $DIO[$i]['Item_Name']?></td>                                                           
                                    <td><?php echo $DIO[$i]['Accept_Decline']?></td>
                                    <td><a href="<?php echo $PDF[$i]['PDF_Link']?>" target="_blank" rel="noopener noreferrer">click</a></td>                                                             
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                        <br><br>
                        <p>Order From <?php echo $supplier[0][1]?> £<?php ?></p>

                        <div class="form-group">
                            <input class="btn btn-main" style="width:50%" type="submit" value="ORDER" name="dairyOrder"></input> 
                        </div> 
                    </form> 
                    
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