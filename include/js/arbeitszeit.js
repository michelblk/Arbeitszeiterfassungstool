$(document).ready(function (){
  $("#wrapper").on('click', '.zeiterfassungseintrag',  function (){
    if ($(this).attr('data-Znr') != $("#Zeiterfassungdetail").attr('data-Znr')){
      $(".zeiterfassungseintrag").removeClass("active");
      $(this).addClass("active");
      zeiterfassungInfoPanel($(this).attr('data-Znr'));
      if (!$("#Zeiterfassungdetail").is(":visible")){
          $(".box").css({'opacity': '1'});
          if ($(window).width() > 815) { $(".box").css({'float': 'left'}); $("#Zeiterfassungdetail").css({'margin-left': '10px'}); }
          $("#Zeiterfassungdetail").stop().fadeIn(300);
      }
      if($(window).width() <= 815) {$("body").animate({ scrollTop: parseInt($("#Zeiterfassung").height() + $("nav").height()) }, 300);}
    }else{
        $(".box").css({'float': ''});
        $("#Zeiterfassungdetail").css({'opacity': '0', 'display': 'none', 'margin-left': 'auto'});
        $("#Zeiterfassungdetail").attr({'data-Znr': ""});
        $(".zeiterfassungseintrag").removeClass("active");
    }
  });
});


function zeiterfassungInfoPanel(Znr) {
  var erweiterung = "";
  if (getParameter("Mnr") != ""){erweiterung = "&Mnr="+getParameter("Mnr");}
  $.ajax({
    url: 'include/daten.php?zeiterfassungDetail&Znr='+Znr+erweiterung,
    method: 'GET',
    error: function (e) {
      alert("Fehler!");
    },
    success: function (data) {
        $("#Zeiterfassungdetail").attr({"data-Znr": Znr});
        $("#Zeiterfassungdetail-Datum").text(data["Wochentag"]+", den "+data["Datum"]);

        /*var Grafikdaten = {labels:[],series:[]}
        $.each(data['Zeiten'], function (index, value) {
            var Uhrzeit = [];
            Uhrzeit.push(new Date(value[0]*1000));
            Uhrzeit.push(new Date(value[1]*1000));
            Uhrzeit[0] = Uhrzeit[0].getHours() +":"+ ("0"+Uhrzeit[0].getMinutes()).substr(-2) +":"+ ("0"+Uhrzeit[0].getSeconds()).substr(-2);
            Uhrzeit[1] = Uhrzeit[1].getHours() +":"+ ("0"+Uhrzeit[1].getMinutes()).substr(-2) +":"+ ("0"+Uhrzeit[1].getSeconds()).substr(-2);
            if (index % 2 || index == 0){
                Grafikdaten['labels'].push("Arbeit: "+Uhrzeit[0]+"-"+Uhrzeit[1]);
            }else{
                Grafikdaten['labels'].push("Pause: "+Uhrzeit[0]+"-"+Uhrzeit[1]);
            }
            Grafikdaten['series'].push((parseInt(value[1]) - parseInt(value[0]))); // /60/60
        }); */
        $("#Chart-total .progress-bar").css({'width': '0px'});
        var zeiten = $.parseJSON(data["Daten"]);
        if (typeof zeiten[2] !== "undefined"){

            // Diagramm
            var arbeitslaenge = (zeiten[2] - zeiten[0]) / 60 / 60; //Zeit des gesamten Arbeitstages (in h)
            if (arbeitslaenge < data["Soll"]){
                sum = data["Soll"];
            }else {
                sum = arbeitslaenge;
            }
            var pausen = 0; //Zeit am Tag (in h)
            $.each(zeiten[1], function (index, value) { //Pausen berechnen
                pausen = pausen + ((value[1] - value[0]) / 60 / 60);
            });
            var gearbeitet = arbeitslaenge - pausen;
            var netto = gearbeitet - data["Soll"];

            if (gearbeitet > data["Soll"]) {gearbeitet = data["Soll"];}
            if(data["Soll"] == "0"){gearbeitet = 0;}
            var gearbeitetP = gearbeitet / sum * 100;
            var pausenP = pausen / sum * 100;
            var nettoP = Math.abs(netto) / sum * 100;
            if((gearbeitetP + pausenP + nettoP) > 100){
                nettoP = 100 - (gearbeitetP + pausenP);
                pausenP = 100 - (gearbeitetP + nettoP);
                gearbeitetP = 100 - (pausenP + nettoP)
            }

            $("#Zeiterfassungdetail-Chart-Arbeit").css({'width': gearbeitetP + "%"});
            $("#Zeiterfassungdetail-Chart-Arbeit span").text(Math.round(gearbeitetP) + "%");
            $("#Zeiterfassungdetail-Chart-Pause").css({'width': pausenP + "%"});
            $("#Zeiterfassungdetail-Chart-Pause span").text(Math.round(pausenP) + "%");
            if (netto < 0) {
                $("#Zeiterfassungdetail-Chart-Fehlzeit").css({'width': nettoP  + "%"});
                $("#Zeiterfassungdetail-Chart-Fehlzeit span").text(Math.round(nettoP) + "%");
            }else if(netto > 0){
                $("#Zeiterfassungdetail-Chart-Ueberstunden").css({'width': nettoP + "%"});
                $("#Zeiterfassungdetail-Chart-Ueberstunden span").text(Math.round(nettoP) + "%");
            }

            // Tabelle
            $("#Zeiterfassungdetail-Tabelle").show();

        }else{ //Wenn noch arbeitet
            $("#Zeiterfassungdetail-Chart-Arbeit").css({'width': "100%"});
            $("#Zeiterfassungdetail-Chart-Arbeit span").text("Arbeitet");
            $("#Zeiterfassungdetail-Tabelle").hide();
        }
    }
  });
}


// Externe Codes
function getParameter(val) { //http://stackoverflow.com/questions/5448545/how-to-retrieve-get-parameters-from-javascript
    var result = "",
        tmp = [];
    location.search
    //.replace ( "?", "" )
    // this is better, there might be a question mark inside
    .substr(1)
        .split("&")
        .forEach(function (item) {
        tmp = item.split("=");
        if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
    });
    return result;
}