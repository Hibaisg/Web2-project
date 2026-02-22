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

// Check if form is submitted via POST method and keyword is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['keyword'])) {
    // Sanitize the keyword input
    $keyword = $conn->real_escape_string($_POST['keyword']);
    
    // Query to search projects by keyword in description
    $sql_projects = "SELECT id_projet, titre, description, nombre_actions_a_vendre, prix_action
                     FROM projet
                     WHERE description LIKE '%$keyword%'";
} else {
    // Default query to fetch all projects
    $sql_projects = "SELECT id_projet, titre, description, nombre_actions_a_vendre, prix_action
                     FROM projet";
}

$result_projects = $conn->query($sql_projects);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .dashboard {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 70%;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
        }

        .search-bar button {
            padding: 10px 20px;
            background-color: #4dc7f0;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }

        .project-list {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
        }

        .project-item {
            border-bottom: 1px solid #ccc;
            padding: 10px;
        }

        .project-item:last-child {
            border-bottom: none;
        }

        .project-title {
            font-weight: bold;
        }

        .project-description {
            margin-top: 5px;
            color: #666;
        }

        .project-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .action-buttons {
            display: flex;
            align-items: center;
        }

        .action-buttons button {
            margin-right: 10px;
            padding: 8px 15px;
            background-color: #4dc7f0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .action-buttons button:last-child {
            margin-right: 0;
        }
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
        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            color: white; /* Set text color to white */
            background-image: url('https://static.vecteezy.com/system/resources/previews/001/937/727/original/business-startup-launching-product-with-rocket-concept-template-and-backgrounds-illustration-business-project-startup-process-idea-through-planning-and-strategy-time-management-vector.jpg') ; /* Add your background image URL */
            background-size: cover; /* Cover the entire viewport */
            background-position: center; /* Center the background image */
        }
        .project-list{
            background-color:transparent;
            backdrop-filter: blur(20px);
        }
    </style>
    <title>Capital Risque Dashboard</title>
</head>

<body>
    <div class="sidebar">
            <ul>
                <li><a href="cap_à_financer.php">Projets à financer</a></li>
                <li><a href="cap_financés.php">Projets financés</a></li>
                <li><a href="s'authentifier_C.php">Déconnexion</a></li>
            </ul>
    </div>
    <div class="dashboard">
        <h1>Capital Risque Dashboard</h1>
        <div class="search-bar">
    <form method="post" style="display: flex;">
        <input type="text" name="keyword" placeholder="Rechercher un projet..." style="flex: 1;">
        <button type="submit" style="margin-left: 10px;">Rechercher</button>
    </form>
</div>

        <div class="project-list">
            <!-- Project items will be dynamically added here -->
            <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Nombre d'actions à vendre</th>
                    <th>Prix de l'action</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_projects->num_rows > 0) {
                    while ($row = $result_projects->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row['titre']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['nombre_actions_a_vendre']}</td>
                            <td>{$row['prix_action']}</td>
                            <td>
                                <a class='btn btn-primary btn-sm' href='/Projetpweb2/cap_acheter.php?id_projet={$row['id_projet']}'>Editer</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Aucun projet trouvé.</td></tr>";
                }
                ?>
            </tbody>
            </table>
        </div>
    </div>
</body>
</html>



