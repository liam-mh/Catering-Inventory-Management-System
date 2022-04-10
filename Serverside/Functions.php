<?php

//Adds new item to Stock table
function addNew() {

    //Unit price calculation
    $pounds = $_POST['UnitPounds'];
    $pence = $_POST['UnitPence'];
    $UnitPrice = ($pounds*100) + $pence;

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO Stock (Item_Name, Category, Unit_Price, Threshold) 
            VALUES (:ItemName, :Category, :UnitPrice, :Threshold)';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':ItemName',  $_POST['ItemName'], SQLITE3_TEXT);
    $stmt->bindParam(':Category',  $_POST['Category'], SQLITE3_TEXT);
    $stmt->bindParam(':UnitPrice', $UnitPrice, SQLITE3_INTEGER);
    $stmt->bindParam(':Threshold', $_POST['Threshold'], SQLITE3_INTEGER);
    $stmt->execute();
}

//Updates details of selected item in Stock table
function updateSelected() {

    //Unit price calculation
    $pounds = $_POST['UpdateUnitPounds'];
    $pence = $_POST['UpdateUnitPence'];
    $UpdateUnitPrice = ($pounds*100) + $pence;

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 
       'UPDATE Stock 
        SET Item_Name=:ItemName, Category=:Category, Unit_Price=:UnitPrice, Threshold=:Threshold, Quantity=:Quantity 
        WHERE Item_Name=:Name';

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Name',      $_POST['SelectedUpdateItemName'], SQLITE3_TEXT);
    $stmt->bindParam(':ItemName',  $_POST['UpdateItemName'], SQLITE3_TEXT);
    $stmt->bindParam(':Category',  $_POST['UpdateCategory'], SQLITE3_TEXT);
    $stmt->bindParam(':UnitPrice', $UpdateUnitPrice, SQLITE3_INTEGER);
    $stmt->bindParam(':Threshold', $_POST['UpdateThreshold'], SQLITE3_INTEGER);
    $stmt->bindParam(':Quantity',  $_POST['UpdateQuantity'], SQLITE3_INTEGER);
    $stmt->execute();

    header('Refresh:0');
}

//Removes selected item from Stock table
function deleteSelected() {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'DELETE FROM Stock WHERE Item_Name=:Name';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Name', $selected, SQLITE3_TEXT);
    $stmt->execute();
    header("Location:Index.php?deleted=true");
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING FROM STOCK TABLE ------------------------------------------------------------------------

//All stock
function getCurrentStock () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Stock');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}

//Getting selected stock items details
function getSelectedStock($selected) {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = "SELECT * FROM Stock WHERE Item_name = :Itemname";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Itemname', $selected, SQLITE3_TEXT); 
    $result = $stmt->execute();
    $SelectedItem = [];
    while($row=$result->fetchArray(SQLITE3_NUM)){$SelectedItem [] = $row;}
    return $SelectedItem;
}

//-------------------------------------------------------------------------------------------------------
//----- SUPPLIERS ---------------------------------------------------------------------------------------

//Getting all supplier detials
function getSupplier () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Supplier');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}

//Updates suppliers name and email
function updateSupplier($c,$n,$e) {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 
       'UPDATE Supplier 
        SET Name=:Name, Email=:Email
        WHERE Category=:Cat';

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Name',  $n, SQLITE3_TEXT);
    $stmt->bindParam(':Email', $e, SQLITE3_TEXT);
    $stmt->bindParam(':Cat',  $c, SQLITE3_TEXT);
    $stmt->execute();

    header('Refresh:0');
}

//Getting logged in supplier category
function getLoggedInSupplierCat($SupplierName) {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = "SELECT * FROM Supplier WHERE Name = :Name";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Name', $SupplierName, SQLITE3_TEXT); 
    $result = $stmt->execute();
    $supplier = [];
    while($row=$result->fetchArray(SQLITE3_NUM)) {
        $supplier [] = $row;
    }
    return $supplier;
}

//-------------------------------------------------------------------------------------------------------
//----- ORDERS & ORDER ITEMS ----------------------------------------------------------------------------

//Getting low quantity Item_Order in supplier category
function getItemOrder ($c) {
    
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'SELECT * FROM Item_Order WHERE Category = :Cat AND Placed IS NULL';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Cat', $c, SQLITE3_TEXT); 
    $result = $stmt->execute();
    $ItemOrder = [];
    while($row=$result->fetchArray(SQLITE3_NUM)) {
        $ItemOrder [] = $row;
    }
    return $ItemOrder;
}

//Getting low quantity Item_Order in supplier category
function getAddedItemOrder ($c) {
    
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'SELECT * FROM Item_Order WHERE Category = :Cat AND Order_Quantity IS NOT NULL AND PLACED IS NULL';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Cat', $c, SQLITE3_TEXT); 
    $result = $stmt->execute();
    $ItemOrder = [];
    while($row=$result->fetchArray(SQLITE3_NUM)) {
        $ItemOrder [] = $row;
    }
    return $ItemOrder;
}

//Getting Placed low quantity Item_Order in supplier category
function getPlacedItemOrder ($c) {
    
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'SELECT * FROM Item_Order WHERE Category = :Cat AND Order_Quantity IS NOT NULL';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Cat', $c, SQLITE3_TEXT); 
    $result = $stmt->execute();
    $ItemOrder = [];
    while($row=$result->fetchArray(SQLITE3_NUM)) {
        $ItemOrder [] = $row;
    }
    return $ItemOrder;
}

//Total of placed items order
function TotalPIO ($c) {
    
    $g = getPlacedItemOrder($c);
    $sum = 0;

    for ($i=0; $i<count($g); $i++) {
        $sum = $sum + $g[$i][3];
    }
    return $sum;
}
//Total of items order
function TotalIO ($c) {
    
    $g = getItemOrder($c);
    $sum = 0;

    for ($i=0; $i<count($g); $i++) {
        $sum = $sum + $g[$i][3];
    }
    return $sum;
}

//places order from Item_Order to Whole_Order and updates Placed to 1
function placeOrder ($c,$t) {

    //updating Order_Item table
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Item_Order 
            SET Placed = 1  
            WHERE Category = :Cat';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Cat', $c, SQLITE3_TEXT);
    $stmt->execute();

    //Adding details to Order table
    //current date 
    $date  = new DateTime(); 
    $formatDate = $date->format('d/m/y');

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Whole_Order 
            SET Order_Date="today", Order_Total=:Total  
            WHERE Category = :Cat';    
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':D',     $date, SQLITE3_TEXT);
    $stmt->bindParam(':Total', $t, SQLITE3_INTEGER);
    $stmt->bindParam(':Cat',   $c, SQLITE3_TEXT);
    $stmt->execute();
}

//Getting Order date from Whole_Order for logged in supplier
function getOrderDate ($category) {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = "SELECT * FROM Whole_Order WHERE Category = :Cat";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Cat', $category[0][0], SQLITE3_TEXT); 
    $result = $stmt->execute();
    $OD = [];
    while($row=$result->fetchArray(SQLITE3_NUM)) {
        $OD [] = $row;
    }
    return $OD;
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING PDFS ------------------------------------------------------------------------------------

function getPDF ($c) {
    
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'SELECT * FROM PDF WHERE Category = :Cat ORDER BY Date DESC';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Cat', $c, SQLITE3_TEXT); 
    $result = $stmt->execute();
    $PDF = [];
    while($row=$result->fetchArray(SQLITE3_NUM)) {
        $PDF [] = $row;
    }
    return $PDF;
}


?>