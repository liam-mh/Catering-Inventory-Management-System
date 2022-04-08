<?php

require('fpdf.php');
include("Functions.php");

//-------------------------------------------------------------------------------------------------------
//----- Variables and getting data ----------------------------------------------------------------------

$category = $_GET['Category'];

//Getting Supplier details for logged in supplier
$db = new SQLite3('/Applications/MAMP/db/IMS.db');
$sql = 'SELECT * FROM Supplier WHERE Category = :Cat';
$stmt = $db->prepare($sql);
$stmt->bindParam(':Cat', $category, SQLITE3_TEXT); 
$result = $stmt->execute();
$supplier = [];
while($row=$result->fetchArray(SQLITE3_NUM)) {
    $supplier [] = $row;
}

//Getting data from Item_Order table for logged in supplier
$db = new SQLite3('/Applications/MAMP/db/IMS.db');
$sql = 'SELECT Item_Name, Order_Quantity, Total FROM Item_Order WHERE Category = :Cat AND Order_Placed = 1';
$stmt = $db->prepare($sql);
$stmt->bindParam(':Cat', $category, SQLITE3_TEXT); 
$result = $stmt->execute();
$stock = [];
while($row=$result->fetchArray(SQLITE3_NUM)) {
    $stock [] = $row;
}

//Getting total from Whole_Order table for current PDF total
$db = new SQLite3('/Applications/MAMP/db/IMS.db');
$sql = 'SELECT Order_Total FROM Whole_Order WHERE Category = :Cat';
$stmt = $db->prepare($sql);
$stmt->bindParam(':Cat', $category, SQLITE3_TEXT); 
$result = $stmt->execute();
$total = [];
while($row=$result->fetchArray(SQLITE3_NUM)) {
    $total [] = $row;
}
$currentTotal = number_format((($total[0][0])/100),2);

//Getting data from PDF
$db = new SQLite3('/Applications/MAMP/db/IMS.db');
$sql = 'SELECT * FROM PDF WHERE Category = :Cat';
$stmt = $db->prepare($sql);
$stmt->bindParam(':Cat', $category, SQLITE3_TEXT); 
$result = $stmt->execute();
$getPDF = [];
while($row=$result->fetchArray(SQLITE3_NUM)) {
    $getPDF [] = $row;
}
$date = $getPDF[0][0];
$AD = strtoupper($getPDF[0][4]);

//-------------------------------------------------------------------------------------------------------
//----- PDF FUNCTIONS -----------------------------------------------------------------------------------

//Functions for PDF
class PDF extends FPDF {

    //Creates the table
    function BasicTable($header, $data) {

        $this->SetFont('Arial','B',12);//Header
        foreach($header as $col){
            $this->Cell(40,10,$col);
        }
        $this->Ln();

        $this->SetFont('Arial','',12);//Body
        foreach($data as $row){
            foreach($row as $col)
                $this->Cell(40,5,$col);
            $this->Ln();
        }
    }
}

//-------------------------------------------------------------------------------------------------------
//----- Creating PDF ------------------------------------------------------------------------------------

//Cell(width,height,text,border,end line,align)

$pdf = new PDF(); //create an object of PDF
$pdf->AddPage('P','A4'); //create page

//WH title and Order form
$pdf->SetFont('Arial','B',12);
$pdf->Ln(5);
$pdf->Cell(150,5,'THE WHITE HORSE INN',0,0);
$pdf->Cell(40,5,'ORDER FORM',0,1,'R');
//WH details
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'123 Long Street, Sheffield, S1 ABC',0,1);
$pdf->Cell(0,5,'email@whitehorseinn.co.uk',0,1);
$pdf->Cell(0,5,'0114 123 1234',0,1);

$pdf->Ln(5); //space

//Supplier title 
$pdf->SetFont('Arial','B',12);
$pdf->Ln(5);
$pdf->Cell(0,5,strtoupper($supplier[0][1]),0,1);
//Supplier details
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'44 Small Drive, Sheffield, S3 ABC',0,1);
$pdf->Cell(0,5,$supplier[0][2],0,1);
$pdf->Cell(0,5,'0114 321 4321',0,1);

$pdf->Ln(10); //space

//Requested stock
$pdf->Cell(190,0,'',1,1);//line 
$pdf->Ln(1); //space
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'REQUESTED STOCK',0,1);
$pdf->Ln(1); //space
$pdf->Cell(190,0,'',1,1);//line 

//table
$pdf->SetFont('Arial','',12);
$header = array("ITEM","QUANTITY","TOTAL");
$pdf->BasicTable($header,$stock);
$pdf->Ln(5); //space
$pdf->Cell(190,0,'',1,1);//line 

$pdf->Ln(10); //space

//total
$pdf->SetFont('Arial','B',12);
$pdf->Cell(35,5,'ORDER TOTAL: ',0,0);
$pdf->Cell(0,5,$currentTotal,0,1);

//Accept/Decline
$pdf->SetFont('Arial','',12);
$pdf->Cell(25,5,$AD,0,0);
$pdf->Cell(0,5,$date,0,1);

$pdf->Output();


?>