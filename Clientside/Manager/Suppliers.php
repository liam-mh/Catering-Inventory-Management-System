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

$supplier = getSupplier();

//update suppliers with apply
if (isset($_POST['DUpdate'])) {updateSupplierDairy();}
if (isset($_POST['MUpdate'])) {updateSupplierMeat();}
if (isset($_POST['VUpdate'])) {updateSupplierVeg();}

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
                            <br><br>
                            <div class="w1-box">
                                <p>NAME</p>
                                <input type="text" name="UpdateDName" value="<?php echo $supplier[0][1]; ?>">
                            </div>
                            <br>
                            <div class="w1-box">
                                <p>EMAIL</p>
                                <input type="text" name="UpdateDEmail" value="<?php echo $supplier[0][2]; ?>">
                            </div>
                            <br>
                            <div class="form-group">
                                <input class="btn btn-main" style="width:50%" type="submit" value="APPLY" name="DUpdate"></input> 
                            </div> 
                        </form> 

                        <br><br>

                        <p>RECENT ORDERS</p>
                        <table class="styled-table" style="display:block; height:100px; overflow:auto;">
                            <thead>
                                <th>Date</th>
                                <th>Accept/Decline</th>
                                <th>Link</th>    
                            </thead>
                            <tbody>
                                <?php 
                                $PDF = getDairyPDF();
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

                    <!-- MEAT / FISH -->
                    <div class="col-md-4">
                        <form method="post">                   
                            <h><?php echo strtoupper($supplier[1][0]) ?></h>
                            <br><br>
                            <div class="w1-box">
                                <p>NAME</p>                            
                                <input type="text" name="UpdateMName" value="<?php echo $supplier[1][1]; ?>">
                            </div>
                            <br>
                            <div class="w1-box">
                                <p>EMAIL</p>
                                <input type="text" name="UpdateMEmail" value="<?php echo $supplier[1][2]; ?>">
                            </div>
                            <br>
                            <div class="form-group">
                                <input class="btn btn-main" style="width:50%" type="submit" value="APPLY" name="MUpdate"></input> 
                            </div> 
                        </form> 

                        <br><br>

                        <p>RECENT ORDERS</p>
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
                                    <td><a href="<?php echo $PDF[$i]['PDF_Link']?>" target="_blank" rel="noopener noreferrer">click</a></td>                                                             
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- FRUIT / VEG -->
                    <div class="col-md-4">
                        <form method="post">                   
                            <h><?php echo strtoupper($supplier[2][0]) ?></h>
                            <br><br>
                            <div class="w1-box">
                                <p>NAME</p>
                                <input type="text" name="UpdateVName" value="<?php echo $supplier[2][1]; ?>">
                            </div>
                            <br>
                            <div class="w1-box">
                                <p>EMAIL</p>
                                <input type="text" name="UpdateVEmail" value="<?php echo $supplier[2][2]; ?>">
                            </div>
                            <br>
                            <div class="form-group">
                                <input class="btn btn-main" style="width:50%" type="submit" value="APPLY" name="VUpdate"></input> 
                            </div> 
                        </form> 

                        <br><br>

                        <p>RECENT ORDERS</p>
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
    </div><!-- container -->
    
</body>