<?php
session_start();
//session_register("username");
include "header.php";
connect();

  $sql = "select password from users where username = '$jmeno'";
  $res = mysql_query($sql);
  if ($res && mysql_num_rows($res) && md5($heslo)==mysql_result($res,0)) {
      $_SESSION["username"] = $jmeno;
      Header("Location:$back");
      exit;
  }

?>

<h1>Virtu�ln� knihovna - p�ihl�en�</h1>

<?php 
if ($chyba)
  echo "<p style=\"color: red\">Chyba. U�ivatelsk� jm�no neexistuje, nebo jste nezadal spr�vn� heslo.</p>";
?>

<form action="<?php echo $PHP_SELF ?>" method="post">
<input type="hidden" name="back" value="<?php echo $back ?>">

<table border=1 align="center">
  <tr><td>
    <table>
      <tr><td>U�ivatelsk� jm�no:</td>
          <td><input type="text" name="jmeno" value="<?php echo $jmeno ?>"></td>
      </tr>
      <tr><td>Heslo:</td>
          <td><input type="password" name="heslo"></td>
      <tr>
      <tr><td colspan="2"><input type="submit" name="ok" value="P�ihl�sit"></td></tr>
    </table>
  </td></tr>
</table>
</form>


<?php
include_once "footer.php";
?>
