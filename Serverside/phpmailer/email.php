<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendEmail($category,$supplier) {
    // Load Composer's autoloader
    require 'vendor/autoload.php';

    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    //Server settings
    $mail->SMTPDebug  = 0;                                        // Enable/disable verbose debug output, change this to 2 if you want to see it doing its thing :)
    $mail->isSMTP();                                              // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                         // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                     // Enable SMTP authentication
    $mail->Username   = 'liam.mh97@gmail.com';                    // SMTP username
    $mail->Password   = 'Chester542664';         		          // SMTP password
    $mail->SMTPSecure = 'tls';                                    // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                      // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    $mail->isHTML(true);                                          // Set email format to HTML

    $mail->setFrom('liam.mh97@gmail.com','The White Horse Inn');  // From address
    $mail->addAddress('liamisham@gmail.com');                     // Add a recipient, In this case your email address 

    // Content
    $mail->Subject = 'New '.$category.' order from The White Horse Inn';
    $mail->Body = 
    'Hello '.$supplier.', <br>
    <br>
    A new '.$category.' order has been placed, please login to view and respond to the order via the link below <br>
    <a href="http://localhost/IMS/Clientside/Supplier/SupplierLogin.php">The White Horse Inn - Supplier Login</a> <br>
    <br>
    Kind regards, <br>
    <b>The White Horse Inn</b>';

    $mail->send();
}

?>
