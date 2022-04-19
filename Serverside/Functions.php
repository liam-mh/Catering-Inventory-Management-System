<?php

/**
 * This page contains most functions used throughout the system 
 * Functions are split by generalised catagory, not by specific pages 
*/

 
//******************************************************************************************************* 
//***** Error Handling **********************************************************************************

function checkError ($check, $name) {

    //** check if input empty
    if ($check == NULL) {
        throw new Exception($name." must not be empty."); 
    }

    //** check if input is only a number
    //** check if input is a whole number
    if (is_numeric($check)) {
        if (is_float($check)) {
            //blank
        } else {
            throw new Exception($name." must be a whole number.");
        }
    } else {
        throw new Exception($name." must be a number.");
    }
}

//******************************************************************************************************* 
//******************************************************************************************************* 

//-------------------------------------------------------------------------------------------------------
//----- ADD NEW / EDIT STOCK / INSERT STOCK -------------------------------------------------------------

//Adds new item to Stock table
function addNew() {

    //******************** Error Handling ********************
    //** check if input is only a number
    //** check if input is a whole number
    if (is_numeric($_POST['UnitPounds'])) {
        if (is_float($_POST['UnitPounds'])) {
        } else {throw new Exception("Pounds must be a whole number");}
    } else {throw new Exception("Pounds must be a number.");}

    if (is_numeric($_POST['UnitPence'])) {
        if (is_float($_POST['UnitPence'])) {
        } else {throw new Exception("Pence must be a whole number");}
    } else {throw new Exception("Pence must be a number.");}

    if (is_numeric($_POST['Threshold'])) {
        if (is_float($_POST['UnitThreshold'])) {
        } else {throw new Exception("Threshold must be a whole number");}
    } else {throw new Exception("Threshold must be a number.");}
    //******************************************************** 

    //Unit price calculation
    $pounds = $_POST['UnitPounds'];
    $pence = $_POST['UnitPence'];
    $UnitPrice = ($pounds*100) + $pence;
    //changing null fields to 0
    $pounds = ($SelectedItem[0][2] < 100) ? '0' : substr($selectedPrice, 0, -2);

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'INSERT INTO Stock (Item_Name, Category, Unit_Price, Threshold, Quantity) 
            VALUES (:ItemName, :Category, :UnitPrice, :Threshold, 0)';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':ItemName',  $_POST['ItemName'], SQLITE3_TEXT);
    $stmt->bindParam(':Category',  $_POST['Category'], SQLITE3_TEXT);
    $stmt->bindParam(':UnitPrice', $UnitPrice, SQLITE3_INTEGER);
    $stmt->bindParam(':Threshold', $_POST['Threshold'], SQLITE3_INTEGER);
    $stmt->execute();
}

//Updates details of selected item in Stock table
function updateSelected() {

    //******************** Error Handling ********************
    //** check if input is only a number
    //** check if input is a whole number
     if (is_numeric($_POST['UpdateUnitPounds'])) {
        if (is_float($_POST['UpdateUnitPounds'])) {
        } else {throw new Exception("Update pounds must be a whole number");}
    } else {throw new Exception("Update pounds value must be a number.");}

    if (is_numeric($_POST['UpdateUnitPence'])) {
        if (is_float($_POST['UpdateUnitPence'])) {
        } else {throw new Exception("Update pence must be a whole number");}
    } else {throw new Exception("Update pence value must be a number.");}

    if (is_numeric($_POST['UpdateThreshold'])) {
        if (is_float($_POST['UpdateUnitThreshold'])) {
        } else {throw new Exception("Update threshold must be a whole number");}
    } else {throw new Exception("Update threshold value must be a number.");}

    if (is_numeric($_POST['UpdateQuantity'])) {
        if (is_float($_POST['UpdateQuantity'])) {
        } else {throw new Exception("Update quantity must be a whole number");}
    } else {throw new Exception("Update quantity value must be a number.");}
    //******************************************************** 

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

    ////Removing items from Item_Order table if back above threshold
    if ($_POST['UpdateQuantity'] > $_POST['UpdateThreshold']) {

        $sql = 'DELETE FROM Item_Order WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name', $_POST['SelectedUpdateItemName'], SQLITE3_TEXT);
        $stmt->execute();  
    }

    header('Refresh:0');
}

//Removes selected item from Stock table
function deleteSelected() {

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'DELETE FROM Stock WHERE Item_Name=:Name';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':Name', $_GET['Selected'], SQLITE3_TEXT);
    $stmt->execute();

    header("Location:Index.php?deleted=true");
}

//Inserts used stock input into stock table
function insertStock($SelectedItem) {

    //******************** Error Handling ******************** 
    //** check if input is only a number
    if (is_numeric($_POST['UpdateUnitPounds'])) {
    } else {
        throw new Exception("Insert quantity value must be a number");
    }
    //******************************************************** 

    //Subtracting insert quantity
    $CurrentQ = $SelectedItem[0][4];
    $InsertQ = $_POST['InsertQuantity'];
    $NewQ = $CurrentQ-$InsertQ;

    //Selected item data
    $N = $SelectedItem[0][0];
    $Threshold = $SelectedItem[0][3];
    $Cat = $SelectedItem[0][1];

    //Number inserted larger than current stock 
    if ($NewQ < 0) {

        $AlertQ = TRUE; 

    } else {

        //Updating quantity
        $db = new SQLite3('/Applications/MAMP/db/IMS.db');
        $sql = 'UPDATE Stock SET Quantity=:NewQ WHERE Item_Name=:Name';
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':Name', $N, SQLITE3_TEXT);
        $stmt->bindParam(':NewQ', $NewQ, SQLITE3_INTEGER);
        $stmt->execute();  

        //Add item to Item_Order table if below threshold
        if ($NewQ < $Threshold) {

            $AlertT = TRUE;

            $db = new SQLite3('/Applications/MAMP/db/IMS.db');
            $sql = 'INSERT INTO Item_Order (Item_Name, Category) 
                    VALUES (:ItemName, :Category)';
            $stmt = $db->prepare($sql); 
            $stmt->bindParam(':ItemName', $N, SQLITE3_TEXT);
            $stmt->bindParam(':Category', $Cat, SQLITE3_TEXT);
            $stmt->execute();  
        }
    }

    return array($AlertQ, $AlertT);
}

//-------------------------------------------------------------------------------------------------------
//----- GETTING FROM STOCK TABLE ------------------------------------------------------------------------

//All stock by name
function getCurrentStock () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Stock ORDER BY Item_Name ASC');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}
//All stock by category
function getCurrentStockByCat () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Stock ORDER BY Category ASC');
    while ($row=$rows->fetchArray()) {
        $rows_array[]=$row;
    }
    return $rows_array;
}
//All stock thats below below threshold
function getCurrentStockBelow () {
    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $rows = $db->query('SELECT * FROM Stock WHERE Quantity < Threshold ORDER BY Item_Name ASC');
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

//Adds user input quantity to order of below threshold item
function addToOrder($UnitPrice) {
    
    //******************** Error Handling ******************** 
    //** check if input not empty
    if ($_POST['OrderQuantity'] == NULL) {
        throw new Exception("Order quantity must contain a number to be added to the order"); 
    //** check if input is only a number
    } elseif(is_numeric($_POST['OrderQuantity'])) {
    } else {
        throw new Exception("Order quantity must be a number");
    }
    //** check if input is a whole number
    if (is_float($_POST['OrderQuantity'])) {
    } else {
        throw new Exception("Order quantity must be a whole number");
    }
    //******************************************************** 

    //Calculating total price of order
    $total = $UnitPrice * $_POST['OrderQuantity'];

    $db = new SQLite3('/Applications/MAMP/db/IMS.db');
    $sql = 'UPDATE Item_Order 
            SET Order_Quantity=:Quantity, Total=:Total 
            WHERE Item_Name=:ItemName';
    $stmt = $db->prepare($sql); 
    $stmt->bindParam(':ItemName', $N, SQLITE3_TEXT);
    $stmt->bindParam(':Quantity', $_POST['OrderQuantity'], SQLITE3_INTEGER);
    $stmt->bindParam(':Total',    $total, SQLITE3_INTEGER);
    $stmt->execute();
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

    //Adding details to Whole_Order table
    //current date 
    $date  = new DateTime(); 
    $d = $date->format('d-m-y');

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
    $d = $date->format('d-m-y');
    
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
    header("Location:SupplierOrder.php?Order=$AD");

    
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
//----- PDFS --------------------------------------------------------------------------------------------

//gets PDF in most recent order by category
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

//Opens saved PDF orders by selecting their Invoice Number
function readPDF($ID) {

    $filename = "/Applications/MAMP/htdocs/IMS/Serverside/PDF_Store/Invoice_".$ID.".pdf";
    header("Content-type: application/pdf");
    header("Content-Length: " . filesize($filename));
    readfile($filename);
}

//-------------------------------------------------------------------------------------------------------
//----- NAVBAR ------------------------------------------------------------------------------------------

//Provides a nav bar with highlighted page buttons
function nav($page) {

    echo
    '<!doctype html>
    <html>
    <head>
        <title>Inventory Managemement </title>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> 
        <link rel="stylesheet" href="/IMS/site.css">
    </head>
    
    <header>
        <nav class="navbar navbar1 navbar-expand-sm">
    
            <div class="container">
    
                <!-- Left -->
                <ul class="navbar-nav flex-grow-1">
    
                    <li class="nav-item">
                        <a class="nav-link text-light">THE WHITE HORSE INN</a>
                    </li>
    
                </ul>      
    
                <!-- Right -->
                <ul class="navbar-nav nav-space flex-grow-1">
                    
                <li class="nav-item nav-button">
                    <a class="nav-link text-light" href="/IMS/Serverside/Logout.php">LOGOUT</a>
                </li>';

                if ($page == "home") {
                    echo 
                    '<li class="nav-item nav-button-selected">
                        <a class="nav-link text-light" href="/IMS/Clientside/Manager/Index.php">HOME</a>
                    </li>';
                } else {
                    echo 
                    '<li class="nav-item nav-button">
                        <a class="nav-link text-light" href="/IMS/Clientside/Manager/Index.php">HOME</a>
                    </li>';
                }

                if ($page == "supplier") {
                    echo 
                    '<li class="nav-item nav-button-selected">
                        <a class="nav-link text-light" href="/IMS/Clientside/Manager/Suppliers.php">SUPPLIERS</a>
                    </li>';
                } else {
                    echo 
                    '<li class="nav-item nav-button">
                        <a class="nav-link text-light" href="/IMS/Clientside/Manager/Suppliers.php">SUPPLIERS</a>
                    </li>';
                }

                if ($page == "order") {
                    echo 
                    '<li class="nav-item nav-button-selected">
                        <a class="nav-link text-light" href="/IMS/Clientside/Manager/Orders.php">ORDERS</a>
                    </li>';
                } else {
                    echo 
                    '<li class="nav-item nav-button">
                        <a class="nav-link text-light" href="/IMS/Clientside/Manager/Orders.php">ORDERS</a>
                    </li>';
                }
    
                echo 
               '</ul>
    
            </div>
        </nav>
    
        <br><br>
    </header>';
}

//-------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------

?>