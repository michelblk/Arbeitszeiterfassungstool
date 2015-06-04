<?php session_start(); if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == True){header('LOCATION: startseite.php');exit;}?>
<!doctype html>
<html>
<head>
  <title> Zeiterfassung </title>
  <meta charset="utf-8">
  <meta name="author" content="Michel Blank">
  <meta name="keywords" content="Michel Blank">
  <meta name="description" content="System zur Zeiterfassung">
  <meta name="language" content="DE">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#474747">
  <link href="include/css/bootstrap.css" rel="stylesheet">
  <link href="include/css/login.css" type="text/css" rel="stylesheet" />
</head>
<body>
  <div id="login">
    <?php
      if (isset($_GET['login']) && $_GET['login'] == "failed") { ?>
        <div class="alert alert-danger" role="alert">
          <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
          <span class="sr-only">Fehler:</span> Login fehlgeschlagen!
        </div>
      <?php
      }
    ?>
    <form class="form-signin" action="login.php?login<?php if(isset($_GET['ref'])) echo "&ref=".$_GET['ref']; ?>" method="post">
        <h2 class="form-signin-heading">Bitte anmelden</h2>
        <label for="inputEmail" class="sr-only">E-Mail Adresse</label>
        <input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="E-Mail Adresse" required autofocus />
        <label for="inputPassword" class="sr-only">Passwort</label>
        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Passwort" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Anmelden</button>
      </form>
  </div>

</body>


</html>
