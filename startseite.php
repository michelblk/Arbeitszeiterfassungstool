<?php require('include/php/auth.php'); ?>
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
  <script type="text/javascript" src="include/js/jQuery.js" /></script>
  <script type="text/javascript" src="include/js/startseite.js" /></script>
  <style>
    #wrapper {
      margin-top: 50px;
      padding-top: 30px;
    }
    .button {
      width: 200px;
      margin: 5px;
      text-align: left;
    }
    .text {
      padding-left: 5px;
    }
    .container:before {
      height: 50px;
      width: 50px;
      background-size: contain;
      background-image: url('include/images/logo.png');
      float: left;
      margin-right: 10px;
    }
    .MitarbeiterStatusAlert {
        display: none;
        width: 420px;
        margin-left: auto;
        margin-right: auto;
    }
  </style>
</head>
<body>
  <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Zeiterfassung</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="startseite.php">Home</a></li>
            <li><a href="mitarbeiter.php">Mitarbeiter</a></li>
            <li><a href="arbeitszeit.php">Arbeitszeit</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
  <div id="wrapper">
    <div id="arbeitszeit-buttons">
      <table style="margin-left: auto; margin-right: auto;">
        <tr tablespan="2">
            <div class="alert alert-success MitarbeiterStatusAlert" role="alert"><strong>Erfolg!</strong> Neuer Status verbucht</div>
            <div class="alert alert-danger MitarbeiterStatusAlert" role="alert"><strong>Fehler!</strong> Etwas ist schiefgelaufen. Eventuell ist diese Aktion nicht verfügbar.</div>
        </tr>
        <tr>
          <td>
            <button type="button" class="btn btn-default btn-lg button" id="begin-button">
              <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span><span id="begin-buttontext" class="text"> Arbeitsbeginn</span>
            </button>
          </td>
          <td>
            <button type="button" class="btn btn-default btn-lg button" id="pause-button">
              <span class="glyphicon glyphicon-cutlery" aria-hidden="true"></span><span id="pause-buttontext" class="text"> Pausenbeginn</span>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <button type="button" class="btn btn-default btn-lg button" id="end-button">
              <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span><span id="begin-end-buttontext" class="text"> Arbeitsende</span>
            </button>
          </td>
          <td>
            <button type="button" class="btn btn-default btn-lg button" id="pauseend-button">
              <span class="glyphicon glyphicon-print" aria-hidden="true"></span><span id="pause-buttontext" class="text"> Pausenende</span>
            </button>
          </td>
        </tr>
      </table>
    </div>
  </div>
</body>


</html>
