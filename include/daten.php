<?php
require('php/auth.php');
require('php/database.php');


//////////////////////////////////////////////////////////////////// Mitarbeiter
if (isset($_GET['MitarbeiterInfo']) && isset ($_GET['Mnr'])) {
  $Mnr = $_GET['Mnr'];
  if($_SESSION['Admin'] != "1") {
      $query = mysql_query("SELECT mitarbeiter.Mnr, mitarbeiter.Name, mitarbeiter.Vorname, mitarbeiter.Titel, mitarbeiter.Mail, mitarbeiter.Straße, mitarbeiter.PLZ, mitarbeiter.Ort, mitarbeiter.Telefon, abteilungen.Name as 'Abteilungsname', abteilungen.Aufgabe as 'Abteilungsaufgabe' FROM `mitarbeiter` INNER JOIN `abteilungen` ON abteilungen.Anr=mitarbeiter.Anr WHERE `Mnr` LIKE '$Mnr' LIMIT 1");
  }else{
      $query = mysql_query("SELECT `mitarbeiter`.`Mnr`, `mitarbeiter`.`Name`, `mitarbeiter`.`Vorname`, `mitarbeiter`.`Straße`, `mitarbeiter`.`Ort`, `mitarbeiter`.`PLZ`, `mitarbeiter`.`Telefon`, `mitarbeiter`.`Mobil`, `mitarbeiter`.`Mail`, `mitarbeiter`.`Titel`, `mitarbeiter`.`Admin`, `mitarbeiter`.`Anr`, `mitarbeiter`.`Qnr`, `mitarbeiter`.`Gnr`, `mitarbeiter`.`Arbeitszeit`, `mitarbeiter`.`Arbeitstage`, `mitarbeiter`.`Urlaubstage`, `mitarbeiter`.`Zeitkonto`, abteilungen.Name as 'Abteilungsname', abteilungen.Aufgabe as 'Abteilungsaufgabe', gehalt.Gehalt as 'Gehalt', gehalt.Beschreibung as 'Gehaltsstufenbeschreibung', GROUP_CONCAT( `qualifikationen`.`Name`) as 'Qualifikationen' FROM `mitarbeiter` INNER JOIN `abteilungen` ON abteilungen.Anr=mitarbeiter.Anr INNER JOIN `gehalt` ON gehalt.Gnr=mitarbeiter.Gnr INNER JOIN qualifikationen ON FIND_IN_SET( qualifikationen.Qnr, mitarbeiter.Qnr ) !=0 WHERE `Mnr` LIKE '$Mnr' GROUP BY mitarbeiter.Mnr LIMIT 1");
  }
  $daten = mysql_fetch_array($query, MYSQL_ASSOC); //Keine Zahlen als index
  if (strpos($daten['Titel'], "Herr") !== false) {
    $daten['Geschlecht'] = "m";
  } else if(strpos($daten['Titel'], "Frau") !== false){
    $daten['Geschlecht'] = "w";
  }else{
    $daten['Geschlecht'] = "na";
  }
  $JSON = json_encode($daten);
  header('Content-Type: application/json; charset=utf-8');
  header('Content-Length: '.strlen($JSON));
  echo $JSON;
  exit;
}else
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_GET['addNewUser']) && isset($_GET['Admin'])){
  if($_SESSION["Admin"] == "1"){
    $daten["Titel"] = $_POST["Titel"];
    $daten["Name"] = $_POST["Name"];
    $daten["Vorname"] = $_POST["Vorname"];
    $daten["Straße"] = $_POST["Straße"];
    $daten["PLZ"] = $_POST["PLZ"];
    $daten["Ort"] = $_POST["Ort"];
    $daten["Telefon"] = $_POST["Telefon"];
    $daten["Mobil"] = $_POST["Mobil"];
    $daten["Mail"] = $_POST["Mail"];
    $daten["Abteilung"] = $_POST["Abteilung"];
    $daten["Qualifikationen"] = implode(",", $_POST["Qualifikationen"]);
    $daten["Gehaltsstufe"] = $_POST["Gehaltsstufe"];
    $daten["Arbeitszeit"] = $_POST["Arbeitszeit"];
    $daten["Arbeitstage"] = $_POST["Arbeitstage"];
    $daten["Urlaubstage"] = $_POST["Urlaubstage"];
    $daten["Passwort"] = md5($_POST["Passwort"]);

    if((!isset($daten["Mail"]) || $daten["Mail"] == "" || strlen($daten["Mail"]) < 5) || (!isset($daten["Passwort"]) || $daten["Passwort"] == "" || strlen($daten["Passwort"]) <= 3)){
        http_response_code(420);
        exit;
    }

    $arbeitstage = "";
    foreach($daten['Arbeitstage'] as &$value) {
      $arbeitstage = $arbeitstage.$value;
    }
    $daten["Arbeitstage"] = $arbeitstage;

      mysql_query("INSERT INTO `mitarbeiter`(`Name`, `Vorname`, `Straße`, `Ort`, `PLZ`, `Telefon`, `Mobil`, `Mail`, `Titel`, `Anr`, `Qnr`, `Gnr`, `Arbeitszeit`, `Arbeitstage`, `Urlaubstage`, `Zeitkonto`, `Passwort`) VALUES ('".$daten['Name']."', '".$daten['Vorname']."', '".$daten['Straße']."', '".$daten['Ort']."', '".$daten['PLZ']."', '".$daten['Telefon']."','".$daten['Mobil']."', '".$daten['Mail']."', '".$daten['Titel']."', '".$daten['Abteilung']."', '".$daten['Qualifikationen']."', '".$daten['Gehaltsstufe']."', '".$daten['Arbeitszeit']."', '".$daten['Arbeitstage']."', '".$daten['Urlaubstage']."', '0','".$daten['Passwort']."')");
      http_response_code(204);
      exit;
  }else{
      http_response_code(403);
      exit;
  }
}else
//////////////////////////////////////////////////////////////////// Arbeitszeit
if (isset($_GET['arbeitszeit']) && $_SERVER['REQUEST_METHOD'] == "HEAD") {
    $status = getArbeitsstatus();
    $time = time();
    $data = mysql_fetch_array(mysql_query("SELECT zeiterfassung.*, mitarbeiter.Arbeitszeit, mitarbeiter.Arbeitstage, mitarbeiter.Zeitkonto FROM `zeiterfassung` INNER JOIN mitarbeiter ON mitarbeiter.Mnr = zeiterfassung.Mnr WHERE `zeiterfassung`.`Mnr` LIKE '".$_SESSION['Mnr']."' ORDER BY `zeiterfassung`.`Znr` DESC LIMIT 1"));
    if ($_GET['arbeitszeit'] == "arbeitsbeginn" && $status == "1") { // (0) Arbeit beginnen | Nur wenn gerade nicht arbeitet
        $datum = date("Y-m-d", $time);
        if (strpos($data['Arbeitstage'],date("N", $time)) === false){$soll = 0;}else{$soll = $data['Arbeitszeit'];} // Als Überstunden deklarieren, wenn nicht einer der Arbeitstage
        $heuteSoll = mysql_fetch_array(mysql_query("SELECT COALESCE(SUM(`Netto`), 0) as `Summe` FROM `zeiterfassung` WHERE `Mnr` LIKE '".$_SESSION['Mnr']."' and `Datum` LIKE '".$datum."'"))["Summe"];
        if($heuteSoll != 0){
            $soll = $soll - ($soll + $heuteSoll); //Falls der Mitarbeiter mehr als ein mal auf start drückt
            if($soll < 0){$soll = 0;} //kein Negativ Soll
            header("DATA: OEHMMMM");
        }
        $query = mysql_query("INSERT INTO `zeiterfassung`(`Mnr`, `Daten`, `Datum`, `Soll`, `Netto`) VALUES ('".$_SESSION['Mnr']."', '[".$time."]', '".$datum."', '".$soll."', '-".$soll."')");
    }else if ($_GET['arbeitszeit'] == "arbeitsende" && ($status == "0" || $status == "3")) { // (1) Arbeit beenden | Nur wenn gerade arbeitet und nicht in Pause
        $daten = json_decode($data['Daten']);
        if(!isset($daten[1])){$daten[1]=array();}
        $daten[2] = $time;
        $Netto = intval($daten[2]) - intval($daten[0]); //Arbeitsanfang bis Arbeitsende in Sekunden
        foreach($daten[1] as $val) { //Pausen abziehen
            $diff = intval($val[1]) - intval($val[0]);
            $Netto = $Netto - $diff;
        }
        $Netto = $Netto / 60 / 60; // In Stunden umrechnen
        $Netto = $Netto - $data['Soll']; //Von Soll abziehen
        $daten = json_encode($daten);
        $query = mysql_query("UPDATE `zeiterfassung` SET `Daten`='$daten',`Netto`='$Netto' WHERE `Znr` LIKE '".$data['Znr']."' LIMIT 1");
        $Zeitkonto = round($data['Zeitkonto'] + $Netto, 3);
        $query2 = mysql_query("UPDATE `mitarbeiter` SET `Zeitkonto`='$Zeitkonto' WHERE `Mnr` LIKE '".$_SESSION['Mnr']."' LIMIT 1");
    }else if ($_GET['arbeitszeit'] == "pausenbeginn" && ($status == "0" || $status == "3")) { // (2) Pause beginnen | Nur wenn gerade arbeitet und nicht in Pause
        $daten = json_decode($data['Daten']);
        $daten[1][] = array($time);
        $daten = json_encode($daten);
        $query = mysql_query("UPDATE `zeiterfassung` SET `Daten`='$daten' WHERE `Znr` LIKE '".$data['Znr']."' LIMIT 1");
    }else if ($_GET['arbeitszeit'] == "pausenende" && $status == "2") { // (3) Pause beenden | Nur wenn gerade in Pause
        $daten = json_decode($data['Daten']);
        $daten[1][count($daten[1]) - 1][] = $time;
        $daten = json_encode($daten);
        $query = mysql_query("UPDATE `zeiterfassung` SET `Daten`='$daten' WHERE `Znr` LIKE '".$data['Znr']."' LIMIT 1");
    }else{
        http_response_code(409); // Conflict
        exit;
    }
    if(mysql_errno()){ //Fehler beim ausführen
        http_response_code(500);
        exit;
    }else{ //Erfolg
        http_response_code(204);
        exit;
    }
}else
if (isset($_GET['aktuellerArbeitsstatus']) && $_SERVER['REQUEST_METHOD'] == "GET"){
    $status = getArbeitsstatus();
    $ausgabe['status'] = $status;
    $ausgabe = json_encode($ausgabe);
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Length: '.strlen($ausgabe));
    echo $ausgabe;
    exit;
}else
if (isset($_GET['zeiterfassungDetail']) && isset($_GET['Znr']) && $_SERVER['REQUEST_METHOD'] == "GET") {
    if(isset($_GET['Mnr']) && $_SESSION['Admin'] == "1"){$Mnr = $_GET['Mnr'];}else{$Mnr = $_SESSION['Mnr'];}
    $query = mysql_fetch_array(mysql_query("SELECT * FROM `zeiterfassung` WHERE `Znr` LIKE '".$_GET['Znr']."' and `Mnr` LIKE '".$Mnr."' LIMIT 1"));
    $daten = json_decode($query['Daten']);
    /*$ausgabe["Zeiten"][][] = $daten[0];
    for($i = 0; $i < count($daten[1]); $i++) {
        $ausgabe["Zeiten"][count($ausgabe["Zeiten"]) - 1][] = $daten[1][$i][0];
        $ausgabe["Zeiten"][][] = $daten[1][$i][0];
        $ausgabe["Zeiten"][count($ausgabe["Zeiten"]) - 1][] = $daten[1][$i][1];
        $ausgabe["Zeiten"][][] = $daten[1][$i][1];
    }
    $ausgabe["Zeiten"][count($ausgabe["Zeiten"]) - 1][] = $daten[2];
    $ausgabe["Datum"] = date_format(date_create($query['Datum']), "d.m.Y");
    $ausgabe = json_encode($ausgabe); */
    $ausgabe["Datum"] = date_format(date_create($query['Datum']), "d.m.Y");
    setlocale(LC_TIME, "de_DE");
    $ausgabe["Wochentag"] = strftime("%A",date_format(date_create($query['Datum']),"U")); //Damit Wochentag auf Deutsch
    $ausgabe["Daten"] = $query['Daten'];
    $ausgabe["Soll"] = $query["Soll"];
    $ausgabe = json_encode($ausgabe);
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Length: '.strlen($ausgabe));
    echo $ausgabe;
    exit;
}
else {
  http_response_code(404);
  exit;
}


function getArbeitsstatus () {
    $status = mysql_query("SELECT `Daten` FROM `zeiterfassung` WHERE `Mnr` LIKE '".$_SESSION['Mnr']."' ORDER BY `Znr` DESC LIMIT 1");
    if (mysql_num_rows($status) == 1){
        $status = mysql_fetch_array($status)["Daten"];
        $status = json_decode($status);
        if (isset($status[2])) { //Arbeit war beendet
            $status = "1";
        }else
        if (isset($status[1][count($status[1]) - 1][1])){ //Pause beendet
            $status = "3";
        }else
        if (isset($status[1][count($status[1]) - 1][0])){ //Pause begonnen
            $status = "2";
        }else{ // Arbeit begonnen, noch keine Pause gehabt
            $status = "0";
        }
    }else{$status="1";}
    return $status;
}

?>
