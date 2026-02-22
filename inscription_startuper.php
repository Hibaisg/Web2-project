<?php
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

// Check if the form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['photo'])) {
    // Récupération des données fournies par l'utilisateur
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $nom_entreprise = $_POST['nom_entreprise'];
    $adresse_entreprise = $_POST['adresse_entreprise'];
    $numero_registre_commerce = $_POST['numero_registre_commerce'];

    // Vérifier si un fichier a été correctement uploadé
    if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo']['name'];
        // Chemin de destination pour l'enregistrement du fichier
        $upload_directory = "C:/wamp64/www/projetpweb2/";

        // Déplacer le fichier uploadé vers le répertoire souhaité
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_directory . $photo)) {
            // Fichier téléchargé avec succès, continuer le traitement

            // Insertion dans la base de données
            $pseudo = $_POST['pseudo'];
            $pwrd = $_POST['mot_de_passe'];

            // Check if a startuper with the same email or CIN already exists
            $requete_verif = "SELECT * FROM startuper WHERE pseudo=? OR cin=? OR email=?";
            $statement_verif = $connexion->prepare($requete_verif);
            $statement_verif->execute([$pseudo, $cin, $email]);


            // Vérifier si la requête a renvoyé des résultats
            if ($statement_verif->rowCount() > 0) {
                // Un startuper avec le même email ou CIN existe déjà
                echo "<script>alert('Un startuper avec cet pseudo, email ou ce CIN existe déjà.');</script>";
            } else {
                // Aucun startuper avec le même email ou CIN trouvé, procéder à l'inscription

                // Prepare the SQL query for insertion
                $stm = $connexion->prepare("INSERT INTO startuper (nom,prenom,CIN,email,nom_entreprise,adresse_entreprise,numero_registre_commerce,photo,pseudo,pwrd) VALUES (:nom,:prenom,:CIN,:email,:nom_entreprise,:adresse_entreprise,:numero_registre_commerce,:photo,:pseudo,:pwrd)");

                // Execute the query with parameters
                $stm->execute(array(
                    ":nom" => $nom,
                    ":prenom" => $prenom,
                    ":CIN" => $cin,
                    ":email" => $email,
                    ":nom_entreprise" => $nom_entreprise,
                    ":adresse_entreprise" => $adresse_entreprise,
                    ":numero_registre_commerce" => $numero_registre_commerce,
                    ":photo" => $photo,
                    ":pseudo" => $pseudo,
                    ":pwrd" => $pwrd
                ));

                // Display success message
                $message = "Startuper inscrit";

                // Redirect after successful registration
                header("Location: s'authentifier_S.php");
                exit; // Assure that the script stops after redirection
            }
        } else {
            echo "Erreur lors du téléchargement du fichier.";
        }
    } else {
        echo "Erreur: Aucun fichier sélectionné ou erreur lors du téléchargement.";
    }
}
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
                            <input type="file" class="form-control" placeholder="Photo d'identité" name="photo" required>
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
