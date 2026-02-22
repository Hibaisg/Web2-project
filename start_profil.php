<?php
session_start();

$id_startuper = $_SESSION['id_startuper'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projetpweb2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update profile photo if a new one is uploaded
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = file_get_contents($_FILES['photo']['tmp_name']);
        $photo_update_query = "UPDATE startuper SET photo=? WHERE id_startuper=?";
        $stmt = $conn->prepare($photo_update_query);
        $stmt->bind_param("bi", $photo, $id_startuper);
        $stmt->send_long_data(0, $photo);
        $stmt->execute();
        $stmt->close();
    }

    // Update profile infos :
    $updated_nom = $_POST['nom'];
    $updated_prenom = $_POST['prenom'];
    $updated_email = $_POST['email'];
    $updated_cin = $_POST['cin'];
    $updated_company_name = $_POST['nom_entreprise'];
    $updated_company_address = $_POST['adresse_entreprise'];
    $updated_commerce_register = $_POST['numero_registre_commerce'];
    $updated_pseudo = $_POST['pseudo'];
    $updated_password = $_POST['mot_de_passe'];

    // Update profile
    $update_query = "UPDATE startuper SET nom=?, prenom=?, email=?, CIN=?, nom_entreprise=?, adresse_entreprise=?, numero_registre_commerce=?, pseudo=?, pwrd=? WHERE id_startuper=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssssssi", $updated_nom, $updated_prenom, $updated_email, $updated_cin, $updated_company_name, $updated_company_address, $updated_commerce_register, $updated_pseudo, $updated_password, $id_startuper);
    if ($stmt->execute()) {
        $successMessage = "Profile updated successfully!";
    } else {
        $errorMessage = "Error updating profile: " . $conn->error;
    }
    $stmt->close();
}

$sql_startuper = "SELECT nom, prenom, email, CIN, nom_entreprise, adresse_entreprise, numero_registre_commerce, pseudo, pwrd, photo FROM startuper WHERE id_startuper='$id_startuper'";
$result_startuper = $conn->query($sql_startuper);

if ($result_startuper->num_rows > 0) {
    $row = $result_startuper->fetch_assoc();
    $nom = $row['nom']; 
    $prenom = $row['prenom'];
    $email = $row['email'];
    $cin = $row['CIN'];
    $nom_entreprise = $row['nom_entreprise'];
    $adresse_entreprise = $row['adresse_entreprise'];
    $numero_registre_commerce = $row['numero_registre_commerce'];
    $pseudo = $row['pseudo'];
    $pwrd = $row['pwrd'];
    $photo = $row['photo'];
} else {
    echo "Startuper not found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inscription.css">
    <title>Inscription</title>
    <style>
        .sidebar {
    border:2px solid rgba(255, 255, 255, .2);
    position: fixed;
    left: 0;
    top: 0;
    width: 200px;
    height: 100%;
    background-color:transparent; /* Fonds semi-transparent */
    color: white;
    padding: 20px;
    backdrop-filter: blur(300px); /* Ajoute un flou au fond */
    z-index: 2; /* Assurez-vous que la barre latérale est au-dessus du contenu */
    transition: background-color 0.3s; /* Ajoute une transition douce pour le changement de couleur de fond */
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    margin-bottom: 10px;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    transition: color 0.3s; /* Ajoute une transition douce pour le changement de couleur de texte */
}

.sidebar ul li a:hover {
    color: #4dc7fce0; /* Change la couleur du texte au survol */
}
    </style>
</head>
<body>
<div class="sidebar">
            <ul>
                <li><a href="start_ajout.php">Ajouter les projets</a></li>
                <li><a href="start_lister.php">Lister les projets</a></li>
                <li><a href="start_profil.php">Éditer le profil</a></li>
                <li><a href="s'authentifier_S.php">Déconnexion</a></li>
            </ul>
        </div>
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
                            <div id="forLogin" class="form-header active">Editer profil</div>
                        </div>
                        <div class="card-body" id="formContainer">
                            <form id="editForm" action="start_profil.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id_startuper" value="<?php echo $id_startuper; ?>">
                                <input type="text" class="form-control" placeholder="Nom" name="nom" value="<?php echo $nom; ?>" required>
                                <input type="text" class="form-control" placeholder="Prénom" name="prenom" value="<?php echo $prenom; ?>" required>
                                <input type="text" class="form-control" placeholder="Numéro CIN (8 chiffres)" name="cin" value="<?php echo $cin; ?>" required pattern="[0-9]{8}">
                                <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo $email; ?>" required>
                                <input type="text" class="form-control" placeholder="Nom de l'entreprise" name="nom_entreprise" value="<?php echo $nom_entreprise; ?>" required>
                                <input type="text" class="form-control" placeholder="Adresse de l'entreprise" name="adresse_entreprise" value="<?php echo $adresse_entreprise; ?>" required>
                                <input type="text" class="form-control" placeholder="Numéro du registre de commerce (10 chiffres)" name="numero_registre_commerce" value="<?php echo $numero_registre_commerce; ?>" required pattern="[A-Z][0-9]{10}">
                                <input type="file" class="form-control" name="photo">
                                <input type="text" class="form-control" placeholder="Pseudo" name="pseudo" value="<?php echo $pseudo; ?>" required>
                                <input type="password" class="form-control" placeholder="Mot de passe (au moins 8 caractères,fini par $ ou #)" name="mot_de_passe" value="<?php echo $pwrd; ?>" required pattern="^(?=.*[A-Za-z])(?=.*[$#])[A-Za-z\d$#]{8,}$">
                                <button type="submit" class="formButton">Editer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        // JavaScript to confirm form submission
        document.getElementById("editForm").addEventListener("submit", function(event) {
            // Display confirmation dialog
            var confirmSubmit = confirm("Êtes-vous sûr de vouloir enregistrer les modifications ?");
            // If user cancels, prevent form submission
            if (!confirmSubmit) {
                event.preventDefault(); // Prevent default form submission
            }
        });
    </script>
</body>
</html>













