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
?>

<h1>Virtu�ln� knihovna - administra�n� ��st</h1>

<a href="a_knihy.php">Spr�va knih</a><br>
<a href="a_ctenari.php">Spr�va �ten���</a><br>
<br>
<br>
<form method="post" enctype="multipart/form-data">
Nahr�t zpravodaj: <input type="file" name="jmeno_souboru">
<input type="submit" value="Nahr�t">
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
