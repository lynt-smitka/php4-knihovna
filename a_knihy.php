<?php
session_start();
$PHP_SELF = $_SERVER['PHP_SELF'];
if (!$_SESSION["username"]) {
  // nen�-li u&#65533;ivatel p�ihl�&#65533;en, je p�esm�rov�n na p�ihla&#65533;ovac� str�nku
  Header("Location:login.php?back=$PHP_SELF");
  exit;
}

include "header.php";
$show = true; // Prom�n� ur�uj�c�, zda m� b�t zobrazen z�kladn� formul��

switch ($akce) {
case "insert":
  // vkl�d�n� z�znamu
  connect();
  $sql = "insert into knihy values (0, '$nazev', '$autor', '$prekladatel', '$isbn', '$vydavatel', '$rok_vydani', '$anotace')";
  if ($res = mysql_query($sql)) {
    $id_knihy = mysql_insert_id();
    $vyt_pole = split(";", $vytisky);
    foreach ($vyt_pole as $vytisk) {
      $sql = "insert into vytisky values(0, $id_knihy, '" . trim($vytisk) . "')";
      mysql_query($sql);
    }
    echo "V po��dku, z�znam byl vlo�en<hr>\n";
  }
  else
    echo "Bohu�el, z�znam se nepoda�ilo vlo�it.<br>Dotaz: $sql<hr>\n";
  break;

case "search":
  // zobrazen� z�znam�
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
    echo "{$z["autor"]}: {$z["nazev"]} ({$z["pocet"]} v�tisk�)";
    echo " - <a href=\"$PHP_SELF?akce=detail&id={$z["id"]}\">Detaily</a> <a href=\"$PHP_SELF?akce=delete&id={$z["id"]}\">Smazat</a><br>";
  } 
  $show = false;
  break;

case "detail":
  // zobrazen� detailn�ch informac� o knize
  connect();
  $sql = "select * from knihy where id = $id";
  $res = mysql_query($sql);
  if ($zaznam = mysql_fetch_array($res)) {
    echo "<h1>{$zaznam["autor"]}: {$zaznam["nazev"]}</h1>\n";
    echo "<form action=\"$PHP_SELF\" method=post>\n";
    echo "<table>";
    echo "<tr><td><b>Autor:</b></td><td><input type=text name=autor value='{$zaznam["autor"]}'></td></tr>\n";
    echo "<tr><td><b>N�zev:</b></td><td><input type=text name=nazev value='{$zaznam["nazev"]}'></td></tr>\n";
    echo "<tr><td><b>P�ekladatel:</b></td><td><input type=text name=prekladatel value='{$zaznam["prekladatel"]}'></td></tr>\n";
    echo "<tr><td><b>Vydavatel:</b></td><td><input type=text name=vydavatel value='{$zaznam["vydavatel"]}'></td></tr>\n";
    echo "<tr><td><b>Rok vyd�n�:</b></td><td><input type=text name=rok_vydani value='{$zaznam["rok_vydani"]}'></td></tr>\n";
    echo "<tr><td><b>ISBN:</b></td><td><input type=text name=isbn value='{$zaznam["isbn"]}'></td></tr>\n";
    echo "<tr><td valign=top><b>Anotace:</b></td><td><textarea name=anotace cols=50>{$zaznam["anotace"]}</textarea></td></tr>\n";
    echo "<tr><td valign=top><b>K dispozici v�tisky:</b></td><td>\n";
    $sql = "select ident, vypujceno, vytisky.id from vytisky left join vypujcky on vytisky.id = vypujcky.id_vytisk 
            where id_knihy = $id order by ident";
    $res = mysql_query($sql);
    while ($vytisk = mysql_fetch_array($res)) {
      echo "- {$vytisk["ident"]} ";
      if ($vytisk["vypujceno"])
        echo "(vyp�j�eno {$vytisk["vypujceno"]}) <br>\n";
      else 
        echo "<a href=\"$PHP_SELF?akce=smazat_vytisk&vytisk={$vytisk["id"]}\">smazat</a><br>\n";
    }
    echo "Nov� v�tisk: <input type=text name=vytisk>";
    echo "</td></tr>\n</table>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=update>\n";
    echo "<input type=submit value=\"Upravit data\">\n";
    echo "</form>\n";
    echo "<form action=\"$PHP_SELF\" method=post>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=delete>\n";
    echo "<input type=submit value=\"Smazat z�znam\">\n";
    echo "</form>\n";
    echo "<hr>";
    $show = false;
  }
  else
    echo "Chyba. Z�znam nebyl nalezen, pravd�podobn� byl mezit�m smaz�n.<br>";
  break;

case "delete":
  // smaz�n� z�znamu zadan�ho pomoc� ID
  connect();
  $sql = "select vypujceno from vypujcky, vytisky where id_vytisk = vytisky.id 
          and id_knihy = $id";
  $res = mysql_query($sql);
  if (mysql_num_rows($res)) {
    echo "Tuto knihu nelze smazat, nebo� n�kter� jej� v�tisky jsou dosud vyp�j�en�!";
    break;
  }
  $sql = "delete from vytisky where id_knihy = $id";
  if (mysql_query($sql)) {
    $sql = "delete from knihy where id = $id";
    if (mysql_query($sql))
      echo "V po��dku, z�znam byl smaz�n<hr>\n";
    else
      echo "Bohu�el, z�znam se nepoda�ilo smazat.<br>Dotaz: $sql<hr>\n";
  }
  else
    echo "Bohu�el, z�znam se nepoda�ilo smazat.<br>Dotaz: $sql<hr>\n";
  break;

case "update":
  // �prava dat o knize
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
    echo "Ok, z�znam upraven.<br>";
  }
  else
    echo "Chyba, nepoda�ilo se data upravit.<br>Dotaz: $sql<br>";
  break;

case "smazat_vytisk":
  connect();
  $sql = "delete from vytisky where id = $vytisk";
  if (mysql_query($sql))
    echo "V po��dku, z�znam byl smaz�n<hr>\n";
  else
    echo "Bohu�el, z�znam se nepoda�ilo smazat.<br>Dotaz: $sql<hr>\n";
  break;
} // *** case ***

if ($show) {
  // v n�kter�ch p��padech se formul�� nezobrazuje
?>

<h1>Virtu�ln� knihovna - administra�n� ��st, knihy</h1>

<!-- Formul�� pro vlo&#65533;en� nov� knihy -->
<h2>Nov� publikace</h2>
<form action="<?php echo $PHP_SELF ?>" method="post">
<table>
<tr><td>Autor:</td>
    <td><input type="text" name="autor"></td>
</tr>
<tr><td>N�zev:</td>
    <td><input type="text" name="nazev"></td>
</tr>
<tr><td>Vydavatelstv�:</td>
    <td><input type="text" name="vydavatel"></td>
</tr>
<tr><td>Rok vyd�n�:</td>
    <td><input type="text" name="rok_vydani"></td>
</tr>
<tr><td>P�ekladatel:</td>
    <td><input type="text" name="prekladatel"></td>
</tr>
<tr><td>ISBN:</td>
    <td><input type="text" name="isbn"></td>
</tr>
<tr><td>V�tisk(y):</td>
    <td><input type="text" name="vytisky"><i>Je-li k dispozici v�ce v�tisk�, odd�lujte je st�edn�kem</i></td>
</tr>
<tr><td>Anotace:</td>
    <td><textarea name="anotace" cols=50></textarea></td>
</tr>
</table>
<input type="hidden" name="akce" value="insert">
<input type="submit" value="Vlo�it">
</form>

<!-- Formul�� pro vyhled�v�n� -->
<h2>V�pis</h2>
<form action="<?php echo $PHP_SELF ?>" method="post">
<i>Nezad�te-li ��dn� omezen�, budou vyps�ny v�echny z�znamy.</i><br>
Vyhledat: <input type="text" name="text"><br>
<input type="radio" name="kde" value="t"> v n�zvech
<input type="radio" name="kde" value="a"> ve jm�nech autor�<br>
<input type="hidden" name="akce" value="search">
<input type="submit" value="Zobrazit">
</form>

<?php
} // if - zobrazen� formul��e
include_once "footer.php";
?>