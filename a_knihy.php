<?php
session_start();
$PHP_SELF = $_SERVER['PHP_SELF'];
if (!$_SESSION["username"]) {
  // není-li u&#65533;ivatel pøihlá&#65533;en, je pøesmìrován na pøihla&#65533;ovací stránku
  Header("Location:login.php?back=$PHP_SELF");
  exit;
}

include "header.php";
$show = true; // Promìná urèující, zda má bıt zobrazen základní formuláø

switch ($akce) {
case "insert":
  // vkládání záznamu
  connect();
  $sql = "insert into knihy values (0, '$nazev', '$autor', '$prekladatel', '$isbn', '$vydavatel', '$rok_vydani', '$anotace')";
  if ($res = mysql_query($sql)) {
    $id_knihy = mysql_insert_id();
    $vyt_pole = split(";", $vytisky);
    foreach ($vyt_pole as $vytisk) {
      $sql = "insert into vytisky values(0, $id_knihy, '" . trim($vytisk) . "')";
      mysql_query($sql);
    }
    echo "V poøádku, záznam byl vloen<hr>\n";
  }
  else
    echo "Bohuel, záznam se nepodaøilo vloit.<br>Dotaz: $sql<hr>\n";
  break;

case "search":
  // zobrazení záznamù
  connect();
  $sql = "select knihy.id, autor, nazev, count(vytisky.id) as pocet 
          from knihy left join vytisky on knihy.id = vytisky.id_knihy";
  if ($kde) {
    $sql .= " where ";
    $sql .= ($kde == "t") ? "nazev" : "autor";
    $sql .= " like '%$text%'";
  }
  $sql .= " group by knihy.id order by autor";
  $res = mysql_query($sql);
  if (!$res) 
    break;
  while ($z = mysql_fetch_array($res)) {
    echo "{$z["autor"]}: {$z["nazev"]} ({$z["pocet"]} vıtiskù)";
    echo " - <a href=\"$PHP_SELF?akce=detail&id={$z["id"]}\">Detaily</a> <a href=\"$PHP_SELF?akce=delete&id={$z["id"]}\">Smazat</a><br>";
  } 
  $show = false;
  break;

case "detail":
  // zobrazení detailních informací o knize
  connect();
  $sql = "select * from knihy where id = $id";
  $res = mysql_query($sql);
  if ($zaznam = mysql_fetch_array($res)) {
    echo "<h1>{$zaznam["autor"]}: {$zaznam["nazev"]}</h1>\n";
    echo "<form action=\"$PHP_SELF\" method=post>\n";
    echo "<table>";
    echo "<tr><td><b>Autor:</b></td><td><input type=text name=autor value='{$zaznam["autor"]}'></td></tr>\n";
    echo "<tr><td><b>Název:</b></td><td><input type=text name=nazev value='{$zaznam["nazev"]}'></td></tr>\n";
    echo "<tr><td><b>Pøekladatel:</b></td><td><input type=text name=prekladatel value='{$zaznam["prekladatel"]}'></td></tr>\n";
    echo "<tr><td><b>Vydavatel:</b></td><td><input type=text name=vydavatel value='{$zaznam["vydavatel"]}'></td></tr>\n";
    echo "<tr><td><b>Rok vydání:</b></td><td><input type=text name=rok_vydani value='{$zaznam["rok_vydani"]}'></td></tr>\n";
    echo "<tr><td><b>ISBN:</b></td><td><input type=text name=isbn value='{$zaznam["isbn"]}'></td></tr>\n";
    echo "<tr><td valign=top><b>Anotace:</b></td><td><textarea name=anotace cols=50>{$zaznam["anotace"]}</textarea></td></tr>\n";
    echo "<tr><td valign=top><b>K dispozici vıtisky:</b></td><td>\n";
    $sql = "select ident, vypujceno, vytisky.id from vytisky left join vypujcky on vytisky.id = vypujcky.id_vytisk 
            where id_knihy = $id order by ident";
    $res = mysql_query($sql);
    while ($vytisk = mysql_fetch_array($res)) {
      echo "- {$vytisk["ident"]} ";
      if ($vytisk["vypujceno"])
        echo "(vypùjèeno {$vytisk["vypujceno"]}) <br>\n";
      else 
        echo "<a href=\"$PHP_SELF?akce=smazat_vytisk&vytisk={$vytisk["id"]}\">smazat</a><br>\n";
    }
    echo "Novı vıtisk: <input type=text name=vytisk>";
    echo "</td></tr>\n</table>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=update>\n";
    echo "<input type=submit value=\"Upravit data\">\n";
    echo "</form>\n";
    echo "<form action=\"$PHP_SELF\" method=post>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=delete>\n";
    echo "<input type=submit value=\"Smazat záznam\">\n";
    echo "</form>\n";
    echo "<hr>";
    $show = false;
  }
  else
    echo "Chyba. Záznam nebyl nalezen, pravdìpodobnì byl mezitím smazán.<br>";
  break;

case "delete":
  // smazání záznamu zadaného pomocí ID
  connect();
  $sql = "select vypujceno from vypujcky, vytisky where id_vytisk = vytisky.id 
          and id_knihy = $id";
  $res = mysql_query($sql);
  if (mysql_num_rows($res)) {
    echo "Tuto knihu nelze smazat, nebo nìkteré její vıtisky jsou dosud vypùjèené!";
    break;
  }
  $sql = "delete from vytisky where id_knihy = $id";
  if (mysql_query($sql)) {
    $sql = "delete from knihy where id = $id";
    if (mysql_query($sql))
      echo "V poøádku, záznam byl smazán<hr>\n";
    else
      echo "Bohuel, záznam se nepodaøilo smazat.<br>Dotaz: $sql<hr>\n";
  }
  else
    echo "Bohuel, záznam se nepodaøilo smazat.<br>Dotaz: $sql<hr>\n";
  break;

case "update":
  // úprava dat o knize
  connect();
  $sql = "update knihy set autor = '$autor', nazev = '$nazev', prekladatel = '$prekladatel', 
          vydavatel = '$vydavatel', rok_vydani = '$rok_vydani', isbn = '$isbn',
          anotace = '$anotace' where id = $id";
  if (mysql_query($sql)) {
    if ($vytisk) {
      $vyt_pole = split(";", $vytisk);
      foreach ($vyt_pole as $ident) {
        $sql = "insert into vytisky values(0, $id, '" . trim($ident) . "')";
        mysql_query($sql);
      }
    }
    echo "Ok, záznam upraven.<br>";
  }
  else
    echo "Chyba, nepodaøilo se data upravit.<br>Dotaz: $sql<br>";
  break;

case "smazat_vytisk":
  connect();
  $sql = "delete from vytisky where id = $vytisk";
  if (mysql_query($sql))
    echo "V poøádku, záznam byl smazán<hr>\n";
  else
    echo "Bohuel, záznam se nepodaøilo smazat.<br>Dotaz: $sql<hr>\n";
  break;
} // *** case ***

if ($show) {
  // v nìkterıch pøípadech se formuláø nezobrazuje
?>

<h1>Virtuální knihovna - administraèní èást, knihy</h1>

<!-- Formuláø pro vlo&#65533;ení nové knihy -->
<h2>Nová publikace</h2>
<form action="<?php echo $PHP_SELF ?>" method="post">
<table>
<tr><td>Autor:</td>
    <td><input type="text" name="autor"></td>
</tr>
<tr><td>Název:</td>
    <td><input type="text" name="nazev"></td>
</tr>
<tr><td>Vydavatelství:</td>
    <td><input type="text" name="vydavatel"></td>
</tr>
<tr><td>Rok vydání:</td>
    <td><input type="text" name="rok_vydani"></td>
</tr>
<tr><td>Pøekladatel:</td>
    <td><input type="text" name="prekladatel"></td>
</tr>
<tr><td>ISBN:</td>
    <td><input type="text" name="isbn"></td>
</tr>
<tr><td>Vıtisk(y):</td>
    <td><input type="text" name="vytisky"><i>Je-li k dispozici více vıtiskù, oddìlujte je støedníkem</i></td>
</tr>
<tr><td>Anotace:</td>
    <td><textarea name="anotace" cols=50></textarea></td>
</tr>
</table>
<input type="hidden" name="akce" value="insert">
<input type="submit" value="Vloit">
</form>

<!-- Formuláø pro vyhledávání -->
<h2>Vıpis</h2>
<form action="<?php echo $PHP_SELF ?>" method="post">
<i>Nezadáte-li ádné omezení, budou vypsány všechny záznamy.</i><br>
Vyhledat: <input type="text" name="text"><br>
<input type="radio" name="kde" value="t"> v názvech
<input type="radio" name="kde" value="a"> ve jménech autorù<br>
<input type="hidden" name="akce" value="search">
<input type="submit" value="Zobrazit">
</form>

<?php
} // if - zobrazení formuláøe
include_once "footer.php";
?>