<?php
include "header.php";

/* Výpis detailù o konkrétní publikaci **
Vypíše všechny dostupné informace o knize
vèetnì celkového poètu výtiskù a výtiskù 
aktuálnì k dispozici

Parametry:
id - identifikaèní èíslo publikace v tabulce knihy
****************************************/

connect();
$sql = "select * from knihy where id = $id";
$res = mysql_query($sql);
if ($zaznam = mysql_fetch_array($res)) {
  echo "<h1>{$zaznam["autor"]} : {$zaznam["nazev"]}</h1>\n";
  if ($zaznam["prekladatel"]) 
    echo "Pøeklad: {$zaznam["prekladatel"]}<p>";
  if ($zaznam["vydavatel"]) {
    echo "Vydalo nakladatelství {$zaznam["vydavatel"]}";
    if ($zaznam["rok_vydani"])
      echo ", {$zaznam["rok_vydani"]}";
    echo "<p>\n";
  }
  if ($zaznam["isbn"])
    echo "ISBN <b>{$zaznam["isbn"]}</b><p>\n";
  if ($zaznam["anotace"])
    echo "<i>{$zaznam["anotace"]}</i><p>";
  // konec výpisu dat z tabulky knihy

  $sql = "select ident, vypujceno from vytisky left join vypujcky on vytisky.id = vypujcky.id_vytisk where id_knihy = $id order by ident";
  $res = mysql_query($sql);
  if ($res && mysql_num_rows($res)) {
    echo ":-)<br>\n";
    while ($rec = mysql_fetch_array($res))
      if ($rec["vypujceno"])
        echo $rec["ident"] . " ";
      else
        echo "<b>{$rec["ident"]}</b> ";
    echo "\n<p>\n";
  }
}

include_once "footer.php";
?>