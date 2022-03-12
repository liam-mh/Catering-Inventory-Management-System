<?php

include("../../Serverside/Sessions.php");

$UsernameError = $PasswordError = $invalid = "";

if (isset($_POST['submit'])) {

    if ($_POST["Username"]==null) {$UsernameError = "Username is required";}
    if ($_POST["Password"]==null) {$PasswordError = "Password is required";}
    if ($_POST["Username"]!=null && $_POST["Password"]!=null) {
 
        $Manager = verifyManager();

        if ($Manager != null) {
            session_start();
            $_SESSION['Username'] = $Manager[0]['Username'];
            $_SESSION['Password'] = $Manager[0]['Password'];
            header("Location: Index.php");
            exit(); 
        } else {
            $invalid = "Invalid login details";
        }
    }
}


?>

<body>
    <?php require("../../navbar2.php");?>

    <br><br><br>

    <div class="container">
    <div class="row">   

        <div class="col-md-3"></div>

        <div class="col-md-6">
            <!-- Tabs -->
            <div class="row">
                <div class="col" style="text-align:center">  
                    <div class="w1-tab">
                        <h>WHITE HORSE LOGIN</h>
                    </div> 
                </div>
                <div class="col" style="text-align:center">  
                    <div class="w1-tab-unselected">
                        <a class="text-light" href="/IMS/Clientside/Supplier/SupplierLogin.php">SUPPLIER LOGIN</a>
                    </div> 
                </div>
            </div>   

            <!-- Logins -->
            <div>
                <div class="w1-box" style="text-align:center">

                    <form method="post">
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
                            <input class="btn btn-main" type="submit" value="LOGIN" name="submit"></input> 
                        </div> 
                    </form>   
                
                </div>
            </div>  

        </div> 
    </div>
    </div>
</body>