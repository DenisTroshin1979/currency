<?php
session_start();

if (isset($_SESSION['idCurrentUser']))
    $loggedin=true;
else 
    $loggedin=false;

// показывать сообщения об ошибках
ini_set("display_errors", 1);
error_reporting(E_ALL);
?>