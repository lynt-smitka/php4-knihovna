    </td></tr>
  <tr><td>
  <hr>
    <p align=center>
      <a href="index.php">[ Hlavní stránka ]</a> ||
      <a href="autori.php"> [ Seznam autorù ]</a> ||
      <a href="tituly.php"> [ Seznam titulù ]</a> ||
      <a href="administrace.php"> [ Administrace ]</a>
      <?php if ($_SESSION["username"])
        echo " || <a href=\"logout.php\"> [ Odhlášení ]</a>";
      ?>
    </p>
    </td></tr>
    </table>
</div>
</body>
</html>
