<?php require('include/php/auth.php');require('include/php/database.php');
if (isset($_GET['Mnr']) && $_SESSION['Admin'] == "1") {
    $Mnr = $_GET['Mnr'];
    $MnrDaten = mysql_fetch_array(mysql_query("SELECT * FROM `mitarbeiter` WHERE `Mnr` LIKE '".$Mnr."' LIMIT 1"));
    $Nachname = $MnrDaten['Name'];
    $Vorname = $MnrDaten['Vorname'];
    $Name = $MnrDaten['Titel']." ".$Vorname." ".$Nachname;
}else{
    $Mnr = $_SESSION['Mnr'];
}
 ?>
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
  <link href="include/css/chartist.min.css" rel="stylesheet">
  <link href="include/css/main.css" rel="stylesheet">
  <link href="include/css/arbeitszeit.css" rel="stylesheet">
  <script type="text/javascript" src="include/js/jQuery.js" /></script>
  <script type="text/javascript" src="include/js/bootstrap.min.js" /></script>
  <script type="text/javascript" src="include/js/chartist.min.js" /></script>
  <script type="text/javascript" src="include/js/arbeitszeit.js" /></script>
</head>
<body>
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Zeiterfassung</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="startseite.php">Home</a></li>
            <li><a href="mitarbeiter.php">Mitarbeiter</a></li>
            <li class="active"><a href="arbeitszeit.php">Arbeitszeit</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
  <div id="wrapper">
      <div class="box" id="Zeiterfassung">
          <div class="panel panel-default">
              <?php
              $ArbeitszeitMonat = round(mysql_fetch_array(mysql_query("SELECT SUM(`Netto`) as `Summe` FROM `zeiterfassung` WHERE `Mnr` LIKE '".$Mnr."' and `Datum` >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 1 MONTH)), INTERVAL 1 DAY) ORDER BY `Znr` DESC"))["Summe"],1);
              $ArbeitszeitTotal = round(mysql_fetch_array(mysql_query("SELECT `Zeitkonto` FROM `mitarbeiter` WHERE `Mnr` LIKE '".$Mnr."' LIMIT 1"))["Zeitkonto"],1);
              ?>
            <div class="panel-heading"><?php if ($_SESSION['Mnr'] != $Mnr){echo $Name."'s";}else{echo "Ihre";} ?> Zeitgutschriften diesen Monats<span style="float: right;">Monat: <span id="Zeiterfassung-monat" data-negativ="<?php if($ArbeitszeitMonat < 0){echo "1";}else{echo "0";}?>"><?php echo $ArbeitszeitMonat; ?>h</span> Gesamt: <span id="Zeiterfassung-gesamt" data-negativ="<?php if($ArbeitszeitTotal < 0){echo "1";}else{echo "0";}?>"><?php echo $ArbeitszeitTotal; ?>h</span></span></div>
            <table class="table">
                <tr><th>Tag</th><th>Gearbeitet (in h)</th><th>Gutschrift (in h)</th></tr>
                <?php
                $query = mysql_query("SELECT `Znr`, `Datum`, `Netto`, `Daten`, `Soll` FROM `zeiterfassung` WHERE `Mnr` LIKE '".$Mnr."' and `Datum` >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 1 MONTH)), INTERVAL 1 DAY) ORDER BY `Znr` DESC");
                while ($eintrag = mysql_fetch_array($query)) {
                    if(!isset(json_decode($eintrag["Daten"])[2])){$ausgabeNetto = "Arbeitet";$ausgabeGearbeitet=round(((time() - json_decode($eintrag["Daten"])[0])/60/60),1)." ohne Pausen";}else{$ausgabeNetto = round($eintrag['Netto'], 1);$ausgabeGearbeitet = round(($eintrag['Soll'] + $eintrag['Netto']), 1);}
                    echo "<tr data-Znr='".$eintrag['Znr']."' class='zeiterfassungseintrag'><td>".date_format(date_create($eintrag['Datum']), "d.m.Y")."</td><td>".$ausgabeGearbeitet."</td><td>".$ausgabeNetto."</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <div class="box" id="Zeiterfassungdetail">
        <div class="panel panel-default">
            <div class="panel-heading">Details zum <span id="Zeiterfassungdetail-Datum"></span></div>
            <div class="inhalt">

                <div class="progress" id="Chart-total">
                    <div class="progress-bar progress-bar-success" id="Zeiterfassungdetail-Chart-Arbeit"><!-- Arbeit -->
                        <span></span>
                    </div>
                    <div class="progress-bar progress-bar-success progress-bar-striped" id="Zeiterfassungdetail-Chart-Ueberstunden"><!-- Überstunden -->
                        <span></span>
                    </div>
                    <div class="progress-bar progress-bar-warning" id="Zeiterfassungdetail-Chart-Pause"><!-- Pause -->
                        <span></span>
                    </div>
                    <div class="progress-bar progress-bar-danger" id="Zeiterfassungdetail-Chart-Fehlzeit"><!-- Fehlzeit -->
                        <span></span>
                    </div>
                </div>
                <div id="Zeiterfassungdetail-Tabelle">

                </div>
            </div>
        </div>
    </div>
  </div>
</body>


</html>
