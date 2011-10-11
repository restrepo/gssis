
<?php
header('content-type: application/x-javascript');

$s = unserialize(urldecode(stripslashes($_GET['s'])));

$query="select A,B,C,D,E,F,G,H,I,J where A contains '".$s['name']."' and B contains '".$s['institution']."' and C contains '".$s['country']."' and F contains '".$s['group']."' and G contains '".$s['seminar']."'";
$query2="select+A,B,C,D,E,F,G,H,I,J+where+(A+contains+'".$s['name']."'+and+B+contains+'".$s['institution']."'+and+C+contains+'".$s['country']."'+and+F+contains+'".$s['group']."'+and+G+contains+'".$s['seminar']."')";

if(!empty($s['yearInit']))
{
    $query=$query." and C >= ".$s['yearInit'];
    $query2=$query2."+and+(C>=".$s['yearInit'].")";
}
if(!empty($s['yearEnd']))
{
    $query=$query." and C <= ".$s['yearEnd'];
    $query2=$query2."+and+(C<=".$s['yearEnd'].")";
}
?>

var SS_URL = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dGllR2x3NHEwU1VaV3oyb2pFVl9Vc0E";
var SS_URL_EXEL = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query2; ?>&key=0AjqGPI5Q_Ez6dGllR2x3NHEwU1VaV3oyb2pFVl9Vc0E&exportFormat=xls";

$.ss(SS_URL)
.setQuery("<?php echo $query; ?>")
.setField("A,B,C,D,E,F,G,H,I,J")
.send(function(success) {
    if(!success) return;
    var con = $('#content')
    var str = "<a href=\"" + SS_URL_EXEL +"\">Descargar en formato Excel</a><br><table class='templateTable'>"
    this.each(function(i, k) {
        str += "<tr><th>Nombre</th><th>Institución</th><th>País</th><th>Fecha llegada</th><th>Fecha salida</th><th>Grupo</th><th>Seminario</th><th>Resumen</th><th>Fecha seminario</th><th>Proyecto ID</th></tr>\
               <tr><td>" + this['A'] + "</td><td>" + this['B'] + "</td><td>" + this['C'] + "</td><td>" + this['D'] + "</td><td>" + this['E'] + "</td><td>" + this['F'] + "</td><td>" + this['G'] + "</td><td>" + this['H'] + "</td><td>" + this['I'] + "</td><td>" + this['J'] + "</td></tr>"
    })
    str += "</table>"
    con.html(con.html() + str)
});