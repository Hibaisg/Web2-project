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

// Vérifie si l'ID du projet est fourni via le paramètre GET
if(isset($_GET['id_projet'])) {
    $id_projet = $_GET['id_projet'];
    
    // Requête pour obtenir les détails du projet
    $sql_project = "SELECT titre, description, nombre_actions_a_vendre, prix_action
                    FROM projet
                    WHERE id_projet = $id_projet";
    
    $result_project = $conn->query($sql_project);
    
    if ($result_project->num_rows > 0) {
        $project = $result_project->fetch_assoc();
    } else {
        // Si le projet n'est pas trouvé, redirige vers une page d'erreur ou gère de manière appropriée
        echo "Project not found!";
        exit;
    }
} else {
    // Si l'ID du projet n'est pas fourni, redirige vers une page d'erreur ou gère de manière appropriée
    echo "Project ID not provided!";
    exit;
}

// Vérifie si le formulaire est soumis via la méthode POST et si la quantité est définie
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity'])) {
    // Assainit l'entrée
    $quantity = intval($_POST['quantity']);
    
    // Valide l'entrée
    if ($quantity > 0 && $quantity <= $project['nombre_actions_a_vendre']) {
        // Requête d'insertion pour ajouter l'achat d'actions dans la table capital_risque_projet
        $id_capital_risque = $_SESSION['id_capital_risque'];
        $sql_insert = "INSERT INTO capital_risque_projet (id_projet, id_capital_risque, nombre_actions_achetees)
                       VALUES ($id_projet, $id_capital_risque, $quantity)";
        
        if ($conn->query($sql_insert) === TRUE) {
            // Mettre à jour le nombre d'actions disponibles et vendues dans la table projet
            $sql_update = "UPDATE projet 
                           SET nombre_actions_a_vendre = nombre_actions_a_vendre - $quantity,
                               nombre_actions_vendues = nombre_actions_vendues + $quantity
                           WHERE id_projet = $id_projet";
            
            if ($conn->query($sql_update) === TRUE) {
                // Recharger la page après l'achat réussi
                header("Location: cap_acheter.php?id_projet=$id_projet");
                exit;
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo "Error inserting record: " . $conn->error;
        }
    } else {
        // Quantité non valide
        echo "Invalid quantity!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Capital Risque Dashboard</title>
    <style>
        body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: url('https://static.vecteezy.com/system/resources/previews/001/937/781/non_2x/business-startup-launching-product-with-rocket-concept-template-and-backgrounds-illustration-business-project-startup-process-idea-through-planning-and-strategy-time-management-vector.jpg') no-repeat;
        overflow: hidden;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color:transparent;
            backdrop-filter: blur(20px);
            border:2px solid rgba(255, 255, 255, .2);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #198dbffd;
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
        .titre{
            color: #198dbffd;
        }
        button[type="submit"] {
            background-color: #4dc7fce0; /* Background color */
            color: #fff;
            padding: 16px 32px; /* Increase padding for bigger button */
            border: 1px solid #4dc7fce0;
            border-radius: 50px;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            box-shadow: 3px 5px 115px white; /* White shadow on hover */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin: 1rem auto; /* Center the button horizontally */
            display: block; /* Ensure the button is a block element */
        }

        button[type="submit"]:hover {
            transform: translateY(-5px); /* Move button up slightly on hover */
        }
    </style>
</head>
<body>
<div class="sidebar">
            <ul>
                <li><a href="cap_à_financer.php">Projets à financer</a></li>
                <li><a href="cap_financés.php">Projets financés</a></li>
                <li><a href="s'authentifier_C.php">Déconnexion</a></li>
            </ul>
    </div>
    <div class="container">
        <h1>Acheter des actions</h1>
        <table class="table">
            <tbody>
                <tr>
                    <th>Projet</th>
                    <td><?php echo $project['titre']; ?></h2></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?php echo $project['description']; ?></td>
                </tr>
                <tr>
                    <th>Nombre d'actions à vendre</th>
                    <td><?php echo $project['nombre_actions_a_vendre']; ?></td>
                </tr>
                <tr>
                    <th>Prix de l'action</th>
                    <td><?php echo $project['prix_action']; ?></td>
                </tr>
            </tbody>
        </table>
        <form method="post">
            <div class="form-group">
                <label class="titre" for="quantity">Nombre d'actions à acheter:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?php echo $project['nombre_actions_a_vendre']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Acheter</button>
        </form>
    </div>
</body>
</html>


