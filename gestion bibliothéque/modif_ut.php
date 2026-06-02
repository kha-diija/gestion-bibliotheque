<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'mylast_biblio';

$conn = new mysqli($host, $user, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Initialisation de la variable de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Requête SQL avec filtre de recherche
$sql = "SELECT id_user, nom, prenom, dateNaissance, ville, email, type, interdit 
        FROM user 
        WHERE CONCAT(id_user, nom, prenom, dateNaissance, ville, email, type) 
        LIKE '%$search%'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <style>
        /* Styles pour la page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: #8e24aa;
            text-align: center;
        }

        .search-bar {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-bar input[type="text"] {
            width: 60%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .search-bar button {
            padding: 10px 15px;
            background-color: #8e24aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .search-bar button:hover {
            background-color: #7b1fa2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #8e24aa;
            color: white;
        }

        .btn-modifier {
            padding: 5px 10px;
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-modifier:hover {
            background-color: #fb8c00;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des Utilisateurs</h1>

    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Utilisateur</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de Naissance</th>
                <th>Ville</th>
                <th>Email</th>
                <th>Type</th>
                <th>Interdit</th>
                <th>Modifier</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                // Affichage des utilisateurs dans le tableau
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id_user']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['dateNaissance']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ville']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . ($row['interdit'] == 1 ? 'Oui' : 'Non') . "</td>";
                    echo "<td><a href='modifier.php?id=" . $row['id_user'] . "' class='btn-modifier'>Modifier</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Aucun utilisateur trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Fermer la connexion
$conn->close();
?>
