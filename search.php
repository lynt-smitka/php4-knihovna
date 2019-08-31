<?php
/*********************************
Vyhled�v� v datab�zi podle jm�na autora �i podle n�zvu
Parametry: 
  hledat - hledan� text
  kde - zda je hled�no jm�no autora �i n�zev publikace
  poc - zda hledat od za��tku �et�zce nebo kdekoli
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

  echo ($kde=="t") ? "<h1>Tituly" : "<h1>Auto�i";

  if ($poc)
    echo " - rejst��k, p�smeno " . strtoupper($hledat);
  else 
    echo " - vyhled�v�n� '$hledat'";
  echo "</h1>\n";

  echo "Nalezeno celkem " . mysql_num_rows($res) . " z�znam�:<p>\n";

  while ($zaznam = mysql_fetch_array($res))
    echo "<a href=\"detail.php?id={$zaznam["id"]}\">{$zaznam["autor"]} - {$zaznam["nazev"]}</a><br>\n";
}

include_once "footer.php";
?>