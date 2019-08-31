<?php
/*********************************
Vyhledává v databázi podle jména autora èi podle názvu
Parametry: 
  hledat - hledaný text
  kde - zda je hledáno jméno autora èi název publikace
  poc - zda hledat od zaèátku øetìzce nebo kdekoli
**********************************/

include_once "header.php";

if ($hledat && $kde) {
  connect();
  $sql = "select id, autor, nazev from knihy where "; 
  $sql .= ($kde=="t") ? "nazev" : "autor";
  $sql .= " like '";
  if (!$poc) $sql .= "%";
  $sql .= "$hledat%' order by autor, nazev";
  if (!$res = mysql_query($sql)) echo "Chyba<br>Dotaz: $sql";

  echo ($kde=="t") ? "<h1>Tituly" : "<h1>Autoøi";

  if ($poc)
    echo " - rejstøík, písmeno " . strtoupper($hledat);
  else 
    echo " - vyhledávání '$hledat'";
  echo "</h1>\n";

  echo "Nalezeno celkem " . mysql_num_rows($res) . " záznamù:<p>\n";

  while ($zaznam = mysql_fetch_array($res))
    echo "<a href=\"detail.php?id={$zaznam["id"]}\">{$zaznam["autor"]} - {$zaznam["nazev"]}</a><br>\n";
}

include_once "footer.php";
?>