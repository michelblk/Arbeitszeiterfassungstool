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
  <link href="include/css/main.css" rel="stylesheet">
  <link href="include/css/mitarbeiter.css" rel="stylesheet">
  <script type="text/javascript" src="include/js/jQuery.js" /></script>
  <script type="text/javascript" src="include/js/bootstrap.min.js" /></script>
  <script type="text/javascript" src="include/js/mitarbeiter.js" /></script>
  <?php if ($_SESSION['Admin'] == "1") echo "<script type='text/javascript' src='include/js/mitarbeiter-admin.js.php' /></script>"; ?>

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
            <li class="active"><a href="mitarbeiter.php">Mitarbeiter</a></li>
            <li><a href="arbeitszeit.php">Arbeitszeit</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <div id="wrapper">
        <div class="box" style=""  id="MitarbeiterTabelle">
            <div class="panel panel-default">
                <div class="panel-heading">Mitarbeiterliste</div>
                <table class="table">
                    <tr>
                        <th>Name</th><th>Abteilung</th><?php if ($_SESSION['Admin'] == "1") echo "<th>Zeitkonto</th>"; ?>
                    </tr>
              <?php
              require('include/php/database.php');
                $query = mysql_query("SELECT mitarbeiter.Mnr, mitarbeiter.Name, mitarbeiter.Vorname, mitarbeiter.Titel, mitarbeiter.Zeitkonto, abteilungen.Name as 'Abteilungsname' FROM `mitarbeiter` INNER JOIN `abteilungen` ON abteilungen.Anr=mitarbeiter.Anr ORDER BY abteilungen.Anr, mitarbeiter.Name");
                while ($row = mysql_fetch_array($query)) {
                  echo "<tr class='mitarbeiterZeile' data-Mnr='".$row['Mnr']."'>
                    <td>".$row['Titel']." ".$row['Vorname']." ".$row['Name']."</td>
                    <td>".$row['Abteilungsname']."</td>";
                    if ($_SESSION['Admin'] == "1") echo "<td><span class='AdminZeitkontoUebersicht' data-negativ='"; if($row['Zeitkonto'] < 0){echo "1";}else{echo "0";} echo "'>".$row['Zeitkonto']."</span></td>";
                  echo "</tr>";
                }
              ?>
            </table>
          </div>
          <?php
          if ($_SESSION['Admin'] == "1") { ?>
          <button type="button" class="btn btn-default btn-sm" id="addNewUser" data-toggle="modal" data-target="#newUserPopup">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Neuen Benutzer anlegen
          </button>
          <button type="button" class="btn btn-default btn-sm" id="AttributeBearbeiten" data-toggle="modal" data-target="#AttributeBearbeitenPopup">
            <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Attribute bearbeiten
          </button>
          <?php }
          ?>
        </div>
        <div class="panel panel-default box" id="MitarbeiterInfo">
          <div class="panel-heading">Mitarbeiterinformationen</div>
          <div class="MitarbeiterInfos">
            <div class="Mitarbeiter-Bild" data-geschlecht=""></div>
            <table class="table" id="MitarbeiterInfosTabelle">
              <tr><th>Anrede: </th><td><div id="Mitarbeiter-Titel"></div></td></tr>
              <tr><th>Nachname: </th><td><div id="Mitarbeiter-Name"></div></td></tr>
              <tr><th>Vorname: </th><td><div id="Mitarbeiter-Vorname"></div></td></tr>
              <tr><th>Abteilung: </th><td><div id="Mitarbeiter-Abteilung"></div></td></tr>
              <tr><th>Straße: </th><td><div id="Mitarbeiter-Straße"></div></td></tr>
              <tr><th>Ort: </th><td><div id="Mitarbeiter-Ort"></div></td></tr>
              <tr><th>E-Mail: </th><td><div id="Mitarbeiter-Email"></div></td></tr>
              <tr><th>Telefon: </th><td><div id="Mitarbeiter-Telefon"></div></td></tr>
              <?php if($_SESSION['Admin'] == "1"){ ?>
              <tr class="MitarbeiterInfoAdmin"><th>Mobil: </th><td><div id="Mitarbeiter-Mobil"></div></td></tr>
              <tr class="MitarbeiterInfoAdmin"><th>Qualifikationen: </th><td><div id="Mitarbeiter-Qualifikationen"></div></td></tr>
              <tr class="MitarbeiterInfoAdmin"><th>Arbeitszeit: </th><td><div id="Mitarbeiter-Arbeitszeit"></div></td></tr>
              <tr class="MitarbeiterInfoAdmin"><th>Arbeitstage: </th><td><div id="Mitarbeiter-Arbeitstage"></div></td></tr>
              <tr class="MitarbeiterInfoAdmin"><th>Urlaubstage: </th><td><div id="Mitarbeiter-Urlaubstage"></div></td></tr>
              <tr class="MitarbeiterInfoAdmin"><th>Zeitkonto: </th><td><div id="Mitarbeiter-Zeitkonto"></div></td></tr>
              <tr class="MitarbeiterInfoAdmin"><th>Gehalt: </th><td><div id="Mitarbeiter-Gehalt"></div></td></tr>
              <tr class="MitarbeiterInfoAdmin"><th>Admin: </th><td><div id="Mitarbeiter-Admin"></div></td></tr>
              <tr><td colspan="2" onclick="$('.MitarbeiterInfoAdmin').toggle();" id="toggleSensibleDaten">Sensible Informationen anzeigen/ausblenden</td></tr>
              <?php } ?>
            </table>
          </div>
    </div>
    <div style="clear: both;"></div> <!-- Float ausgleichen -->
  </div>
  <?php
  if($_SESSION['Admin'] == "1"){
  ?>
  <div class="modal fade newUser" id="newUserPopup" role="dialog">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Neuen Benutzer anlegen</h4>
              </div>
              <div class="modal-body">
                  <div class="alert alert-success" role="alert">
                      <strong>Erfolg!</strong> Benutzer hinzugefügt
                  </div>
                  <div class="alert alert-danger" role="alert">
                      <strong>Fehler!</strong> Etwas ist schiefgelaufen.
                  </div>
                  <form width="100%" id="addNewUserForm" method="POST" action="javascript:neuerBenutzer();">
                      <div class="panel panel-default" style="border: 0px;">
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Anrede
                              </span>
                              <input type="text" class="form-control" placeholder="Herr/Frau" name="Titel" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Name
                              </span>
                              <input type="text" class="form-control" placeholder="Mustermann" name="Name" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Vorname
                              </span>
                              <input type="text" class="form-control" placeholder="Max" name="Vorname" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Straße
                              </span>
                              <input type="text" class="form-control" placeholder="Musterstraße" name="Straße" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Postleitzahl
                              </span>
                              <input type="number" class="form-control" placeholder="12345" name="PLZ" min="10000" max="99999" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Ort
                              </span>
                              <input type="text" class="form-control" placeholder="Musterstadt" name="Ort" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Telefon
                              </span>
                              <input type="tel" class="form-control" placeholder="047258369" name="Telefon" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Mobil
                              </span>
                              <input type="tel" class="form-control" placeholder="015789456" name="Mobil" />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  E-Mail
                              </span>
                              <input type="email" class="form-control" placeholder="Max@Mustermann.de" name="Mail" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Abteilung
                              </span>
                              <select class="form-control" name="Abteilung">
                                  <?php require('include/php/database.php');
                                  $query = mysql_query("SELECT * FROM `abteilungen` ORDER BY `Anr`");
                                  while($abteilung = mysql_fetch_array($query)){
                                      echo "<option value='".$abteilung['Anr']."' title='".$abteilung['Aufgabe']."'>".$abteilung['Name']."</option>";
                                  }
                                  ?>
                              </select>
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Qualifikationen
                              </span>
                              <select multiple class="form-control" name="Qualifikationen[]">
                                  <?php require('include/php/database.php');
                                  $query = mysql_query("SELECT * FROM `qualifikationen` ORDER BY `Name`");
                                  while($qualifikation = mysql_fetch_array($query)){
                                      echo "<option value='".$qualifikation['Qnr']."' title='".$qualifikation['Beschreibung']."'>".$qualifikation['Name']."</option>";
                                  }
                                  ?>
                              </select>
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Gehaltsstufe
                              </span>
                              <select class="form-control" name="Gehaltsstufe" required>
                                  <?php require('include/php/database.php');
                                  $query = mysql_query("SELECT * FROM `gehalt` ORDER BY `Gehalt`");
                                  while($gehalt = mysql_fetch_array($query)){
                                      echo "<option value='".$gehalt['Gnr']."' title='".$gehalt['Beschreibung']."'>".$gehalt['Gnr']." (".$gehalt['Gehalt']."€)</option>";
                                  }
                                  ?>
                              </select>
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Arbeitszeit (h pro Tag)
                              </span>
                              <input type="number" class="form-control" placeholder="8" name="Arbeitszeit" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Arbeitstage (Wochentage)
                              </span>
                              <div class="form-control">
                                  <label class="checkbox-inline"><input type="checkbox" value="1" name="Arbeitstage[]">Mo</label>
                                  <label class="checkbox-inline"><input type="checkbox" value="2" name="Arbeitstage[]">Di</label>
                                  <label class="checkbox-inline"><input type="checkbox" value="3" name="Arbeitstage[]">Mi</label>
                                  <label class="checkbox-inline"><input type="checkbox" value="4" name="Arbeitstage[]">Do</label>
                                  <label class="checkbox-inline"><input type="checkbox" value="5" name="Arbeitstage[]">Fr</label>
                                  <label class="checkbox-inline"><input type="checkbox" value="6" name="Arbeitstage[]">Sa</label>
                                  <label class="checkbox-inline"><input type="checkbox" value="7" name="Arbeitstage[]">So</label>
                              </div>
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Urlaubstage
                              </span>
                              <input type="number" class="form-control" placeholder="30" name="Urlaubstage" required />
                          </div>
                          <div class="input-group">
                              <span class="input-group-addon">
                                  Passwort
                              </span>
                              <input type="password" class="form-control" placeholder="Passwort" name="Passwort" required />
                          </div>
                          <button type="submit" class="btn btn-default" style="">Registrieren</button>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
              </div>
          </div>
      </div>
  </div>
  <div class="modal fade newUser" id="AttributeBearbeitenPopup" role="dialog">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Attribute bearbeiten</h4>
              </div>
              <div class="modal-body">
                  Noch nicht verfügbar!
              </div>
          </div>
      </div>
  </div>
  <?php } ?>
</body>


</html>
