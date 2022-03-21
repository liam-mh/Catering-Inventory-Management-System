<?php

function verifyManager () {

    if (!isset($_POST['Username']) or !isset($_POST['Password'])) {return;}   

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $stmt = $db->prepare('SELECT Username, Password FROM Login WHERE Username=:Username AND Password=:Password');
    $stmt->bindParam(':Username', $_POST['Username'], SQLITE3_TEXT);
    $stmt->bindParam(':Password', $_POST['Password'], SQLITE3_TEXT);

    $result = $stmt->execute();
    $rows_array = [];
    while ($row = $result->fetchArray()) {$rows_array[]=$row;}
    return $rows_array;
}   
function verifySupplier() {

    if (!isset($_POST['Username']) or !isset($_POST['Password']) or !isset($_POST['SupplierID']) ) {return;}   

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $stmt = $db->prepare('SELECT Username, Password, Supplier_ID FROM Login WHERE Username=:Username');
    $stmt->bindParam(':Username', $_POST['Username'], SQLITE3_TEXT);
    $stmt->bindParam(':Password', $_POST['Password'], SQLITE3_TEXT);
    $stmt->bindParam(':SupplierID', $_POST['SupplierID'], SQLITE3_TEXT);

    $result = $stmt->execute();
    $rows_array = [];
    while ($row = $result->fetchArray()) {$rows_array[]=$row;}
    return $rows_array;
}   

?>