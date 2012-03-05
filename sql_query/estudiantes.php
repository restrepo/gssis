<?
//IMPORTANT REFERENCE:
//http://code.google.com/apis/chart/interactive/docs/querylanguage.html
//See "Setting the Query in the Data Source URL"
//===SAMPLES==================================
//http://blogoscoped.com/archive/2009-09-11-n77.html
//http://goo.gl/sto8p
//http://www.rossgoodman.com/2010/04/19/google-spreadsheet-sql-queries/
//Example with date:
//http://goo.gl/HKbLO
//visitantes: 0AjqGPI5Q_Ez6dGllR2x3NHEwU1VaV3oyb2pFVl9Vc0E
//select B,E,F where (E= date '2011-01-19')
//select B,E,F where (E > date '2011-01-01')
//==========URL SAMPLE============
//https://spreadsheets.google.com/tq?tqx=out:html&tq=
//select E,F,G,H,C,J where (E contains 'Osorio') order by A desc
//&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E
echo '<head>
<link href="/gssis/public/css/global.css" media="screen" rel="stylesheet" type="text/css">
<title>SQL queryes in public Google Spreadsheets</title></head>';

echo '<body>';
echo '<header>GDS Instituto de Física</header>';
echo '<h1>Búsqueda SQL: estudiantes Instituto de Física</h1>';


echo '
<form action="estudiantes.php" method="POST">
<input type="hidden" name="step" value="1">
<input type="hidden" name="key" value="0AuLa_xuSIEvxdERYSGVQWDBTX1NCN19QMXVpb0lhWXc">';
echo "<select name=\"sheetgid\">
<option selected value=\"2\">Seleccione tipo de Estudiante:
<option value=\"0\">Estudiantes de Pregrado\n
<option value=\"1\">Estudiantes de Maestria\n
<option value=\"2\">Estudiantes de Doctorado\n";
echo"</select><br/><br/>";
echo '
Ingrese su búsqueda SQL de acuerdo a los siguientes rótulos:<br/><br/>

B: Nombre, D: Fecha de Ingreso, E: Fecha de Graduación, <br/>
G: Grupo, H: Asesor, K: Título de tesis <br/><br/>

Ejemplo:<br/>
select B,H,D where (D contains \'2010\') order by H desc<br/>

SQL query: <input type="text" name="sql_query" value="'.$sql_query.'">
<input type=submit value=Go>
<hr width="100%" size="2">
</form>
';
if($_POST["step"]==1)
  {

    $urlss="https://spreadsheets.google.com/tq?tqx=out:html&tq=";    
    $urlss=$urlss.$_POST['sql_query'];
    if($_POST['sheet']){
      //check: http://goo.gl/MqBiC
      $urlss=$urlss."&sheet=".$_POST['sheet'];
    }
    if($_POST['sheetgid']){
      $urlss=$urlss."&gid=".$_POST['sheetgid'];
    }
    $urlss=$urlss."&key=".$_POST['key'];
       echo "<meta http-equiv=\"refresh\" content=\"0; url=".$urlss."\">";
  }
echo '<a href="http://gfif.udea.edu.co/query.php">Búsqueda</a> sobre cualquier hoja de cálculo de Google';
echo '</html></body>';
//phpinfo(INFO_VARIABLES);
//STUDENTS SAMPLE
//sql_query= select B,H,D,E where (H contains 'Diego Restrepo' or H contains 'Ponce')
//key=0AuLa_xuSIEvxdERYSGVQWDBTX1NCN19QMXVpb0lhWXc
//sheet=Estudiantes_maestria
?>
