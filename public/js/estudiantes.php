
<?php
header('content-type: application/x-javascript');

$s = unserialize(urldecode(stripslashes($_GET['s'])));

$query="select C,D,G where C contains '".$s['name']."' and D contains '".$s['id']."' and G contains '".$s['advisor']."' and H contains '".$s['group']."' and I contains '".$s['projectid']."'";

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
  .setField("C,D,G")
  .send(function(success) {
    if(!success) return;
    var con = $('#content')
    var str = "<table class='templateTable'>"
    this.each(function(i, k) {
      str += "<tr><td colspan='4'><b>Estudiante matriculado o admitido " + (i+1) + "</b></td></tr>\
             <tr><td>Nombre:</td><td>" + this['C'] + "</td><td>Cédula:</td><td>" + this['D'] + "</td></tr>\
             <tr><td>Tutor:</td><td>" + this['G'] + "</td><td>Programa:</td><td>---</td></tr>\
             <tr><td colspan='2'>Trabajo de investigación o proyecto:</td><td colspan='2'>---</td></tr>"
     })
    str += "</table>"
    con.html(con.html() + str)
  });
