<?php
session_start();

if (isset($_SESSION["userID"])) {
    unset($_SESSION["userID"]);
}
// include logout message
header("Location: ../login.php?msg=logout");
die;
?>