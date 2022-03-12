<?php
if (isset($_POST["search"])) 
{
    header("Location: Index.php");
}
?>

<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <form  method="post" action=""> 
            <input  type="text" name="name"> 
            <input  type="submit" name="search" value="Search">
        </form>
    </body>
</html>