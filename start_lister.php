<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lister les projets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="dashboard">
    <div class="container my-5">
        <h2>Liste de projets</h2>
        <a class="btn btn-primary" href="/Projetpweb2/start_ajout.php" role="button">Nouveau projet</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>ID projet</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Nombre d'actions à vendre</th>
                    <th>Nombre d'actions vendues</th>
                    <th>Prix de l'action</th>
                    <th>ID startuper</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "projetpweb2";
                $connection = new mysqli($servername, $username, $password, $database);
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }
                $sql = "SELECT * FROM projet";
                $result = $connection->query($sql);
                if(!$result){
                    die("invalid query:". $connection->error);
                }
                while($row = $result->fetch_assoc()){
                    echo"
                    <tr>
                    <td>$row[id_projet]</td>
                    <td>$row[titre]</td>
                    <td>$row[description]</td>
                    <td>$row[nombre_actions_a_vendre]</td>
                    <td>$row[nombre_actions_vendues]</td>
                    <td>$row[prix_action]</td>
                    <td>$row[id_startuper]</td>
                    <td>
                        <a class='btn btn-primary btn-sm' href='/Projetpweb2/start_editer.php?id_projet=$row[id_projet]'>Editer</a>
                        <a class='btn btn-danger btn-sm' href='/Projetpweb2/start_sup.php?id_projet=$row[id_projet]'>Supprimer</a>
                    </td>
                </tr>
                    ";
                }
                ?>

            
            </tbody>
        </table>
</body>
</html>