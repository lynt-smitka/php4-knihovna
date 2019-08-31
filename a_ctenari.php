<?php
session_start();
$PHP_SELF = $_SERVER['PHP_SELF'];
//session_register("username");
if (!$_SESSION["username"]) {
  // není-li uivatel pøihlášen, je pøesmìrován na pøihlašovací stránku
  Header("Location:login.php?back=$PHP_SELF");
  exit;
}

include "header.php";

$show = true; // Promìná urèující, zda má bıt zobrazen základní formuláø

switch ($akce) {
case "insert":
  // vkládání záznamu
  connect();
  $sql = "insert into ctenari values (0, '$jmeno', '$adresa', '$tel', '$email', '" 
         . date("Y-m-d") . "', '" . date("Y-m-d", time() + $placeno * 30.5 * 24 * 60 * 60) . "')";
  if (mysql_query($sql))
    echo "V poøádku, záznam byl vloen<hr>\n";
  else
    echo "Bohuel, záznam se nepodaøilo vloit.<br>Dotaz: $sql<hr>\n";
  break;

case "search":
  // zobrazení záznamù
  connect();
  $sql = "select ctenari.id, jmeno, adresa, vypujceno, count(vypujcky.id) as pocet
          from ctenari left join vypujcky on ctenari.id = vypujcky.id_ctenar";
  if ($jmeno)
    $sql .= " where jmeno like '%$jmeno%'";
  $sql .= " group by ctenari.id order by jmeno";
  $res = mysql_query($sql);
  if (!$res) 
    break;
  while ($z = mysql_fetch_array($res)) {
    echo "{$z["jmeno"]}, {$z["adresa"]} ({$z["pocet"]})";
    echo " - <a href=\"$PHP_SELF?akce=detail&id={$z["id"]}\">Detaily</a> <a href=\"$PHP_SELF?akce=delete&id={$z["id"]}\">Smazat</a><br>";
  } 
  $show = false;
  break;

case "detail":
  // zobrazení detailních informací o ètenáøi
  connect();
  $sql = "select * from ctenari where id = $id";
  $res = mysql_query($sql);
  if ($zaznam = mysql_fetch_array($res)) {
    echo "<h1>{$zaznam["jmeno"]}</h1>\n";
    echo "<form action=\"$PHP_SELF\" method=post>\n";
    echo "<table>";
    echo "<tr><td><b>Jméno:</b></td><td><input type=text name=jmeno value='{$zaznam["jmeno"]}'></td></tr>\n";
    echo "<tr><td><b>Adresa:</b></td><td><input type=text name=adresa value='{$zaznam["adresa"]}'></td></tr>\n";
    echo "<tr><td><b>Telefon:</b></td><td><input type=text name=telefon value='{$zaznam["telefon"]}'></td></tr>\n";
    echo "<tr><td><b>E-mail:</b></td><td><input type=text name=email value='{$zaznam["email"]}'></td></tr>\n";
    echo "<tr><td><b>Èlenem od:</b></td><td><input type=text name=clenstvi_od value='{$zaznam["clenstvi_od"]}' readonly></td></tr>\n";
    echo "<tr><td><b>Pøíspìvky do:</b></td><td><input type=text name=placeno value='{$zaznam["placeno"]}'></td></tr>\n";
    echo "<tr><td valign=top><b>Vypùjèené knihy:</b></td><td>\n<ul>";
    $sql = "select autor, nazev, vypujceno, doba, vypujcky.id from vypujcky, vytisky, knihy
            where id_ctenar = $id and id_vytisk = vytisky.id 
            and id_knihy = knihy.id";
    $res = mysql_query($sql);
    while ($kniha = mysql_fetch_array($res))
      echo "<li> {$kniha["autor"]}: {$kniha["nazev"]} (vypùjèeno {$kniha["vypujceno"]} na {$kniha["doba"]} dní)
            <a href=\"$PHP_SELF?akce=vratit&kniha={$kniha["id"]}\">vrátit</a>\n";
    echo "</ul>\n</td></tr>\n</table>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=update>\n";
    echo "<input type=submit value=\"Upravit data\">\n";
    echo "</form>\n";
    echo "<form action=\"$PHP_SELF\" method=post>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=delete>\n";
    echo "<input type=submit value=\"Smazat záznam\">\n";
    echo "</form>\n";
    echo "<form action=\"$PHP_SELF\" method=post name=vypujceni>\n";
    echo "<h3>Vypùjèení nové knihy</h3>";
    echo "Ident vıtisku: <input type=text name=ident>
          <button onclick=\"window.open('a_vyber.php','Seznam',
          'height=400,width=300,toolbar=no,menubar=no,location=no')\">Vıbìr</button><br>\n";
    echo "Pùjèit na <input type=text name=doba value=31> dní.<br>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=pujcit>\n";
    echo "<input type=submit value=\"Pùjèit knihu\">\n";
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
  $sql = "select id from vypujcky where id_ctenar = $id";
  $res = mysql_query($sql);
  if (mysql_num_rows($res)) {
    echo "Tohoto ètenáøe nelze vyøadit z databáze, nebo má ještì vypùjèené knihy!";
    break;
  }
  $sql = "delete from ctenari where id = $id";
  if (mysql_query($sql))
    echo "V poøádku, záznam byl smazán<hr>\n";
  else
    echo "Bohuel, záznam se nepodaøilo smazat.<br>Dotaz: $sql<hr>\n";
  break;

case "update":
  // úprava dat o ètenáøi
  connect();
  $sql = "update ctenari set jmeno = '$jmeno', adresa = '$adresa', telefon = '$telefon', 
          email = '$email', placeno = '$placeno' where id = $id";
  if (mysql_query($sql))
    echo "Ok, záznam upraven.<br>";
  else
    echo "Chyba, nepodaøilo se data upravit.<br>Dotaz: $sql<br>";
  break;

case "vratit":
  // vrácení zadané knihy
  connect();
  $sql = "select * from vypujcky where id = $kniha";
  $vypujcka = mysql_fetch_array(mysql_query($sql));
  $sql = "insert into archiv values (0, {$vypujcka["id_ctenar"]}, {$vypujcka["id_vytisk"]}, 
          '{$vypujcka["vypujceno"]}', {$vypujcka["doba"]}, '" . date("Y-m-d") . "')";
  mysql_query($sql);
  $sql = "delete from vypujcky where id = $kniha";
//  $sql = "update vypujcky set vraceno = '" . date("Y-m-d") . "' where id = $kniha";
  if (mysql_query($sql))
    echo "Ok, kniha byla vrácena<br>";
  else
    echo "Chyba, nepodaøilo se provést operaci.<br>Dotaz: $sql<br>";
  break;

case "pujcit":
  // vypùjèení knihy zadané pomocí identu
  connect();
  $sql = "select id from vytisky where ident = '$ident'";
  $res = mysql_query($sql);
  if ($res) {
    if ($z = mysql_fetch_array($res)) {
      $sql = "insert into vypujcky values (0, $id, {$z["id"]}, '" .
              date("Y-m-d") . "', $doba)";
      if (mysql_query($sql))
        echo "V poøádku, záznam o vıpùjèce byl vloen do databáze.<br>\n";
    }
    else
      echo "Chyba. Tento ident neexistuje!<br>\n";
  }
  else
    echo "Chyba v dotazu. Dotaz: $sql";
  break;

} // *** case ***

if ($show) {
  // v nìkterıch pøípadech se formuláø nezobrazuje
?>

<h1>Virtuální knihovna - administraèní èást, ètenáøi</h1>

<!-- Formuláø pro vloení nového ètenáøe -->
<h2>Vloení dat o novém ètenáøi</h2>
<form action="<?php echo $PHP_SELF ?>" method="post">
<table>
<tr><td>Jméno:</td>
    <td><input type="text" name="jmeno"></td>
</tr>
<tr><td>Adresa:</td>
    <td><input type="text" name="adresa"></td>
</tr>
<tr><td>Telefon:</td>
    <td><input type="text" name="tel"></td>
</tr>
<tr><td>E-mail:</td>
    <td><input type="text" name="email"></td>
</tr>
<tr><td>Placeno na:</td>
    <td><input type="text" name="placeno" value="12" size=5> mìsícù</td>
</tr>
</table>
<input type="hidden" name="akce" value="insert">
<input type="submit" value="Vloit">
</form>

<!-- Formuláø pro vyhledávání -->
<h2>Vıpis</h2>
<form action="<?php echo $PHP_SELF ?>" method="post">
<i>Nezadáte-li ádné omezení, budou vypsány všechny záznamy.</i><br>
Vyhledat: <input type="text" name="jmeno"><br>
<input type="hidden" name="akce" value="search">
<input type="submit" value="Zobrazit">
</form>

<?php
} // if - zobrazení formuláøe
include_once "footer.php";
?>