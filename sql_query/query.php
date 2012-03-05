<?
echo '<head><title>SQL queryes in public Google Spreadsheets</title>';
echo '<body>';
echo '<h1>SQL queryes in public Google Spreadsheets</h1>';
echo '
<form action="query.php" method="POST">
<input type="hidden" name="step" value="1">
SQL query: <input type="text" name="sql_query" value="'.$sql_query.'">
e.g: select E,F,G,H,C,J where (C contains \'2012\') order by A desc<br/>
Key <input type="text" name="key" value="'.$key.'">
e,g: 0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E<br/>
Optional parameters:<br/>
Sheet name <input type="text" name="sheet" value="'.$sheet.'"><br/>
Or sheet number <input type="text" name="sheetgid" value="'.$sheetgid.'"><br/>
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
echo '<a href=http://ouseful.open.ac.uk/datastore/gspreadsheetdb2.php>Another implementation</a> with query examples';
echo '</html></body>';
?>
