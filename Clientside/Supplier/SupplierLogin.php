<?php

include("../../Serverside/Sessions.php");

$UsernameError = $PasswordError = $IDError = $invalid = "";

if (isset($_POST['submit'])) {

    if ($_POST["Username"]==null) {$UsernameError = "Username is required";}
    if ($_POST["Password"]==null) {$PasswordError = "Password is required";}
    if ($_POST["SupplierID"]==null) {$PasswordError = "Supplier ID is required";}
    if ($_POST["Username"]!=null && $_POST["Password"]!=null && $_POST["SupplierID"]!=null) {
 
        $Supplier = verifySupplier();

        if ($Supplier != null) {
            session_start();
            $_SESSION['Username'] = $Supplier[0]['Username'];
            $_SESSION['Password'] = $Supplier[0]['Password'];
            header("Location: SupplierOrder.php");
            exit(); 
        } else {
            $invalid = "Invalid login details";
        }
    }
}


?>

<body>
    <?php include("../../Serverside/LoginNavbar.php");?>

    <br>

    <div class="container">
    <div class="row">   

        <div class="col-md-3"></div>
        <div class="col-md-6">

            <!-- Tabs -->
            <div class="row">
                <div class="col">  
                    <form method="post">
                        <input type="submit" value="WHITE HORSE LOGIN" class="w1-tab-unselected" name="whLogin">
                    </form>  
                </div>
                <?php 
                    if (isset($_POST['whLogin'])) {header('Location:../Manager/ManagerLogin.php');}
                ?>
                <div class="col" style="text-align:center">  
                    <div class="w1-tab">
                        <h>SUPPLIER LOGIN</h>
                    </div> 
                </div>
            </div>   

            <!-- Logins -->
            <div>
                <div class="w1-box" style="text-align:center">

                    <form method="post">
                        <div class="w1-box">
                            <p>SUPPLIER ID</p>
                            <div class="form-group">
                                <input type="text" name="SupplierID">
                                <span class="text-danger"><?php echo $IDError; ?></span>
                            </div>
                        </div>   
                        <br>
                        <div class="w1-box">
                            <p>USERNAME</p>
                            <div class="form-group">
                                <input type="text" name="Username">
                                <span class="text-danger"><?php echo $UsernameError; ?></span>
                            </div>
                        </div>   
                        <br>
                        <div class="w1-box">
                            <p>PASSWORD</p>
                            <div class="form-group">
                                <input type="text" name="Password">
                                <span class="text-danger"><?php echo $PasswordError; ?></span>
                            </div>    
                        </div>  
                        <br>

                        <!-- Login button and invalid login error -->
                        <p class="text-danger"><?php echo $invalid; ?></p>
                        <div class="form-group">
                            <input class="btn btn-main" style="width:50%" type="submit" value="LOGIN" name ="submit"></input> 
                        </div> 
                    </form>   
                
                </div>
            </div>  

        </div> 
    </div>
    </div>
</body>