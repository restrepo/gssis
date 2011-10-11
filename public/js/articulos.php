
<?php
header('content-type: application/x-javascript');

$s = unserialize(urldecode(stripslashes($_GET['s'])));

$query="select J,E,F,I,C where E contains '".$s['autor']."' and F contains '".$s['journal']."' and J contains '".$s['article']."'";
$query2="select+J,E,F,I,C+where+(E+contains+'".$s['autor']."'+and+F+contains+'".$s['journal']."'+and+J+contains+'".$s['article']."')";

if(!empty($s['yearInit']))
{
    $query=$query." and C >= ".$s['yearInit'];
}
if(!empty($s['yearEnd']))
{
    $query=$query." and C <= ".$s['yearEnd'];
}
?>

var SS_URL = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E";
var SS_URL_EXEL = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query2; ?>&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E&exportFormat=xls";

$.ss(SS_URL)
.setQuery("<?php echo $query; ?>")
.setField("J,E,F,I,C")
.send(function(success) {
    if(!success) return;
    var con = $('#content')
              var str = "<a href=\"" + SS_URL_EXEL +"\">Descargar en formato Excel</a><br><table class='templateTable'>"
    this.each(function(i, k) {
        str += "<tr><td><b>Título del artículo " + (i+1) + "</b></td><td align='center' colspan='2'><b>" + this['J'] + "</b></td></tr>\
             <tr><td>Autores</td><td colspan='2'>" + this['E'] + "</td></tr>\
             <tr><td>Revista</td><td>Nombre: " + this['F'] + "</td><td>ISSN " + this['I'] + "</td></tr>\
             <tr><td>Fecha de publicación</td><td>" + this['C'] + "</td><td>Clasificación a la fecha de publicación: FALTA</td></tr>"
    })
                        str += "</table>"
                               con.html(con.html() + str)
});