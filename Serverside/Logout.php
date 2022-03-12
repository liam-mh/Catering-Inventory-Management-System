<?php
session_start();
session_destroy();
header('Location: ../Clientside/Manager/ManagerLogin.php');
exit;
?>