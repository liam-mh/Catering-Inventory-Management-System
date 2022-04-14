<?php

//-------------------------------------------------------------------------------------------------------
//----- This page contains most functions used throughout the system ------------------------------------
//----- Functions are split by generalised catagory, not by specific pages ------------------------------
//-------------------------------------------------------------------------------------------------------


//-------------------------------------------------------------------------------------------------------
//----- ADD NEW / EDIT STOCK ----------------------------------------------------------------------------

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
    $sql = 'SELECT * FROM Item_Order WHERE Category = :Cat AND Placed IS NOT 1';
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
    $sql = 'SELECT * FROM Item_Order WHERE Category = :Cat AND Order_Quantity IS NOT NULL AND PLACED IS NOT 1';
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
    $sql = 'SELECT * FROM Item_Order WHERE Category = :Cat AND Order_Quantity IS NOT NULL AND Placed = 1';
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
    
    $g = getAddedItemOrder($c);
    $sum = 0;

    for ($i=0; $i<count($g); $i++) {
        $sum = $sum + $g[$i][3];
    }
    return $sum;
}

//places order from Item_Order to Whole_Order and updates Placed to 1
include("OrderPDF.php");
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
    $d = $date->format('d/m/y');

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Whole_Order 
            SET Order_Date=:D, Order_Total=:Total  
            WHERE Category = :Cat';    
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':D',     $d, SQLITE3_TEXT);
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
//----- SUPPLIER ORDERS ---------------------------------------------------------------------------------

//Updates PDF, Whole_Order, Stock and Item_Order when supplier either accepts or declines a placed order
function OrderAD($category, $total, $PIO, $AD) {

    //current date 
    $date  = new DateTime(); 
    $d = $date->format('d/m/y');
    
    //Inserting result into PDF table
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO PDF (PDF_Date, Category, Order_Total, Accept_Decline) 
            VALUES (:D, :Cat, :Total, :AD)';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':D',     $d, SQLITE3_TEXT);
    $stmt->bindParam(':Cat',   $category, SQLITE3_TEXT);
    $stmt->bindParam(':Total', $total, SQLITE3_INTEGER);
    $stmt->bindParam(':AD',    $AD, SQLITE3_TEXT);
    $stmt->execute();

    supplierPDF($category);

    //Updating tables for each item
    for ($i=0; $i<count($PIO); $i++) {

        //variables for name and quantity of placed order
        $Name = $PIO[$i][0];
        $AcceptQuantity = $PIO[$i][1];

        if ($AD == "Declined") {

            //updating Order_Item table
            $db = new SQLite3('/Applications/MAMP/db/IMS.db');
            $sql = 'UPDATE Item_Order 
                    SET Order_Quantity=NULL, Placed=0   
                    WHERE Item_Name=:Name';
            $stmt = $db->prepare($sql); 
            $stmt->bindParam(':Name', $Name, SQLITE3_TEXT);
            $stmt->execute();

        } elseif ($AD == "Accepted") {

            //Getting remaining stock from stock table
            $db = new SQLite3('/Applications/MAMP/db/IMS.db');
            $sql = 'SELECT Quantity FROM Stock WHERE Item_Name=:Name';
            $stmt = $db->prepare($sql); 
            $stmt->bindParam(':Name', $Name, SQLITE3_TEXT);
            $result = $stmt->execute();
            $oldQ = [];
            while($row=$result->fetchArray(SQLITE3_NUM)){$oldQ [] = $row;}
            $OQ = $oldQ[0][0];

            //Adding new quantity onto remaining stock
            $total = $OQ + $AcceptQuantity;

            //Inserting new qauntity into Stock table
            $sql = 'UPDATE Stock 
                    SET Quantity=:Quantity 
                    WHERE Item_Name=:Name';
            $stmt = $db->prepare($sql); 
            $stmt->bindParam(':Name',     $Name, SQLITE3_TEXT);
            $stmt->bindParam(':Quantity', $total, SQLITE3_INTEGER);
            $stmt->execute();

            //Removing items from Item_Order table as back above threshold
            $sql = 'DELETE FROM Item_Order WHERE Item_Name=:Name';
            $stmt = $db->prepare($sql); 
            $stmt->bindParam(':Name', $Name, SQLITE3_TEXT);
            $stmt->execute();   
        }
    }

    //Updating whole order
    updateWhole_Order("NO CURRENT ORDERS", $total, $category);

    //Setting header
    //header("Refresh:0");
    //header("Location:SupplierOrder.php?Order=$AD");

    
}

//Updates Whole_Order table
function updateWhole_Order($d, $t, $c) {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Whole_Order 
            SET Order_Date=:D, Order_Total=:T
            WHERE Category=:C';
       
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':D',  $d, SQLITE3_TEXT);
    $stmt->bindParam(':T',  $t, SQLITE3_INTEGER);
    $stmt->bindParam(':C',  $c, SQLITE3_TEXT);
    $stmt->execute();
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING PDFS ------------------------------------------------------------------------------------

function getPDF($c) {
    
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'SELECT * FROM PDF WHERE Category = :Cat ORDER BY PDF_ID DESC';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Cat', $c, SQLITE3_TEXT); 
    $result = $stmt->execute();
    $PDF = [];
    while($row=$result->fetchArray(SQLITE3_NUM)) {
        $PDF [] = $row;
    }
    return $PDF;
}

//-------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------

?>