<?php
session_start();
//session_register("username");

include "header.php";
?>

<h1>Virtuální knihovna - odhlášení</h1>

<?php
unset($_SESSION["username"]);
if (!$_SESSION["username"])
  echo "Byl jste úspìšnì odhlášen ze systému.";
include_once "footer.php";
?>