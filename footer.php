    </td></tr>
  <tr><td>
  <hr>
    <p align=center>
      <a href="index.php">[ Hlavn� str�nka ]</a> ||
      <a href="autori.php"> [ Seznam autor� ]</a> ||
      <a href="tituly.php"> [ Seznam titul� ]</a> ||
      <a href="administrace.php"> [ Administrace ]</a>
      <?php if ($_SESSION["username"])
        echo " || <a href=\"logout.php\"> [ Odhl�en� ]</a>";
      ?>
    </p>
    </td></tr>
    </table>
</div>
</body>
</html>
