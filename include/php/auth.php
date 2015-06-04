<?php
session_start();
date_default_timezone_set("Europe/Berlin");
if ($_SESSION['angemeldet'] != True){
    $path = $_SERVER['REQUEST_URI'];
    header('LOCATION: index.php?login=noLogin&ref='.base64_encode($path));
    exit;
}
?>
