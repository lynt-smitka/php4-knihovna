<?php
include "header.php";

/* V�pis detail� o konkr�tn� publikaci **
Vyp�e v�echny dostupn� informace o knize
v�etn� celkov�ho po�tu v�tisk� a v�tisk� 
aktu�ln� k dispozici

Parametry:
id - identifika�n� ��slo publikace v tabulce knihy
****************************************/

connect();
$sql = "select * from knihy where id = $id";
$res = mysql_query($sql);
if ($zaznam = mysql_fetch_array($res)) {
  echo "<h1>{$zaznam["autor"]} : {$zaznam["nazev"]}</h1>\n";
  if ($zaznam["prekladatel"]) 
    echo "P�eklad: {$zaznam["prekladatel"]}<p>";
  if ($zaznam["vydavatel"]) {
    echo "Vydalo nakladatelstv� {$zaznam["vydavatel"]}";
    if ($zaznam["rok_vydani"])
      echo ", {$zaznam["rok_vydani"]}";
    echo "<p>\n";
  }
  if ($zaznam["isbn"])
    echo "ISBN <b>{$zaznam["isbn"]}</b><p>\n";
  if ($zaznam["anotace"])
    echo "<i>{$zaznam["anotace"]}</i><p>";
  // konec v�pisu dat z tabulky knihy

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