<?php 
session_start();
ob_start();
session_unset();
header("Location: chirperchat-index.php");
die();

?>