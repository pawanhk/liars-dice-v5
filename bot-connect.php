<?php
$serverName = "localhost";
$username = "root";
$password = "";
$dbName = "liarsDice";


$con = mysqli_connect($serverName, $username, $password, $dbName);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

?>