<?php
// variables to hold database login info
$dbhost = 'fdb1034.awardspace.net';
$dbuser = '4707790_palpostthomaspenny';
$dbpass = 'annabellapenny94';
$dbname = '4707790_palpostthomaspenny';

//Attempt to connect to database
if (!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) {
    die("Failed to connect!");
}
?>