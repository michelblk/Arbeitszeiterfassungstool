$(document).ready(function (){
  $("#wrapper").on('click', '.mitarbeiterZeile',  function (){
    if ($(this).attr('data-Mnr') != $("#MitarbeiterInfo").attr('data-Mnr')){
      $(".mitarbeiterZeile").removeClass("active");
      $(this).addClass("active");
      mitarbeiterInfoPanel($(this).attr('data-Mnr'));
      if (!$("#MitarbeiterInfo").is(":visible")){
          $(".box").css({'opacity': '1'});
          if ($(window).width() > 815) { $(".box").css({'float': 'left'}); $("#MitarbeiterInfo").css({'margin-left': '10px'}); }
          $("#MitarbeiterInfo").stop().fadeIn(300);
      }
      if($(window).width() <= 815) {$("body").animate({ scrollTop: parseInt($("#MitarbeiterTabelle").height() + $("nav").height()) }, 300);}
    }else{
        $(".box").css({'float': ''});
        $("#MitarbeiterInfo").css({'opacity': '0', 'display': 'none', 'margin-left': 'auto'});
        $("#MitarbeiterInfosTabelle tr div").text("");
        $("#MitarbeiterInfo").attr({'data-Mnr': ""});
        $(".mitarbeiterZeile").removeClass("active");
    }
  });

  $("#wrapper").on('click', '#Mitarbeiter-Straße', function () {
     AdresseInMapsOeffnen($("#Mitarbeiter-Straße").text(),$("#Mitarbeiter-Ort").text());
  });
  $("#wrapper").on('click', '#Mitarbeiter-Ort', function () {
     AdresseInMapsOeffnen($("#Mitarbeiter-Straße").text(),$("#Mitarbeiter-Ort").text());
  });
});

function AdresseInMapsOeffnen (Strasse, Ort) {
    window.open("http://maps.google.de/maps?q="+Strasse+","+Ort+",+Germany", "_blank");
}

function mitarbeiterInfoPanel(Mnr) {
  $.ajax({
    url: 'include/daten.php?MitarbeiterInfo&Mnr='+Mnr,
    method: 'GET',
    error: function (e) {
      alert("Fehler!");
    },
    success: function (data) {
      $("#MitarbeiterInfo").attr({'data-Mnr': data['Mnr']});
      $("#Mitarbeiter-Titel").text(data['Titel']);
      $("#Mitarbeiter-Vorname").text(data['Vorname']);
      $("#Mitarbeiter-Name").text(data['Name']);
      $("#Mitarbeiter-Abteilung").html("<span data-toggle=\"popover\" data-trigger=\"hover\" data-content=\""+data['Abteilungsaufgabe']+"\" >"+data['Abteilungsname']+"</span>");
      $("#Mitarbeiter-Email").html("<a href=\"mailto:" + data['Mail'] + "\">" + data['Mail'] + "</a>");
      $("#Mitarbeiter-Straße").text(data['Straße']);
      $("#Mitarbeiter-Ort").text(data['PLZ'] + " " + data['Ort']);
      $("#Mitarbeiter-Telefon").text(data['Telefon']);
      $(".Mitarbeiter-Bild").attr({'data-geschlecht': data['Geschlecht']});
      if (typeof zusaetzlicheInfos == 'function') {
          zusaetzlicheInfos(data);
      }
      $('[data-toggle="popover"]').popover();
    }
  });
}
