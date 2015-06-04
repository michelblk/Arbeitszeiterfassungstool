<?php
session_start();

if (!isset($_GET['login']) || $_SERVER['REQUEST_METHOD'] != "POST") {
  http_response_code(404);
  exit;
}

if (isset($_GET['login']) && isset($_POST['inputEmail']) && isset($_POST['inputPassword'])) {
  require('include/php/database.php');
  $email = mysql_real_escape_string($_POST['inputEmail']); //Sicherheit, um externe SQL Befehle zu verhindern
  $password = md5($_POST['inputPassword']); //Verbesserungswürdig

  $query = mysql_query("SELECT * FROM `mitarbeiter` WHERE `Mail` LIKE '".$email."' AND `Passwort` LIKE '".$password."'");
  if (mysql_num_rows($query) == 1) { //Genau ein Mitarbeiter -> Erfolg
    $data = mysql_fetch_array($query);
    $_SESSION['angemeldet'] = True;
    $_SESSION['Email'] = $email;
    $_SESSION['Mnr'] = $data['Mnr'];
    $_SESSION['Admin'] = $data['Admin'];
    $_SESSION['Anrede'] = $data['Titel']." ".$data['Name'];
    if (!isset($_GET['ref'])){
        header('LOCATION: startseite.php');
    }else{
        header('LOCATION: '.base64_decode($_GET['ref']));
    }
    exit;
  }else{
    if (!isset($_GET['ref'])){
      header('LOCATION: index.php?login=failed');
    }else{
      header('LOCATION: index.php?login=failed&ref='.$_GET['ref']);
    }
  }
}

?>
