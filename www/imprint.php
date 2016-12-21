<?php
/**
 * Impressum
 *
 * Zeigt das Impressum an
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

require './includes/DEF.php';
require './includes/_top.php';
?>

<h2><?php echo $wlang['imprint']; ?></h2>

<div class='center'>
  <h3>Angaben gemäß § 5 TMG:</h3>
  <p>Daniel Kadenbach<br />
  Illerweg 16<br />

  30519 Hannover
  </p>
  <h3>Kontakt:</h3>

  <table class='block' align="center"><tr align="center"> <!-- TODO: Tabelle zentrieren-->
  <td>Telefon:</td>
  <td>+49 (0) 1577 2982413</td></tr>
  <tr><td>E-Mail:</td>
  <td>daniel.kadenbach@hs-hannover.de</td>
  </tr></table>

  <p> </p>
  <p>Quelle: <em><a href="https://www.e-recht24.de/artikel/datenschutz/209.html">https://www.e-recht24.de</a></em></p></a></em></p>
</div>

<?php
require './includes/_bottom.php';
?>
