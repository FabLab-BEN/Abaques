<!DOCTYPE html>
<html>

<head>
    <title>Bordeaux Ecole Numérique - Abaque de préstation - Login</title>
    <?php require_once('./header.php'); ?>
</head>

<?php
// require("log.php");
session_start();
// check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // logRegistry("Already logged in as " . $_SESSION["name"]);
    header("location: /");
    exit;
}

// Connect to the database
require_once '3db827cf9c82849e206f3caf038a8b6e71b55bf2.php';

// Check if the form has been submitted
if (isset($_POST['loginPseudo'], $_POST['loginPassword'])) {
    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    if ($stmt = $conn->prepare('SELECT id, password FROM user WHERE pseudo = ?')) {
        // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
        $stmt->bind_param('s', $_POST['loginPseudo']);
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $password);
            $stmt->fetch();
            // Account exists, now we verify the password.
            // Note: remember to use password_hash in your registration file to store the hashed passwords.
            if (password_verify($_POST['loginPassword'], $password)) {
                // Verification success! User has loggedin!
                // Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['loginPseudo'];
                $_SESSION['id'] = $id;
                // logRegistry("Logged in as " . $_SESSION["name"]);
                header('Location: /');
            } else {
                // Incorrect password
                // logRegistry("Incorrect password for login");
                echo 'Incorrect username and/or password!';
            }
        } else {
            // Incorrect username
            // logRegistry("Incorrect username for login");
            echo 'Incorrect username and/or password!';
        }

        $stmt->close();
    }
}
?>

<!-- ! form connexion -->
<form action="./login" method="post" id="loginForm">
    <div class="login">
        <div class="loginContent">
            <div class="loginInput">
                <label for="loginPseudo">Nom d'utilisateur : </label>
                <input type="text" name="loginPseudo" id="loginPseudo" required autofocus />
                <br>
                <br>
            </div>
            <div class="loginInput">
                <label for="loginPassword">Mot de passe : </label>
                <input type="password" name="loginPassword" id="loginPassword" required />
                <br>
            </div>
        </div>
        <div class="loginButton">
            <input id="loginButton" type="submit" value="submit" />
        </div>
    </div>
</form>
</body>

</html>