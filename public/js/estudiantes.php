
<?php
header('content-type: application/x-javascript');

$s = unserialize(urldecode(stripslashes($_GET['s'])));

$query="select J,E,F,I,C";

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

$.ss(SS_URL)
  .setQuery("<?php echo $query; ?>")
  .setField("J,E,F,I,C")
  .send(function(success) {
    if(!success) return;
    var con = $('#content')
    var str = "<table class='templateTable'>"
    this.each(function(i, k) {
      str += "<tr><td colspan='4'>Estudiante matriculado o admitido " + (i+1) + "</td></tr>\
             <tr><td>Nombre:</td><td>---</td><td>Cédula:</td><td>---</td></tr>\
             <tr><td>Tutor</td><td>---</td><td>Programa:</td><td>---</td></tr>\
             <tr><td colspan='2'>Trabajo de investigación o proyecto:</td><td colspan='2'>---</td></tr>"
     })
    str += "</table>"
    con.html(con.html() + str)
  });
