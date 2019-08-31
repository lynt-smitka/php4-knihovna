<?php
/*********************************
Je-li zadán parametr, zobrazí autory od zadaného písmena, 
jinak nabídne seznam poèáteèních písmen s odkazy
**********************************/

include_once "header.php";

echo "<h2>Pøehled autorù:</h2>";

pismena("a");

include_once "footer.php";
?>