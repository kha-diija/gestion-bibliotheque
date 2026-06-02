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

// Gestion de la recherche
$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Requête SQL pour récupérer les informations des utilisateurs, avec recherche
$sql = "SELECT id_user, nom, prenom, email, type, dateNaissance, ville FROM user WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($sql);
$search = "%" . $searchQuery . "%";
$stmt->bind_param("sss", $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
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

        .btn {
            color: white;
            background-color: #5cb85c;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #4cae4c;
        }

        .search-bar {
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
        }

        .search-bar input {
            padding: 8px;
            font-size: 16px;
            width: 30%;
        }

        .search-bar button {
            padding: 8px 16px;
            font-size: 16px;
            background-color: #8e24aa;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des Utilisateurs</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <form method="POST">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Rechercher par nom, prénom ou email...">
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Utilisateur</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Type</th>
                <th>Date de Naissance</th>
                <th>Ville</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id_user']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['dateNaissance']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ville']) . "</td>";
                    echo "<td><a href='exemplaires.php?user_id=" . $row['id_user'] . "' class='btn'>Emprunter</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Aucun utilisateur trouvé</td></tr>";
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
