<?php
include "header.php";
?>

V�tejte v na�� virtu�ln� knihovn�. Tato aplikace slou��k z�sk�v�n� informac� o obsahu na�eho knihovn�ho fondu.

<p>
<h2>P�ehled autor�:</h2>
<?php pismena("a"); ?>
<p>

<h2>P�ehled titul�:</h2>
<?php pismena("t") ;	?>
<h2>Vyhled�v�n�</h2>
<form action="search.php" method="get">
<input name="hledat">
<input type="radio" name="kde" value="a" checked> Autor  &nbsp; &nbsp; &nbsp;
<input type="radio" name="kde" value="t"> N�zev
 &nbsp; &nbsp; &nbsp; &nbsp;
<input type="submit" name="ok" value="Hledej!">  
</form>

<?php  include_once "footer.php"; ?>
