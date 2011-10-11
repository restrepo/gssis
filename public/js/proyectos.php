
<?php
header('content-type: application/x-javascript');

$s = unserialize(urldecode(stripslashes($_GET['s'])));

$query="select C,D,H,E,F,G,K,L where B contains '".$s['id']."' and C contains '".$s['name']."' and D contains '".$s['manager']."' and H contains '".$s['type']."' and J contains '".$s['group']."'";
$query2="select+C,D,H,E,F,G,K,L+where+(B+contains+'".$s['id']."'+and+C+contains+'".$s['name']."'+and+D+contains+'".$s['manager']."'+and+H+contains+'".$s['type']."'+and+J+contains+'".$s['group']."')";

// if(!empty($s['yearInit']))
// {
//     $query=$query." and F >= '".$s['yearInit']."'";
// }
// if(!empty($s['yearEnd']))
// {
//     $query=$query." and F <= ".$s['yearEnd'];
// }
?>

var SS_URL = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE";
var SS_URL_EXEL = "https://spreadsheets.google.com/feeds/download/spreadsheets/Export?tq=<?php echo $query2; ?>&key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE&exportFormat=xls";

$.ss(SS_URL)
.setQuery("<?php echo $query; ?>")
.setField("C,D,H,E,F,G,K,L")
.send(function(success) {
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
});
