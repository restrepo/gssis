
<?php
header('content-type: application/x-javascript');

$s = unserialize(urldecode(stripslashes($_GET['s'])));

$query="select A,B,C,D,E,F,G,H,I,J,K,R,S,T,U where C contains '".$s['name']."' and D contains '".$s['id']."' and G contains '".$s['advisor']."' and H contains '".$s['group']."' and I contains '".$s['projectid']."'";
$query2="select+A,B,C,D,E,F,G,H,I,J,K,R,S,T,U+where+(C+contains+'".$s['name']."'+and+D+contains+'".$s['id']."'+and+G+contains+'".$s['advisor']."'+and+H+contains+'".$s['group']."'+and+I+contains+'".$s['projectid']."')";

// if(!empty($s['yearInit']))
// {
//     $query=$query." and C >= ".$s['yearInit'];
// }
// if(!empty($s['yearEnd']))
// {
//     $query=$query." and C <= ".$s['yearEnd'];
// }

?>

var SS_URL = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dHJwV0tzaHRzOXlHcHVZSmwySnRTU1E";
var SS_URL_EXEL = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query2; ?>&key=0AjqGPI5Q_Ez6dHJwV0tzaHRzOXlHcHVZSmwySnRTU1E&exportFormat=xls";

$.ss(SS_URL)
.setQuery("<?php echo $query; ?>")
.setField("A,B,C,D,E,F,G,H,I,J,K,R,S,T,U")
.send(<?php echo $s['format']; ?>);

function codi(success) {
    if(!success) return;
    var con = $('#content')
    var str = "<a href=\"" + SS_URL_EXEL +"\">Descargar en formato Excel</a><br><table class='templateTable'>"
    this.each(function(i, k) {
        str += "<tr><td colspan='4'><b>Estudiante matriculado o admitido " + (i+1) + "</b></td></tr>\
                <tr><td>Nombre:</td><td>" + this['C'] + "</td><td>Cédula:</td><td>" + this['D'] + "</td></tr>\
                <tr><td>Tutor:</td><td>" + this['G'] + "</td><td>Programa:</td><td>" + this['I'] + "</td></tr>\
                <tr><td colspan='2'>Trabajo de investigación o proyecto:</td><td colspan='2'>" + this['J'] +"</td></tr>"
    })
    str += "</table>"
    con.html(con.html() + str)
}

function list(success) {
    if(!success) return;
    var con = $('#content')
    var str = "<a href=\"" + SS_URL_EXEL +"\">Descargar en formato Excel</a><br>\
               <table class='templateTable'><tr><th>Timestamp</th><th>Username</th><th>Nombre y Apellidos</th><th>Identificación</th><th>Semestre Ingreso</th><th>Semestre de Sálida</th><th>Director</th><th>Grupo</th><th>Programa</th><th>Trabajo de Investigación</th><th>Proyecto ID</th><th>Programa</th><th>Trabajo de Investigación</th><th>Timestamp</th><th>Username</th></tr>"
    this.each(function(i, k) {
        str += "<tr><td>" + this['A'] + "</td><td>" + this['B'] + "</td><td>" + this['C'] + "</td><td>" + this['D'] + "</td><td>" + this['E'] + "</td><td>" + this['F'] + "</td><td>" + this['G'] + "</td><td>" + this['H'] + "</td><td>" + this['I'] + "</td><td>" + this['J'] + "</td><td>" + this['K'] + "</td><td>" + this['R'] + "</td><td>" + this['S'] + "</td><td>" + this['T'] + "</td><td>" + this['U'] + "</td></tr>"
    })
    str += "</table>"
    con.html(con.html() + str)
}