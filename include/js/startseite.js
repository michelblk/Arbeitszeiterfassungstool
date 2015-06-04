$(document).ready(function (){
    $("#begin-button").on("click", function (){
        neuerArbeitsstatus("arbeitsbeginn");
    });
    $("#end-button").on("click", function (){
        neuerArbeitsstatus("arbeitsende");
    });
    $("#pause-button").on("click", function (){
        neuerArbeitsstatus("pausenbeginn");
    });
    $("#pauseend-button").on("click", function (){
        neuerArbeitsstatus("pausenende");
    });

    ArbeitsstatusAktualisieren();
});

function neuerArbeitsstatus (status) {
    $.ajax({
        url: "include/daten.php?arbeitszeit="+status,
        method: "HEAD",
        success: function (){
            verfuegbareOptionen(status);
            $(".MitarbeiterStatusAlert").hide();
            $(".MitarbeiterStatusAlert.alert-success").show();
            setTimeout(function (){$(".MitarbeiterStatusAlert.alert-success").hide();}, 3000);
        },
        error: function (e){
            $(".MitarbeiterStatusAlert").hide();
            $(".MitarbeiterStatusAlert.alert-danger").show();
            setTimeout(function (){$(".MitarbeiterStatusAlert.alert-danger").hide();}, 3000);
        },
    });
}

function verfuegbareOptionen (status) {
    $(".button").removeAttr('disabled');
    if (status == "arbeitsbeginn" || status == "0") {
        $("#begin-button").attr('disabled', true);
        $("#pauseend-button").attr('disabled', true);
    }else
    if (status == "arbeitsende" || status == "1") {
        $("#end-button").attr('disabled', true);
        $("#pause-button").attr('disabled', true);
        $("#pauseend-button").attr('disabled', true);
    }else
    if (status == "pausenbeginn" || status == "2") {
        $("#end-button").attr('disabled', true);
        $("#pause-button").attr('disabled', true);
        $("#begin-button").attr('disabled', true);
    }else
    if (status == "pausenende" || status == "3") {
        $("#pauseend-button").attr('disabled', true);
        $("#begin-button").attr('disabled', true);
    }
}

function ArbeitsstatusAktualisieren () {
    $.ajax({
        url: 'include/daten.php?aktuellerArbeitsstatus',
        method: 'GET',
        success: function (data) {
            verfuegbareOptionen(data['status']);
        }
    });
}