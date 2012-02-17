
<?php
header('content-type: application/x-javascript');

$s = unserialize(urldecode(stripslashes($_GET['s'])));

$query="select A,B,C,D,E,F,G,H,I,J,K,L,M where C contains '".$s['id']."' and D contains '".$s['name']."' and E contains '".$s['manager']."' and I contains '".$s['type']."' and K contains '".$s['group']."'";
$query2="select+A,B,C,D,E,F,G,H,I,J,K,L,M+where+(C+contains+'".$s['id']."'+and+D+contains+'".$s['name']."'+and+E+contains+'".$s['manager']."'+and+I+contains+'".$s['type']."'+and+K+contains+'".$s['group']."')";

if(!empty($s['yearInit']))
{
    $query=$query." and G >= ".$s['yearInit']."";
    $query2=$query2."+and+(G>='".$s['yearInit']."')";
}
if(!empty($s['yearEnd']))
{
    $query=$query." and H <= ".$s['yearEnd']."";
    $query2=$query2."+and+(H<='".$s['yearEnd']."')";
}
?>

var SS_URL = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE";
var SS_URL_EXEL = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query2; ?>&key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE&exportFormat=xls";

$.ss(SS_URL)
.setQuery("<?php echo $query; ?>")
.setField("A,B,C,D,E,F,G,H,I,J,K,L,M")
.send(<?php echo $s['format']; ?>);

function codi (success) {
    if(!success) return;
    var con = $('#content')
              var str = "<a href=\"" + SS_URL_EXEL +"\">Descargar en formato Excel</a><br><table class='templateTable'>"
    this.each(function(i, k) {
        str += "<tr><td colspan='4'><b>Proyecto " + (i+1) + ": " + this['C'] + "</b></td></tr>\
                <tr><td colspan='4'>Investigador principal: " + this['D'] + "</td></tr>\
                <tr><td>Financiadores</td><td colspan='3'>" + this['H'] + "</td></tr>\
                <tr><td>Convocatoria</td><td colspan='3'>" + this['E'] + "</td></tr>\
                <tr><td>Fecha de inicio</td><td>" + this['F'] + "</td><td>Duración en meses<br>o fecha de<br>finalización</td><td>" + this['G'] + "</td></tr>\
                <tr><td>Valor total en $ millones</td><td>" + this['K'] + "</td><td>% Cofinanciación U.<br>de A.</td><td>" + this['L'] + "</td></tr>"
    })
                        str += "</table>"
                               con.html(con.html() + str)
}

function list (success) {
    if(!success) return;
    var con = $('#content')
              var str = "<a href=\"" + SS_URL_EXEL +"\">Descargar en formato Excel</a><br>\
               <table class='templateTable'><tr><th>Timestamp</th><th>Proyecto ID</th><th>Título</th><th>Investigador Principal</th><th>Acta</th><th>Fecha de Inicio</th><th>Fecha de Finalización</th><th>Tipo</th><th>Centro</th><th>Grupo</th><th>Valor Total</th><th>% Cofinanciación UdeA</th></tr>"
    this.each(function(i, k) {
        str += "<tr><td>" + this['A'] + "</td><td>" + this['C'] + "</td><td>" + this['D'] + "</td><td>" + this['E'] + "</td><td>" + this['F'] + "</td><td>" + this['G'] + "</td><td>" + this['H'] + "</td><td>" + this['I'] + "</td><td>" + this['J'] + "</td><td>" + this['K'] + "</td><td>" + this['L'] + "</td><td>" + this['M'] + "</td></tr>"
    })
                        str += "</table>"
                               con.html(con.html() + str)
}