<?php
include "header.php";
?>

Vítejte v naší virtuální knihovnì. Tato aplikace sloužík získávání informací o obsahu našeho knihovního fondu.

<p>
<h2>Pøehled autorù:</h2>
<?php pismena("a"); ?>
<p>

<h2>Pøehled titulù:</h2>
<?php pismena("t") ;	?>
<h2>Vyhledávání</h2>
<form action="search.php" method="get">
<input name="hledat">
<input type="radio" name="kde" value="a" checked> Autor  &nbsp; &nbsp; &nbsp;
<input type="radio" name="kde" value="t"> Název
 &nbsp; &nbsp; &nbsp; &nbsp;
<input type="submit" name="ok" value="Hledej!">  
</form>

<?php  include_once "footer.php"; ?>
