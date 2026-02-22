<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projetpweb2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id_capital_risque = $_SESSION['id_capital_risque'];

// Query to fetch financed projects
$sql_financed_projects = "SELECT p.titre, p.description, crp.nombre_actions_achetees, p.prix_action, (crp.nombre_actions_achetees * p.prix_action) AS investissement_total
                          FROM projet p
                          INNER JOIN capital_risque_projet crp ON p.id_projet = crp.id_projet
                          WHERE crp.id_capital_risque = $id_capital_risque";

$result_financed_projects = $conn->query($sql_financed_projects);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Projets financés</title>
</head>
<body>
    <div class="container">
        <h1>Projets financés</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre du projet</th>
                    <th>Description</th>
                    <th>Nombre d'actions achetées</th>
                    <th>Prix de l'action</th>
                    <th>Investissement total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_financed_projects->num_rows > 0) {
                    while ($row = $result_financed_projects->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row['titre']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['nombre_actions_achetees']}</td>
                            <td>{$row['prix_action']}</td>
                            <td>{$row['investissement_total']} €</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Aucun projet financé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

