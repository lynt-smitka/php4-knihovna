<?php
session_start();
//session_register("username");

include "header.php";
?>

<h1>Virtu�ln� knihovna - odhl�en�</h1>

<?php
unset($_SESSION["username"]);
if (!$_SESSION["username"])
  echo "Byl jste �sp�n� odhl�en ze syst�mu.";
include_once "footer.php";
?>