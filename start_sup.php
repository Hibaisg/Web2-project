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

// Check if the ID of the project to be deleted is provided via GET request
$id_projet = $_GET['id_projet'] ?? '';

// Check if the ID is not empty and numeric
if (!empty($id_projet) && is_numeric($id_projet)) {
    try {
        // Check if any actions from this project have been sold
        $stmt_check_actions = $connexion->prepare("SELECT nombre_actions_vendues FROM projet WHERE id_projet = :id_projet");
        $stmt_check_actions->bindParam(':id_projet', $id_projet);
        $stmt_check_actions->execute();
        $nombre_actions_vendues = $stmt_check_actions->fetchColumn();

        if ($nombre_actions_vendues == 0) {
            // No actions sold, proceed with deletion
            $stmt_delete = $connexion->prepare("DELETE FROM projet WHERE id_projet = :id_projet");
            $stmt_delete->bindParam(':id_projet', $id_projet);
            $stmt_delete->execute();
            $successMessage = "Projet supprimé avec succès";
        } else {
            $errorMessage = "Le projet ne peut pas être supprimé car des actions ont déjà été vendues.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Erreur lors de la suppression du projet : " . $e->getMessage();
    }
} else {
    $errorMessage = "ID de projet invalide";
}

// Redirect back to the project listing page after deletion
header("Location: start_lister.php");
exit;
?>


