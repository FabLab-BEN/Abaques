<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  $admin = true;
} else {
  $admin = false;
}
require("3db827cf9c82849e206f3caf038a8b6e71b55bf2.php");

if (isset($_GET['erreur'])) {
  switch ($_GET['erreur']) {
    case 0:
      echo '<div id="erreur" onclick="suppr()"><p>X</p><p class="titre">ERREUR : le formulaire n\'est pas complé !</p></div>';
      break;
    case 1;
      echo '<div id="erreur" onclick="suppr()"><p>X</p><p class="titre">ERREUR : Aucun temps ou volume défini</p></div>';
      break;
    case 2;
      echo '<div id="erreur" onclick="suppr()"><p>X</p><p class="titre">ERREUR : Valeur illégal</p></div>';
      break;
    default:
      break;
  }
}

if (isset($_GET['reset']) && $_GET['reset'] == true) {
  unset($_SESSION['machine']);
  unset($_SESSION['matiere']);
  unset($_SESSION['consommable']);
  unset($_SESSION['logiciel']);
  unset($_SESSION['copy']);
  unset($_SESSION['presta']);
  unset($_SESSION['license']);
  unset($_SESSION['urgence']);
  unset($_SESSION['structure']);
  unset($_SESSION['adherent']);
  for ($i = 0; isset($_SESSION["volume" . $i]); $i++) {
    unset($_SESSION["volume" . $i]);
    unset($_SESSION["temps" . $i]);
  }
  header('Location: /');
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Bordeaux Ecole Numérique - Abaque de préstation</title>
  <?php require_once('./header.php'); ?>
  <script src="./script.js"></script>
</head>

<body>
  <?php
  if (isset($_GET['total'])) {
    echo '<div class="total"><p>TOTAL : ';
    echo number_format($_GET['total'], 2, ',', ' ');
    echo '€</p></div>';
  }

  ?>
  <form action="./verify" method="post" id="theForm">
    <div class="selection">
      <div class="machine">
        <label for="machine">Machine : </label>
        <select required name="machine" id="machine">
          <?php
          if (isset($_SESSION['machine'])) {
            echo "<option selected value='" . $_SESSION['machine'] . "'>" . $_SESSION['machine'] . "</option>";
          } else {
            echo "<option selected disabled value='Sélectionner une machine'>--Sélectionner une machine--</option>";
          }

          $sql = "SELECT * FROM abq_machine";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<option value='" . $row["En_Mac_Nom"] . "'>" . $row["En_Mac_Nom"] . "</option>";
            }
          }
          ?>
        </select>
      </div>
      <div class="matiere">
        <label for="matiere">Matière : </label>
        <select required name="matiere" id="matiere">
          <?php
          if (isset($_SESSION['matiere'])) {
            echo "<option selected value='" . $_SESSION['matiere'] . "'>" . $_SESSION['matiere'] . "</option>";
          } else {
            echo "<option selected disabled value='Sélectionner une matière'>--Sélectionner une matière--</option>";
          }

          $sql = "SELECT * FROM abq_matiere";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<option value='" . $row["Nom_Mat"] . "'>" . $row["Nom_Mat"] . "</option>";
            }
          }
          ?>
        </select>
      </div>
      <div class="consommable">
        <label for="consommable">Consommable : </label>
        <select required name="consommable" id="consommable">
          <?php
          if (isset($_SESSION['consommable'])) {
            echo "<option selected value='" . $_SESSION['consommable'] . "'>" . $_SESSION['consommable'] . "</option>";
          } else {
            echo "<option selected disabled value='Sélectionner un consommable'>--Sélectionner un consommable--</option>";
          }

          $sql = "SELECT * FROM abq_consommable";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<option value='" . $row["En_Con_Nom"] . "'>" . $row["En_Con_Nom"] . "</option>";
            }
          }
          ?>
        </select>
      </div>
      <div class="logiciel">
        <label for="logiciel">Logiciel : </label>
        <select required name="logiciel" id="logiciel">
          <!-- <option selected disabled value="Sélectionner un logiciel">--Sélectionner un logiciel--</option> -->
          <?php
          $sql = "SELECT * FROM abq_logiciel";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              if ((isset($_SESSION['logiciel']) && $row["En_Logi_Nom"] == $_SESSION['logiciel']) || $row["En_Logi_Nom"] == "_Sans") {
                echo "<option selected value='" . $row["En_Logi_Nom"] . "'>" . $row["En_Logi_Nom"] . "</option>";
              } else {
                echo "<option value='" . $row["En_Logi_Nom"] . "'>" . $row["En_Logi_Nom"] . "</option>";
              }
            }
          }
          ?>
        </select>
      </div>
      <div class="copy">
        <label for="copy" id="copyLevel">Nombre de copie :</br>1</label>
        <?php
        if (isset($_SESSION['copy'])) {
          echo '<input type="range" name="copy" id="copy" min="1" value="' . $_SESSION['copy'] . '" max="100" orient="vertical" oninput="numberLevel()" />';
        } else {
          echo '<input type="range" name="copy" id="copy" min="1" value="1" max="100" orient="vertical" oninput="numberLevel()" />';
        }
        ?>
      </div>
      <div class="structure">
        <label for="structure">Structure : </label>
        <select required name="structure" id="structure">
          <!-- <option selected disabled value="Sélectionnez une structure">--Sélectionnez une structure--</option> -->
          <?php
          $structure_count = ["_Sans Mo", "1", "2", "3", "4", "5", "6"];
          foreach ($structure_count as $structure) {
            if ($admin == true) {
              if ((isset($_SESSION['structure']) && $structure == $_SESSION['structure']) || $structure == "4") {
                echo "<option selected value='" . $structure . "'>" . $structure . "</option>";
              } else {
                echo "<option value='" . $structure . "'>" . $structure . "</option>";
              }
            } else {
              if ($structure == 4) {
                echo "<option selected value='" . $structure . "'>" . $structure . "</option>";
              } else {
                echo "<option disabled value='" . $structure . "'>" . $structure . "</option>";
              }
            }
          }
          ?>
        </select>
      </div>
      <div class="adherent">
        <label for="adherent">Adhérent : </label>
        <?php
        if (isset($_SESSION['adherent']) && $_SESSION['adherent'] == "Adhérent") {
          echo '<input checked type="checkbox" name="adherent" id="adherent" value="Adhérent" />';
        } else {
          echo '<input type="checkbox" name="adherent" id="adherent" value="Adhérent" />';
        }
        ?>
      </div>

      <div class="presta">
        <label for="presta">Type de préstation : </label>
        <select required name="presta" id="presta">
          <!-- <option selected disabled value="Sélectionner un type de préstation">--Sélectionner un type de préstation--</option> -->
          <?php
          $sql = "SELECT * FROM abq_presta";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              if ((isset($_SESSION['presta']) && $row["Presta_Nom"] == $_SESSION['presta']) || $row["Presta_Nom"] == "One-Shoot") {
                echo "<option selected value='" . $row["Presta_Nom"] . "'>" . $row["Presta_Nom"] . "</option>";
              } else if ($admin == true) {
                echo "<option value='" . $row["Presta_Nom"] . "'>" . $row["Presta_Nom"] . "</option>";
              } else {
                echo "<option disabled value='" . $row["Presta_Nom"] . "'>" . $row["Presta_Nom"] . "</option>";
              }
            }
          }
          ?>
        </select>
      </div>

      <div class="license">
        <label for="license">Type de license : </label>
        <select required name="license" id="license">
          <!-- <option selected disabled value="Sélectionner un type de license">--Sélectionner un type de license--</option> -->
          <?php
          $license_count = ["CC-Zero", "CC-BY-SA", "CC-BY-NC-SA", "CC-BY-ND-SA", "CC-BY-NC-ND-SA", "CC-BY-NC-ND-DP-SA"];
          foreach ($license_count as $license) {
            if ((isset($_SESSION['license']) && $license == $_SESSION['license']) || $license == "CC-BY-SA") {
              echo "<option selected value='" . $license . "'>" . $license . "</option>";
            } else {
              echo "<option value='" . $license . "'>" . $license . "</option>";
            }
          }
          ?>
        </select>
      </div>

      <div class="urgence">
        <label for="urgence" id="urgenceLabel">Urgent : Non</label>
        <?php
        if (isset($_SESSION['urgence'])) {
          echo '<input type="range" name="urgence" id="urgence" min="0" max="2" value="' . $_SESSION['urgence'] . '" oninput="emergencyLevel()" />';
        } else {
          echo '<input type="range" name="urgence" id="urgence" min="0" max="2" value="1" oninput="emergencyLevel()" />';
        }
        ?>
      </div>
    </div>
    <div>
      <table>
        <tbody id="table">
          <tr>
            <th>Volumes en g | m² | ml</th>
            <th>Temps en minutes</th>
          </tr>
          <?php
          $i = 0;
          if (isset($_SESSION["volume0"]) && isset($_SESSION["temps0"])) {
            $flag = true;
            while ($flag == true) {
              if ($_SESSION["volume" . $i] == 0 && $_SESSION["temps" . $i] == 0) {
                $flag = false;
              } else {
                echo "<tr>";
                echo '<td><input type="number" name="volume' . $i . '" class="volume" value="' . $_SESSION["volume" . $i] . '" min="0" step="any"/></td>';
                echo '<td><input type="number" name="temps' . $i . '" class="temps" value="' . $_SESSION["temps" . $i] . '" min="0" step="any"/></td>';
                echo "</tr>";
                $i++;
              }
            }
          }
          ?>
          <tr>
            <td>
              <input type="number" name="volume<?php echo $i ?>" class="volume" value="0" min="0" oninput="inputed()" id="lastV" step="any" />
            </td>
            <td>
              <input type="number" name="temps<?php echo $i ?>" class="temps" value="0" min="0" oninput="inputed()" id="lastT" step="any" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="button">
      <input type="submit" value="Calculer" title="Calculer le prix pour cette préstation" />
      <br>
      <input type="button" value="Réinitialiser" onclick="resetAll()" title="Réinitialiser les informations remplies" />
      <br>
      <?php
      if ($admin == true) {
        echo "<input type='button' value='Déconnection' onclick='logout()' title='Déconnecter vous'/>";
      } else {
        echo '<input type="submit" value="Connection" title="Connecter vous" formaction="./login" />';
      }
      ?>
      <br>
      <input type="submit" value="Commentaires/Problèmes" title="Laisser un commentaire ou signaler un problème" formaction="./comment" formtarget="_blank" />

    </div>
  </form>
</body>

</html>

<?php $conn->close(); ?>