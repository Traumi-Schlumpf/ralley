<?php
include('module.php');
$conn = dbconnect();
echo getfromquestion($conn, 1, 1, "Punkte");
?>