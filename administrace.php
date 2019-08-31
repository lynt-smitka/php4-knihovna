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
?>

<h1>Virtuální knihovna - administraèní èást</h1>

<a href="a_knihy.php">Správa knih</a><br>
<a href="a_ctenari.php">Správa ètenáøù</a><br>
<br>
<br>
<form method="post" enctype="multipart/form-data">
Nahrát zpravodaj: <input type="file" name="jmeno_souboru">
<input type="submit" value="Nahrát">
</form>

<?php
if (is_uploaded_file($_FILES["jmeno_souboru"]["tmp_name"])):
  $name = $_FILES["jmeno_souboru"]["name"];
  move_uploaded_file($_FILES["jmeno_souboru"]["tmp_name"], "./zpravodaj/$name");
endif;
?>

<?php
include_once "footer.php";
?>
