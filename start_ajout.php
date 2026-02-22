<?php
$dsn = "mysql:host=localhost;dbname=projetpweb2";
$username = "root";
$password = "";

try {
    $connexion = new PDO($dsn, $username, $password);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("La connexion a échoué : " . $e->getMessage());
}

$titre = "";
$description = "";
$nombre_actions_a_vendre = "";
$prix_action = "";
$id_startuper = ""; // Added to store the authenticated user's ID

$errorMessage = "";
$successMessage = "";

// Check if user is authenticated
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['project-title'];
    $description = $_POST['project-description'];
    $nombre_actions_a_vendre = $_POST['num-actions'];
    $prix_action = $_POST['action-value'];
    $id_startuper = $_SESSION['id_startuper'] ?? '';

    try {
        $stmt = $connexion->prepare("INSERT INTO projet (titre, description, nombre_actions_a_vendre, prix_action, id_startuper) VALUES (:titre, :description, :nombre_actions_a_vendre, :prix_action, :id_startuper)");
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':nombre_actions_a_vendre', $nombre_actions_a_vendre);
        $stmt->bindParam(':prix_action', $prix_action);
        $stmt->bindParam(':id_startuper', $id_startuper); // Bind authenticated user's ID
        
        $stmt->execute();
        $successMessage = "Projet ajouté avec succès";

        // Clear the field values after successful submission
        $titre = "";
        $description = "";
        $nombre_actions_a_vendre = "";
        $prix_action = "";
    } catch (PDOException $e) {
        $errorMessage = "Erreur lors de l'ajout du projet : " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="start_ajout.css">
    <title>Startuper Dashboard</title>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <ul>
                <li><a href="start_ajout.php">Ajouter les projets</a></li>
                <li><a href="start_lister.php">Lister les projets</a></li>
                <li><a href="start_profil.php">Éditer le profil</a></li>
                <li><a href="s'authentifier_S.php">Déconnexion</a></li>
            </ul>
        </div>
        <h1>Startuper Dashboard</h1>
        <div class="project-form">
            <h2>Ajouter un nouveau projet</h2>
            <?php 
            if(!empty($errorMessage)){
                echo"
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                </div>
                ";
            }
            ?>
            
            <form action="start_ajout.php" method="post">
                <div class="form-group">
                    <label for="project-title">Titre du projet:</label>
                    <input type="text" id="project-title" name="project-title" value="<?php echo $titre; ?>" required>
                </div>
                <div class="form-group">
                    <label for="project-description">Description du projet:</label>
                    <textarea id="project-description" name="project-description" rows="4" required><?php echo $description; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="num-actions">Nombre d'actions à vendre:</label>
                    <input type="number" id="num-actions" name="num-actions" value="<?php echo $nombre_actions_a_vendre; ?>" required>
                </div>
                <div class="form-group">
                    <label for="action-value">Valeur monétaire de l'action:</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control" id="action-value" name="action-value" value="<?php echo $prix_action; ?>" aria-label="Montant (au dollar près)" required>
                        <span class="input-group-text">.00</span>
                    </div>
                </div>
                <?php 
            if(!empty($successMessage)){
                echo"
                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-6'>
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong>$successMessage</strong>
                        </div>
                    </div>
                </div>
                ";
            }
            ?>
                <button type="submit">
                    <i class="fa fa-plus"></i>  Ajouter le projet
                </button>
                
            </form>
        </div>
    </div>
</body>
</html> 


