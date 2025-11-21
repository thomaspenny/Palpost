<?php
// variables to hold database login info
$dbhost = '<>';
$dbuser = '<>';
$dbpass = '<>';
$dbname = '<>';

//Attempt to connect to database
if (!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) {
    die("Failed to connect!");
}
?>
