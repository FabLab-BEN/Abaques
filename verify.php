<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $admin = true;
} else {
    $admin = false;
}
foreach ($_POST as $key => $value) {
    echo "Field " . htmlspecialchars($key) . " is " . htmlspecialchars($value) . "<br>";
}

// Save selection in session
$_SESSION['machine'] = $_POST['machine'];
$_SESSION['matiere'] = $_POST['matiere'];
$_SESSION['consommable'] = $_POST['consommable'];
$_SESSION['logiciel'] = $_POST['logiciel'];
$_SESSION['copy'] = $_POST['copy'];
$_SESSION['presta'] = $_POST['presta'];
$_SESSION['license'] = $_POST['license'];
$_SESSION['urgence'] = $_POST['urgence'];
$_SESSION['structure'] = $_POST['structure'];
$_SESSION['adherent'] = $_POST['adherent'];

for ($i = 0; isset($_POST["volume" . $i]); $i++) {
    $_SESSION["volume" . $i] = $_POST["volume" . $i];
    $_SESSION["temps" . $i] = $_POST["temps" . $i];
}


if (!isset($_POST['machine']) || !isset($_POST['matiere']) || !isset($_POST['consommable']) || !isset($_POST['logiciel']) || !isset($_POST['copy']) || !isset($_POST['presta']) || !isset($_POST['license']) || !isset($_POST['urgence']) || !isset($_POST['structure'])) {
    //This page should not be accessed directly. Need to submit the form.
    echo "erreur; formulaire incompler!";
    header('Location: /?erreur=0');
    exit;
} else {
    settype($_POST['copy'], "integer");
    settype($_POST['urgence'], "integer");
    $machine = $_POST['machine'];
    $matiere = $_POST['matiere'];
    $consommable = $_POST['consommable'];
    $logiciel = $_POST['logiciel'];
    $copy = $_POST['copy'];
    if ($admin != true && $_POST["structure"] != 4) {
        echo "erreur; structure illégal!";
        header('Location: /?erreur=2');
        exit;
    }
    $structure = $_POST['structure'];
    if (!isset($_POST['adherent'])) {
        $adherent = 0;
    } else {
        $adherent = 1;
    }
    if ($admin != true && $_POST["presta"] != 'One-Shoot') {
        echo "erreur; prestation illégal!";
        header('Location: /?erreur=2');
        exit;
    }
    $presta = $_POST['presta'];
    $license = $_POST['license'];
    $urgence = $_POST['urgence'];
    $structure = $_POST['structure'];
    $urgence = $_POST['urgence'];
}

$i = 0;
$flag = false;
require_once('3db827cf9c82849e206f3caf038a8b6e71b55bf2.php');
while ($flag == false) {
    if (isset($_POST['volume' . $i], $_POST['temps' . $i])) {
        $volume[$i] = $_POST['volume' . $i];
        $temps[$i] = $_POST['temps' . $i];
    } else {
        $flag = true;
        $totalVolume = array_sum($volume);
        $totalTemps = array_sum($temps);
    }
    if (isset($temps[$i]) && $temps[$i] != 0) {
        $requete = $conn->prepare("SELECT En_Mac_Majo FROM abq_machine WHERE En_Mac_Nom = ?");
        $requete->bind_param("s", $machine);
        $requete->execute();
        $requete->bind_result($suplement[$i]);
        $requete->fetch();
        $requete->close();
        echo "<br> suplement : " . array_sum($suplement) .  "<br>" . count($temps);
    }
    $i++;
}

echo "<br>volume : " . $totalVolume . "<br>temps : " . $totalTemps . "<br>";

if ($totalVolume == 0 || $totalTemps == 0) {
    echo "erreur; aucun volume ou temps défini";
    header('Location: /?erreur=1');
    exit;
}

echo $machine . $matiere . $consommable . $logiciel . $copy . $structure . $adherent . $presta . $license . $urgence;

$requete = $conn->prepare("SELECT En_Mac_Temps FROM abq_machine WHERE En_Mac_Nom = ?");
$requete->bind_param("s", $machine);
$requete->execute();
$requete->bind_result($machineTemps);
$requete->fetch();
$requete->close();
$C36 = $totalTemps * $machineTemps;
echo "<br>C36 : " . $C36;

$requete = $conn->prepare("SELECT Prix_Mat FROM abq_matiere WHERE Nom_Mat = ?");
$requete->bind_param("s", $matiere);
$requete->execute();
$requete->bind_result($G6);
$requete->fetch();
$requete->close();
echo "<br>G6 : " . $G6;

$G11 = ($G6 * $totalVolume) * 1.1;
echo "<br>G11 : " . $G11;

echo "<br>adherent : " . $adherent . "<br>";
if (!$adherent == 0) {
    $requete = $conn->prepare("SELECT En_Mac_Adherant FROM abq_machine WHERE En_Mac_Nom = ?");
    $requete->bind_param("s", $machine);
    $requete->execute();
    $requete->bind_result($machineAdh);
    $requete->fetch();
    $requete->close();
    $G12 = $totalTemps * $machineAdh;
    echo "<br>G12 : " . $G12 . "=" . $machineAdh . "*" . $totalTemps;
} else {
    $requete = $conn->prepare("SELECT En_Mac_Non_Adherant FROM abq_machine WHERE En_Mac_Nom = ?");
    $requete->bind_param("s", $machine);
    $requete->execute();
    $requete->bind_result($machineNonAdh);
    $requete->fetch();
    $requete->close();
    $G12 =  $totalTemps * $machineNonAdh;
}
$G12 = $G12 / 60;
echo "<br>G12 : " . $G12;

$requete = $conn->prepare("SELECT En_Con_Cout FROM abq_consommable WHERE En_Con_Nom = ?");
$requete->bind_param("s", $consommable);
$requete->execute();
$requete->bind_result($consommableCout);
$requete->fetch();
$requete->close();
$G14 = $consommableCout * $totalTemps;
echo "<br>G14 : " . $G14;

$requete = $conn->prepare("SELECT Presta_Cout FROM abq_presta WHERE Presta_Nom = ?");
$requete->bind_param("s", $presta);
$requete->execute();
$requete->bind_result($prestaCout);
$requete->fetch();
$requete->close();
$G20 = $prestaCout;
echo "<br>G20 : " . $G20;

switch ($urgence) {
    case 0:
        $G26 = 1.15;
        break;
    case 1:
        $G26 = 1;
        break;
    case 2:
        $G26 = 1.9;
        break;
}
echo "<br>G26 : " . $G26;


$G28 = $G11;
echo "<br>G28 : " . $G28;

$G29 = ((array_sum($suplement) + $C36) / 60) * $G20;
echo "<br>suplement : G29 : " . $G29;

$G30 = $G12 + $G14;
echo "<br>G30 : " . $G30;

switch ($structure) {
    case 1:
        $structure = 0.15;
        break;
    case 2:
        $structure = 0.2;
        break;
    case 3:
        $structure = 0.3;
        break;
    case 4:
        $structure = 0.5;
        break;
    case 5:
        $structure = 0.8;
        break;
    case 6:
        $structure = 0.9;
        break;
    default:
        $structure = 0;
        break;
}

$G31 = ($G28 + $G29 + $G30) * $structure;
echo "<br>G31 : " . $G31;

switch ($license) {
    case 'CC-Zero':
        $license = -10;
        break;
    case 'CC-BY-SA':
        $license = 0;
        break;
    case 'CC-BY-NC-SA':
        $license = 10;
        break;
    case 'CC-BY-ND-SA':
        $license = 20;
        break;
    case 'CC-BY-NC-ND-SA':
        $license = 30;
        break;
    case 'CC-BY-NC-ND-DP-SA':
        $license = 50;
        break;
}

$G32 = ($license + $G31 + $G30 + $G29 + $G28) * $G26;
echo "<br>G32 : " . $G32;

$total = $copy * $G32;
echo '<br> TOTAL : ' . $total;
header('Location: /?total=' . $total);
