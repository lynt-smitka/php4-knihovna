<?php
extract($_REQUEST);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

/* Definice globálních funkcí */
function connect() {
  $s = mysql_connect("localhost", "knihovna", "knihovnaheslo");
  mysql_query("SET NAMES cp1250");  
  mysql_select_db("knihovna");
  return $s;
}
 
function pismena($typ) {
  echo "<b>";
  for ($i = 65; $i < 91; $i++) {
    echo "<a href=\"search.php?poc=1&hledat=" . chr($i) . "&kde=$typ\">" . chr($i) . "</a>";
    if ($i != 90) echo " - ";
  }
  echo "</b><p>\n";
}

?>
<html>
  <head>
    <meta charset="windows-1250">   
    <title>Virtuální Knihovna</title>
   
  </head>
  <body>
<table border=5 width=90% align="center">  
<tr bgcolor="#ffff80"><td align=center>
    <h1>Virtuální knihovna</h1>  
</td></tr>
</table>
<p>       
    <table width=90% align="center">
    <tr><td>
   
