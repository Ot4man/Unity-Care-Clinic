<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "unity_care_clinic";

//Connection  of database 
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connections
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
// if($conn) {
//     echo "you are connected";
// }else{
//     echo"Database not connected";
// }