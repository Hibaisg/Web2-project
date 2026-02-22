<?php
// Connection to the database using PDO
$dsn = "mysql:host=localhost;dbname=projetpweb2";
$username = "root";
$password = "";

try {
    $connexion = new PDO($dsn, $username, $password);
    // Set PDO error mode to exception
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $pseudo = $_POST['pseudo'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Authenticate user
    $requete_authentification = "SELECT id_startuper FROM startuper WHERE pseudo=? AND pwrd=?";
    $statement_authentification = $connexion->prepare($requete_authentification);
    $statement_authentification->execute([$pseudo, $mot_de_passe]);

    if ($statement_authentification->rowCount() > 0) {
        // Fetch authenticated user's ID
        $row = $statement_authentification->fetch(PDO::FETCH_ASSOC);
        $id_startuper = $row['id_startuper'];

        // Store authenticated user's ID in the session
        session_start();
        $_SESSION['id_startuper'] = $id_startuper;

        // Redirect to start_ajout.php if authentication succeeds
        header("Location: start_ajout.php");
        exit;
    } else {
        // Authentication failed, show an alert
        echo "<script>alert('Pseudo ou mot de passe incorrect.');</script>";
    }
}


// Close the PDO connection
$connexion = null;
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
    <link rel="stylesheet" href="s'authentifier.css">
</head>

<body>

    <div class="login-light"></div>
    <div class="login-box">
        <form action="s'authentifier_S.php" method="post">
            <input type="checkbox" class="input-check" id="input-check">
            <label for="input-check" class="toggle">
                <span class="text off">off</span>
                <span class="text on">on</span>
            </label>
            <div class="light"></div>
            <h2>S'authentifier</h2>
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="person"></ion-icon> <!-- Utiliser une icône pour le pseudo -->
                </span>
                <input type="text" name="pseudo" required> <!-- Changer le type en "text" pour le pseudo -->
                <label>Pseudo</label> <!-- Modifier le label en conséquence -->
                <div class="input-line"></div>
            </div>
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="lock-closed"></ion-icon>
                </span>
                <input type="password" name="mot_de_passe" required>
                <label>Mot de passe</label>
                <div class="input-line"></div>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox"> Souvenez de moi</label>
                <a href="#">Mot de passe oublié ?</a>
            </div>
            <button type="submit">Login</button>
            <div class="register-link">
                <p><a href="s'authentifier_C.php">S'authentifier comme Capital risque</a></p>
                <p>Vous n’avez pas de compte ? <a href="inscription_startuper.php">S'inscrire</a></p>
            </div>
        </form>
    </div>


    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
