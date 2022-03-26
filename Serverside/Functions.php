<?php

function addNew() {

    //Unit price calculation
    $pounds = $_POST['UnitPounds'];
    $pence = $_POST['UnitPence'];
    $UnitPrice = ($pounds*100) + $pence;

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO Stock (Item_Name, Category, Unit_Price, Threshold) VALUES (:ItemName, :Category, :UnitPrice, :Threshold)';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':ItemName',  $_POST['ItemName'], SQLITE3_TEXT);
    $stmt->bindParam(':Category',  $_POST['Category'], SQLITE3_TEXT);
    $stmt->bindParam(':UnitPrice', $UnitPrice, SQLITE3_INTEGER);
    $stmt->bindParam(':Threshold', $_POST['Threshold'], SQLITE3_INTEGER);
    $stmt->execute();

}

function InsertUsed() {
    
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO Stock (Quantity) VALUES (:Quantity,) WHERE Item_Name = :ItemName';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':ItemName',  $_POST['ItemName'], SQLITE3_TEXT);
    $stmt->bindParam(':Quantity',  $_POST['Quantity'], SQLITE3_TEXT);
    $stmt->execute();

}

function update() {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 
        'UPDATE Stock 
        SET Item_Name=:ItemName, Category=:Category, Unit_Price=:UnitPrice, Threshold=:Threshold, Quantity=:Quantity 
        WHERE Item_Name=:Name' ;

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Name',      $_GET['Selected'], SQLITE3_TEXT);
    $stmt->bindParam(':ItemName',  $_POST['UpdateItemName'], SQLITE3_TEXT);
    $stmt->bindParam(':Category',  $_POST['UpdateCategory'], SQLITE3_TEXT);
    $stmt->bindParam(':UnitPrice', $_POST['UpdateUnitPrice'], SQLITE3_INTEGER);
    $stmt->bindParam(':Threshold', $_POST['UpdateThreshold'], SQLITE3_INTEGER);
    $stmt->bindParam(':Quantity',  $_POST['UpdateQuantity'], SQLITE3_INTEGER);
    $stmt->execute();
    header('Location:Index.php?updated=true"');
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

//Item names for insert used stock
function getCurrentDairyStock () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Stock WHERE Category = "Dairy"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}
function getCurrentMeatStock () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Stock WHERE Category = "Meat / Fish"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}
function getCurrentVegStock () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Stock WHERE Category = "Fruit / Veg"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING/UPDATING SUPLLIERS ----------------------------------------------------------------------

function getSupplier () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Supplier');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}

function updateSupplierDairy() {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 
        'UPDATE Supplier 
        SET Name=:DName, Email=:DEmail 
        WHERE Category="Dairy"' ;

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':DName',   $_POST['UpdateDName'], SQLITE3_TEXT);
    $stmt->bindParam(':DEmail',  $_POST['UpdateDEmail'], SQLITE3_TEXT);
    
    $stmt->execute();
    header('Location:Suppliers.php?DairyUpdated=true"');
}
function updateSupplierMeat() {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 
        'UPDATE Supplier 
        SET Name=:MName, Email=:MEmail 
        WHERE Category="Meat / Fish"' ;

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':MName',   $_POST['UpdateMName'], SQLITE3_TEXT);
    $stmt->bindParam(':MEmail',  $_POST['UpdateMEmail'], SQLITE3_TEXT);
    
    $stmt->execute();
    header('Location:Suppliers.php?MeatUpdated=true"');
}
function updateSupplierVeg() {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 
        'UPDATE Supplier 
        SET Name=:VName, Email=:VEmail 
        WHERE Category="Fruit / Veg"' ;

    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':VName',   $_POST['UpdateVName'], SQLITE3_TEXT);
    $stmt->bindParam(':VEmail',  $_POST['UpdateVEmail'], SQLITE3_TEXT);
    
    $stmt->execute();
    header('Location:Suppliers.php?VegUpdated=true"');
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING PDFS ------------------------------------------------------------------------------------
function getDairyPDF () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM PDF WHERE Category = "Dairy"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}
function getMeatPDF () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM PDF WHERE Category = "Meat / Fish"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}
function getVegPDF () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM PDF WHERE Category = "Fruit / Veg"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}




?>