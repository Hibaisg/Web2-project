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

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_projet = $_POST['id_projet'];
    $titre = $_POST['project-title'];
    $description = $_POST['project-description'];
    $nombre_actions_a_vendre = $_POST['num-actions'];
    $prix_action = $_POST['action-value'];

    try {
        $stmt = $connexion->prepare("UPDATE projet SET titre = :titre, description = :description, nombre_actions_a_vendre = :nombre_actions_a_vendre, prix_action = :prix_action WHERE id_projet = :id_projet");
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':nombre_actions_a_vendre', $nombre_actions_a_vendre);
        $stmt->bindParam(':prix_action', $prix_action);
        $stmt->bindParam(':id_projet', $id_projet);
        
        $stmt->execute();
        $successMessage = "Projet modifié avec succès";
        // Redirect to start_lister.php after successful editing
        header("Location: start_lister.php");
        exit();
    } catch (PDOException $e) {
        $errorMessage = "Erreur lors de la modification du projet : " . $e->getMessage();
    }
}

// Récupérer l'ID du projet à éditer depuis l'URL
$id_projet = $_GET['id_projet'] ?? '';

// Récupérer les données du projet à éditer depuis la base de données
try {
    $stmt = $connexion->prepare("SELECT * FROM projet WHERE id_projet = :id_projet");
    $stmt->bindParam(':id_projet', $id_projet);
    $stmt->execute();
    $projet = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le projet existe
    if (!$projet) {
        die("Le projet avec l'ID $id_projet n'existe pas.");
    }

    // Extraire les données du projet
    $titre = $projet['titre'];
    $description = $projet['description'];
    $nombre_actions_a_vendre = $projet['nombre_actions_a_vendre'];
    $prix_action = $projet['prix_action'];
} catch (PDOException $e) {
    die("Erreur lors de la récupération du projet : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="start_ajout.css">
    <title>Éditer un projet</title>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <ul>
                <li><a href="start_ajout.php">Ajouter les projets</a></li>
                <li><a href="start_lister.php">Lister les projets</a></li>
                <li><a href="start_profil.php">Éditer le profil</a></li>
            </ul>
        </div>
        <h1>Startuper Dashboard</h1>
        <div class="project-form">
            <h2>Éditer le projet</h2>
            <?php 
            if(!empty($errorMessage)){
                echo"
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                </div>
                ";
            }
            ?>
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
            <form action="start_editer.php?id_projet=<?php echo $id_projet; ?>" method="post">
                <input type="hidden" name="id_projet" value="<?php echo $id_projet; ?>">
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
                <button type="submit" id="submitBtn">
                    <i class="fa fa-save"></i> Enregistrer les modifications
                </button>
            </form>
        </div>
    </div>
    <script>
        // JavaScript to confirm form submission
        document.getElementById("submitBtn").addEventListener("click", function(event) {
            // Display confirmation dialog
            var confirmSubmit = confirm("Êtes-vous sûr de vouloir enregistrer les modifications ?");
            // If user confirms, allow form submission
            if (!confirmSubmit) {
                event.preventDefault(); // Prevent default form submission
            }
        });
    </script>
</body>
</html>




