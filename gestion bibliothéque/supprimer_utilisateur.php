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

// Vérifier si une suppression a été demandée
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Requête SQL pour supprimer l'utilisateur
    $delete_sql = "DELETE FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Utilisateur supprimé avec succès');</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression de l\'utilisateur');</script>";
    }

    $stmt->close();
}

// Récupération du filtre de recherche global
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

// Requête SQL avec filtre global
$sql = "SELECT id_user, nom, prenom, dateNaissance, ville, email, type 
        FROM user 
        WHERE id_user LIKE ? 
           OR nom LIKE ? 
           OR prenom LIKE ? 
           OR dateNaissance LIKE ? 
           OR ville LIKE ? 
           OR email LIKE ? 
           OR type LIKE ?";
$stmt = $conn->prepare($sql);
$search_term = "%$search_keyword%";
$stmt->bind_param("sssssss", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 10px;
        }

        h1 {
            color: #8e24aa;
            text-align: center;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .search-bar input[type="text"] {
            padding: 8px;
            width: 80%;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
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

        .delete-btn {
            color: white;
            background-color: red;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .delete-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des Utilisateurs</h1>

    <!-- Barre de recherche globale -->
    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Rechercher dans toutes les colonnes" value="<?php echo htmlspecialchars($search_keyword); ?>">
        <button type="submit">Rechercher</button>
    </form>

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
                <th>Action</th>
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
                    echo "<td><a href='?delete_id=" . $row['id_user'] . "' class='delete-btn' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur ?\");'>Supprimer</a></td>";
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
$stmt->close();
$conn->close();
?>
