<html>
<head>
<title>Seznam knih</title>
<script language="JavaScript" type="text/javascript">
<!--
function VratIdent(id) {
  window.opener.vypujceni.ident.value = id;
  window.close();
}
-->
</script>
</head>
<body>
<p>Kliknut�m na ident vlo��te tento ident do hlavn�ho formul��e</p>
<small>
<?php
mysql_connect("localhost", "root", "");
mysql_select_db("knihovna");
$sql = "select distinct ident, autor, nazev, vypujceno from knihy, 
        vytisky left join vypujcky on vytisky.id=id_vytisk
        where knihy.id = vytisky.id_knihy 
		and vypujceno is null
        order by autor, nazev, vypujceno desc";
$res = mysql_query($sql) or die("Chyba datab�ze, nepoda�ilo se prov�st dotaz <i>$sql</i>");
$puvodni = "";
while ($zaznam = mysql_fetch_array($res)) {
  if ($zaznam["autor"] . $zaznam["nazev"] != $puvodni) {
    // nov� titul
    $puvodni = $zaznam["autor"] . $zaznam["nazev"];
    echo "<br>{$zaznam["autor"]}: {$zaznam["nazev"]} - ";
  }
  // vypsat ident
  echo "<a href=\"\" onclick=\"VratIdent('{$zaznam["ident"]}')\">{$zaznam["ident"]}</a>\n";
}
?>
</small>
<p>
<input type="button" value="Zav��t" onclick="window.close()">
</body>
</html>
