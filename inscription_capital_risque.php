<?php
// Connexion à la base de données avec PDO
$dsn = "mysql:host=localhost;dbname=projetpweb2";
$username = "root";
$password = "";

try {
    $connexion = new PDO($dsn, $username, $password);
    // Définir le mode d'erreur de PDO sur Exception
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("La connexion a échoué : " . $e->getMessage());
}

// Récupération des données du formulaire si la méthode est POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $cin = $_POST['cin'];
    $pseudo = $_POST['pseudo'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Préparer la requête SQL pour vérifier si le capital risque existe déjà
    $requete_verif = "SELECT * FROM capital_risque WHERE pseudo=? OR cin=? OR email=?";
    $statement_verif = $connexion->prepare($requete_verif);
    $statement_verif->execute([$pseudo, $cin, $email]);


    // Vérifier si la requête a renvoyé des résultats
    if ($statement_verif->rowCount() > 0) {
        // Le capital risque existe déjà
        echo "<script>alert('Un capital risque avec ce pseudo, cin ou cette adresse e-mail existe déjà.');</script>";
    } else {
        // Insertion des données du capital risque dans la base de données
        $requete_insertion = "INSERT INTO capital_risque (nom, prenom, email, CIN, pseudo, pwrd) VALUES (?, ?, ?, ?, ?, ?)";
        $statement_insertion = $connexion->prepare($requete_insertion);
        $resultat_insertion = $statement_insertion->execute([$nom, $prenom, $email, $cin, $pseudo, $mot_de_passe]);

        if ($resultat_insertion) {
            echo "<script>alert('Inscription réussie !');";
            // Rediriger vers la page de connexion
            header("Location: s'authentifier_C.php");
            exit(); // Terminer le script après la redirection
        } else {
            echo "<script>alert('Erreur lors de l'inscription.');</script>";
        }
    }
}

// Fermer la connexion PDO
$connexion = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inscription.css">
    <title>inscription</title>
</head>
<body>
    <div class="container">
    <?php 
                if(!empty($message)){
                    echo"
                    <div class='row mb-3'>
                    <strong>".$message."</strong>
                    </div>
                    ";
                }
                ?>
        <section>
        <div class="sec-container">
            <div class="form-wrapper">
                <div class="card">
                    <div class="card-header">
                        <div id="forLogin" class="form-header active">Startuper</div>
                        <div id="forRegister" class="form-header">Capital risque</div>
                    </div>
                    <div class="card-body" id="formContainer">
                        <form id="formLogin" action="inscription_startuper.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm('formLogin')">
                            <input type="text" class="form-control" placeholder="Nom" name="nom" required>
                            <input type="text" class="form-control" placeholder="Prénom" name="prenom" required>
                            <input type="text" class="form-control" placeholder="Numéro CIN (8 chiffres)" name="cin" required pattern="[0-9]{8}">
                            <input type="email" class="form-control" placeholder="Email" name="email" required>
                            <input type="text" class="form-control" placeholder="Nom de l'entreprise" name="nom_entreprise" required>
                            <input type="text" class="form-control" placeholder="Adresse de l'entreprise" name="adresse_entreprise" required>
                            <input type="text" class="form-control" placeholder="Numéro du registre de commerce (10 chiffres)" name="numero_registre_commerce" required pattern="[A-Z][0-9]{10}">
                            <input type="file" class="form-control" placeholder="Photo d'identité" name="photo" >
                            <input type="text" class="form-control" placeholder="Pseudo" name="pseudo" required>
                            <input type="password" class="form-control" placeholder="Mot de passe (au moins 8 caractères,fini par $ ou #)" name="mot_de_passe" required pattern="^(?=.*[A-Za-z])(?=.*[$#])[A-Za-z\d$#]{8,}$">
                            <button type="submit" class="formButton">inscription</button>
                            <div class="login-link">
                                <p>Vous avez déjà un compte ? <a href="s'authentifier_S.php">S'authentifier</a></p>
                            </div>
                        </form>
                        <form id="formRegister" class="toggleForm" action="inscription_capital_risque.php" method="post" onsubmit="return validateForm('formRegister')">
                            <input type="text" class="form-control" placeholder="Nom" name="nom" required>
                            <input type="text" class="form-control" placeholder="Prénom" name="prenom" required>
                            <input type="email" class="form-control" placeholder="Email" name="email" required>
                            <input type="text" class="form-control" placeholder="Numéro CIN (8 chiffres)" name="cin" required pattern="[0-9]{8}">
                            <input type="text" class="form-control" placeholder="Pseudo" name="pseudo" required>
                            <input type="password" class="form-control" placeholder="Mot de passe (au moins 8 caractères,fini par $ ou #)" name="mot_de_passe" required pattern="^(?=.*[A-Za-z])(?=.*[$#])[A-Za-z\d$#]{8,}$">
                            <button type="submit" class="formButton">Inscription</button>
                            <div class="login-link">
                                <p>Vous avez déjà un compte ? <a href="s'authentifier_C.php">S'authentifier</a></p>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
        </section>
    </div>
    <script>
        const forLogin = _('forLogin');
const loginForm = _('formLogin'); // Added this line
const forRegister = _('forRegister');
const registerForm = _('formRegister'); // Added this line
const formContainer = _('formContainer');

forRegister.addEventListener('click', () => {
    forLogin.classList.remove('active'); // Corrected typo
    forRegister.classList.add('active');
    if (registerForm.classList.contains('toggleForm')) {
        formContainer.style.transform = 'translate(-100%)'; // Corrected typo
        formContainer.style.transition = 'transform .5s';
        registerForm.classList.remove('toggleForm');
        loginForm.classList.add('toggleForm'); // Corrected typo
    }
});
forLogin.addEventListener('click', () => { // Added event listener for "Startuper" button
    forRegister.classList.remove('active');
    forLogin.classList.add('active');
    if (loginForm.classList.contains('toggleForm')) {
        formContainer.style.transform = 'translate(0%)'; // Translate back to original position
        formContainer.style.transition = 'transform .5s';
        loginForm.classList.remove('toggleForm');
        registerForm.classList.add('toggleForm');
    }
});

function _(e) {
    return document.getElementById(e);
}
function validateForm(formId) {
            var form = document.getElementById(formId);
            var inputs = form.querySelectorAll('input');
            for (var i = 0; i < inputs.length; i++) {
                if (!inputs[i].checkValidity()) {
                    // Afficher un message d'erreur ou une indication à l'utilisateur
                    alert("Veuillez remplir correctement tous les champs.");
                    return false;
                }
            }
            return true;
        }
    </script>
</body>
</html>



