
<?php
header('content-type: application/x-javascript');

$s = unserialize(urldecode(stripslashes($_GET['s'])));

$query="select A,B,C,D,E,F,G,H,I,J,K where B contains '".$s['name']."' and C contains '".$s['institution']."' and D contains '".$s['country']."' and G contains '".$s['group']."' and H contains '".$s['seminar']."'";
$query2="select+A,B,C,D,E,F,G,H,I,J,K+where+(B+contains+'".$s['name']."'+and+C+contains+'".$s['institution']."'+and+D+contains+'".$s['country']."'+and+G+contains+'".$s['group']."'+and+H+contains+'".$s['seminar']."')";

if(!empty($s['yearInit']))
{
    $query=$query." and E >= '".$s['yearInit']."'";
    $query2=$query2."+and+(E>='".$s['yearInit']."')";
}
if(!empty($s['yearEnd']))
{
    $query=$query." and F <= '".$s['yearEnd']."'";
    $query2=$query2."+and+(F<='".$s['yearEnd']."')";
}
?>

var SS_URL = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dGllR2x3NHEwU1VaV3oyb2pFVl9Vc0E";
var SS_URL_EXEL = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query2; ?>&key=0AjqGPI5Q_Ez6dGllR2x3NHEwU1VaV3oyb2pFVl9Vc0E&exportFormat=xls";

$.ss(SS_URL)
.setQuery("<?php echo $query; ?>")
.setField("A,B,C,D,E,F,G,H,I,J,K")
.send(<?php echo $s['format']; ?>);

function list(success) {
    if(!success) return;
    var con = $('#content')
              var str = "<a href=\"" + SS_URL_EXEL +"\">Descargar en formato Excel</a><br><table class='templateTable'>"
    this.each(function(i, k) {
        str += "<tr><th>Timestamp</th><th>Nombre</th><th>Institución</th><th>País</th><th>Fecha llegada</th><th>Fecha salida</th><th>Grupo</th><th>Seminario</th><th>Resumen</th><th>Fecha seminario</th><th>Proyecto ID</th></tr>\
               <tr><td>" + this['A'] + "</td><td>" + this['B'] + "</td><td>" + this['C'] + "</td><td>" + this['D'] + "</td><td>" + this['E'] + "</td><td>" + this['F'] + "</td><td>" + this['G'] + "</td><td>" + this['H'] + "</td><td>" + this['I'] + "</td><td>" + this['J'] + "</td><td>" + this['K'] + "</td></tr>"
    })
                        str += "</table>"
                               con.html(con.html() + str)
}