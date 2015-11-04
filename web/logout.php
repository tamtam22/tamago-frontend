<?php
/*---------------------------------------sendLogOutRequest()-----------------------------------------*/
session_start();
session_destroy();
$_SESSION = array();
header("Location: login.php");
/*-----------------------------------End of send log out request-------------------------------------*/
?>