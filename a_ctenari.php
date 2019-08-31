<?php
session_start();
$PHP_SELF = $_SERVER['PHP_SELF'];
//session_register("username");
if (!$_SESSION["username"]) {
  // nen�-li u�ivatel p�ihl�en, je p�esm�rov�n na p�ihla�ovac� str�nku
  Header("Location:login.php?back=$PHP_SELF");
  exit;
}

include "header.php";

$show = true; // Prom�n� ur�uj�c�, zda m� b�t zobrazen z�kladn� formul��

switch ($akce) {
case "insert":
  // vkl�d�n� z�znamu
  connect();
  $sql = "insert into ctenari values (0, '$jmeno', '$adresa', '$tel', '$email', '" 
         . date("Y-m-d") . "', '" . date("Y-m-d", time() + $placeno * 30.5 * 24 * 60 * 60) . "')";
  if (mysql_query($sql))
    echo "V po��dku, z�znam byl vlo�en<hr>\n";
  else
    echo "Bohu�el, z�znam se nepoda�ilo vlo�it.<br>Dotaz: $sql<hr>\n";
  break;

case "search":
  // zobrazen� z�znam�
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
  // zobrazen� detailn�ch informac� o �ten��i
  connect();
  $sql = "select * from ctenari where id = $id";
  $res = mysql_query($sql);
  if ($zaznam = mysql_fetch_array($res)) {
    echo "<h1>{$zaznam["jmeno"]}</h1>\n";
    echo "<form action=\"$PHP_SELF\" method=post>\n";
    echo "<table>";
    echo "<tr><td><b>Jm�no:</b></td><td><input type=text name=jmeno value='{$zaznam["jmeno"]}'></td></tr>\n";
    echo "<tr><td><b>Adresa:</b></td><td><input type=text name=adresa value='{$zaznam["adresa"]}'></td></tr>\n";
    echo "<tr><td><b>Telefon:</b></td><td><input type=text name=telefon value='{$zaznam["telefon"]}'></td></tr>\n";
    echo "<tr><td><b>E-mail:</b></td><td><input type=text name=email value='{$zaznam["email"]}'></td></tr>\n";
    echo "<tr><td><b>�lenem od:</b></td><td><input type=text name=clenstvi_od value='{$zaznam["clenstvi_od"]}' readonly></td></tr>\n";
    echo "<tr><td><b>P��sp�vky do:</b></td><td><input type=text name=placeno value='{$zaznam["placeno"]}'></td></tr>\n";
    echo "<tr><td valign=top><b>Vyp�j�en� knihy:</b></td><td>\n<ul>";
    $sql = "select autor, nazev, vypujceno, doba, vypujcky.id from vypujcky, vytisky, knihy
            where id_ctenar = $id and id_vytisk = vytisky.id 
            and id_knihy = knihy.id";
    $res = mysql_query($sql);
    while ($kniha = mysql_fetch_array($res))
      echo "<li> {$kniha["autor"]}: {$kniha["nazev"]} (vyp�j�eno {$kniha["vypujceno"]} na {$kniha["doba"]} dn�)
            <a href=\"$PHP_SELF?akce=vratit&kniha={$kniha["id"]}\">vr�tit</a>\n";
    echo "</ul>\n</td></tr>\n</table>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=update>\n";
    echo "<input type=submit value=\"Upravit data\">\n";
    echo "</form>\n";
    echo "<form action=\"$PHP_SELF\" method=post>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=delete>\n";
    echo "<input type=submit value=\"Smazat z�znam\">\n";
    echo "</form>\n";
    echo "<form action=\"$PHP_SELF\" method=post name=vypujceni>\n";
    echo "<h3>Vyp�j�en� nov� knihy</h3>";
    echo "Ident v�tisku: <input type=text name=ident>
          <button onclick=\"window.open('a_vyber.php','Seznam',
          'height=400,width=300,toolbar=no,menubar=no,location=no')\">V�b�r</button><br>\n";
    echo "P�j�it na <input type=text name=doba value=31> dn�.<br>\n";
    echo "<input type=hidden name=id value={$zaznam["id"]}>\n";
    echo "<input type=hidden name=akce value=pujcit>\n";
    echo "<input type=submit value=\"P�j�it knihu\">\n";
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
  $sql = "select id from vypujcky where id_ctenar = $id";
  $res = mysql_query($sql);
  if (mysql_num_rows($res)) {
    echo "Tohoto �ten��e nelze vy�adit z datab�ze, nebo� m� je�t� vyp�j�en� knihy!";
    break;
  }
  $sql = "delete from ctenari where id = $id";
  if (mysql_query($sql))
    echo "V po��dku, z�znam byl smaz�n<hr>\n";
  else
    echo "Bohu�el, z�znam se nepoda�ilo smazat.<br>Dotaz: $sql<hr>\n";
  break;

case "update":
  // �prava dat o �ten��i
  connect();
  $sql = "update ctenari set jmeno = '$jmeno', adresa = '$adresa', telefon = '$telefon', 
          email = '$email', placeno = '$placeno' where id = $id";
  if (mysql_query($sql))
    echo "Ok, z�znam upraven.<br>";
  else
    echo "Chyba, nepoda�ilo se data upravit.<br>Dotaz: $sql<br>";
  break;

case "vratit":
  // vr�cen� zadan� knihy
  connect();
  $sql = "select * from vypujcky where id = $kniha";
  $vypujcka = mysql_fetch_array(mysql_query($sql));
  $sql = "insert into archiv values (0, {$vypujcka["id_ctenar"]}, {$vypujcka["id_vytisk"]}, 
          '{$vypujcka["vypujceno"]}', {$vypujcka["doba"]}, '" . date("Y-m-d") . "')";
  mysql_query($sql);
  $sql = "delete from vypujcky where id = $kniha";
//  $sql = "update vypujcky set vraceno = '" . date("Y-m-d") . "' where id = $kniha";
  if (mysql_query($sql))
    echo "Ok, kniha byla vr�cena<br>";
  else
    echo "Chyba, nepoda�ilo se prov�st operaci.<br>Dotaz: $sql<br>";
  break;

case "pujcit":
  // vyp�j�en� knihy zadan� pomoc� identu
  connect();
  $sql = "select id from vytisky where ident = '$ident'";
  $res = mysql_query($sql);
  if ($res) {
    if ($z = mysql_fetch_array($res)) {
      $sql = "insert into vypujcky values (0, $id, {$z["id"]}, '" .
              date("Y-m-d") . "', $doba)";
      if (mysql_query($sql))
        echo "V po��dku, z�znam o v�p�j�ce byl vlo�en do datab�ze.<br>\n";
    }
    else
      echo "Chyba. Tento ident neexistuje!<br>\n";
  }
  else
    echo "Chyba v dotazu. Dotaz: $sql";
  break;

} // *** case ***

if ($show) {
  // v n�kter�ch p��padech se formul�� nezobrazuje
?>

<h1>Virtu�ln� knihovna - administra�n� ��st, �ten��i</h1>

<!-- Formul�� pro vlo�en� nov�ho �ten��e -->
<h2>Vlo�en� dat o nov�m �ten��i</h2>
<form action="<?php echo $PHP_SELF ?>" method="post">
<table>
<tr><td>Jm�no:</td>
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
    <td><input type="text" name="placeno" value="12" size=5> m�s�c�</td>
</tr>
</table>
<input type="hidden" name="akce" value="insert">
<input type="submit" value="Vlo�it">
</form>

<!-- Formul�� pro vyhled�v�n� -->
<h2>V�pis</h2>
<form action="<?php echo $PHP_SELF ?>" method="post">
<i>Nezad�te-li ��dn� omezen�, budou vyps�ny v�echny z�znamy.</i><br>
Vyhledat: <input type="text" name="jmeno"><br>
<input type="hidden" name="akce" value="search">
<input type="submit" value="Zobrazit">
</form>

<?php
} // if - zobrazen� formul��e
include_once "footer.php";
?>