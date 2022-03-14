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
//----- GETTING FROM SUPLLIERS --------------------------------------------------------------------------

function getSupplier () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Supplier');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
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

//-------------------------------------------------------------------------------------------------------
//----- ORDERS ------------------------------------------------------------------------------------------

function getDairyOrder () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Item_Order WHERE Category = "Dairy"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}
function getMeatOrder () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Item_Order WHERE Category = "Meat / Fish"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}
function getVegOrder () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Item_Order WHERE Category = "Fruit / Veg"');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}




?>