
<?php
header('content-type: application/x-javascript');

$p = unserialize(urldecode(stripslashes($_GET['activities'])));

$query="select D,I,J,L,G where E contains '".$p['name']."' ";
$query2="select E,J,F,G,H where E contains '".$p['name']."' ";
$query3="select C,H,D,M,L,F,G where C contains '".$p['name']."' ";
if(!empty($p['yearInit']))
{
    $query=$query." and G >= ".$p['yearInit']." ";
}


if(!empty($p['yearEnd']))
{
    $query=$query." and H <= ".$p['yearEnd']." ";
}

if(!empty($p['yearInit']))
{
    $query2=$query2." and C >= ".$p['yearInit']."";
}


if(!empty($p['yearEnd']))
{
    $query2=$query2." and C <= ".$p['yearEnd']."";
}

// if(!empty($p['yearInit']))
// {
//     $query3=$query3." and F contains ".$p['yearInit']."";
// }
//
//
// if(!empty($p['yearEnd']))
// {
//     $query3=$query3." and G contains ".$p['yearEnd']."";
// }

$output=$p['format'];
?>
var format="<?php echo $p['format']; ?>";

var SS_URL1 = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE";
var SS_URL2 = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E";
var SS_URL3 = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dHlfZTZJN0tuS1c1ZlZNakhpbjc0Umc";

var SS_URL_EXEL1 = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query; ?>&key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE&exportFormat=xls";
var SS_URL_EXEL2 = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query2; ?>&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E&exportFormat=xls";
var SS_URL_EXEL3 = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query3; ?>&key=0AjqGPI5Q_Ez6dHlfZTZJN0tuS1c1ZlZNakhpbjc0Umc&exportFormat=xls";



$.ss(SS_URL1)
.setQuery("<?php echo $query; ?>")
.setField("D,I,J,L,G")
.send(<?php echo 'listProjects'; ?>);

function listProjects(success) {
    if(!success) return;
    var con = $('#content')
              if(format=="list")
    {

        var str = "<h3>Proyectos de Investigación</h3> <a href=\"" + SS_URL_EXEL1 +"\">Descargar en formato Excel</a><br>\
      <table class='templateTable' ><th>Título</th><th>Entidad</th><th>Centro que administra</th><th>Monto</th></tr>"
        this.each(function(i, k) {
            str += "<tr><td>" + this['D'] + "</td><td>" + this['I'] + "</td><td>" + this['J'] + "</td><td>" + this['L'] + "</td></tr>"
        })
                  str += "</table>"
                         con.html(con.html() + str)

    } else {
        var str = "<table class='csvTable' ><th>Título,</th><th>Entidad,</th><th>Centro que administra,</th><th>Monto</th></tr>"
        this.each(function(i, k) {
            str += "<tr><td>" + this['D'] + ",</td><td>" + this['I'] + ",</td><td>" + this['J'] + ",</td><td>" + this['L'] + ",</td></tr>"
        })
                  str += "</table>"
                         con.html(con.html() + str)
    }
}

                 $.ss(SS_URL2)
                 .setQuery("<?php echo $query2; ?>")
                 .setField("E,J,F,G,H")
                 .send(<?php echo 'listArticles'; ?>);

function listArticles(success) {
    if(!success) return;
    var con = $('#content')
              if(format=="list")
    {
        var str = "<h3>Artículos</h3> <a href=\"" + SS_URL_EXEL2 +"\">Descargar en formato Excel</a><br>\
	      <table class='templateTable''><th>Autores</th><th>Título</th><th>Revista</th><th>Volumen y Páginas</th></tr>"
        this.each(function(i, k) {
            str += "<tr><td>" + this['E'] + "</td><td>" + this['J'] + "</td><td>" + this['F'] + "</td><td>" + this['G']+"/"+this['H']+ "</td></tr>"
        })
                  str += "</table>"
                         con.html(con.html() + str)
    } else {
        var str = " <table class='csvTable''><th>Autores,</th><th>Título,</th><th>Revista,</th><th>Volumen y Páginas</th></tr>"
        this.each(function(i, k) {
            str += "<tr><td>" + this['E'] + ",</td><td>" + this['J'] + ",</td><td>" + this['F'] + ",</td><td>" + this['G']+"/"+this['H']+ "</td></tr>"
        })
                  str += "</table>"
                         con.html(con.html() + str)

    }

}


                 $.ss(SS_URL3)
                 .setQuery("<?php echo $query3; ?>")
                 .setField("C,H,D,M,L,F,G")
                 .send(<?php echo 'listEvents'; ?>);

function listEvents(success) {
    if(!success) return;
    var con = $('#content')
              if(format=="list")
    {
        var str = "<h3>Eventos</h3> <a href=\"" + SS_URL_EXEL3 +"\">Descargar en formato Excel</a><br>\
	      <table class='templateTable''><th>Autores</th><th>Título</th><th>Evento, lugar y fecha</th><th>Volumen y Páginas</th></tr>"
        this.each(function(i, k) {
            str += "<tr><td>" + this['C'] + "</td><td>" + this['H'] + "</td><td>" + this['D']+" / "+this['M']+" / "+this['L']+" / "+this['F']+" / "+this['G']+ "</td><td>"+"-"+"</td></tr>"
        })
                  str += "</table>"
                         con.html(con.html() + str)
    } else {
        var str ="<table class='csvTable''><th>Autores,</th><th>Título,</th><th>Evento, lugar y fecha,</th><th>Volumen y Páginas,</th></tr>"
        this.each(function(i, k) {
            str += "<tr><td>" + this['C'] + ",</td><td>" + this['H'] + ",</td><td>" + this['D']+" / "+this['M']+" / "+this['L']+" / "+this['F']+" / "+this['G']+ ",</td><td>"+"-"+"</td></tr>"
        })
                 str += "</table>"

                        con.html(con.html() + str)

    }
}

