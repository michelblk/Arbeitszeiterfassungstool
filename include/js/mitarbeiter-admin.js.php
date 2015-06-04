<?php require('../php/auth.php'); header('Content-Type: text/javascript; charset=utf-8'); ?>
function neuerBenutzer() {
    $.ajax({
      url: "include/daten.php?addNewUser&Admin",
      method: "POST",
      data: $("#addNewUserForm").serialize(),
      dataType: 'json',
      success: function () {
        $("#addNewUserForm")[0].reset();
        $("#newUserPopup .alert").hide();
        $("#newUserPopup .alert-success").show();
      },
      error: function () {
          $("#newUserPopup .alert").hide();
          $("#newUserPopup .alert-danger").show();
      },
      complete: function () {
          $("#newUserPopup").animate({ scrollTop: 0 }, 300);
      }
    });
}

function zusaetzlicheInfos(data) { //MitarbeiterInformationen nur für den Admin
    var Arbeitstage = "";
    var Wochentag = new Array ("Mo.","Di.","Mi.","Do.","Fr.","Sa.", "So."); //Arbeitstage zu Text konvertieren
    for (var i = 0, len = data['Arbeitstage'].length; i < len; i++) {
        if (data['Arbeitstage'][i] <= 7 && data['Arbeitstage'][i] > 0) {
            Arbeitstage += Wochentag[data['Arbeitstage'][i]-1];
            if (i < data['Arbeitstage'].length-1) Arbeitstage += ", ";
        }
    };
    $("#Mitarbeiter-Mobil").text(data['Mobil']);
    $("#Mitarbeiter-Qualifikationen").text(data['Qualifikationen'].replace(/\,/g,', '));
    $("#Mitarbeiter-Arbeitszeit").text(data['Arbeitszeit'] + "h pro Tag");
    $("#Mitarbeiter-Arbeitstage").text(Arbeitstage);
    $("#Mitarbeiter-Urlaubstage").text(data['Urlaubstage'] + " Tage im Jahr");
    $("#Mitarbeiter-Zeitkonto").text(data['Zeitkonto'] + "h");
    $("#Mitarbeiter-Gehalt").html(data['Gehalt'] + "€ <span data-toggle=\"popover\" data-trigger=\"hover\" data-content=\"" + data['Gehaltsstufenbeschreibung'] + "\">(Stufe "+ data['Gnr'] +")</span>");
    $("#Mitarbeiter-Admin").text(data['Admin']);
}

$(document).ready(function (){
  $("#addNewUserForm").submit(function (e) {
    e.preventDefault();
    neuerBenutzer();
  });

  $("#wrapper").on("click","#Mitarbeiter-Zeitkonto", function () {
      window.open("arbeitszeit.php?Mnr="+$(this).parents(".panel").attr('data-Mnr'),"_blank");
  });
});

// Hier stehen unsichtbare Zeichen ->