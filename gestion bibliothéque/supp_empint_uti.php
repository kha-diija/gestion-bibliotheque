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

// Gestion de la suppression d'un emprunt
if (isset($_GET['delete_user_id']) && isset($_GET['delete_ex_id'])) {
    $user_id = $_GET['delete_user_id'];
    $exemplaire_id = $_GET['delete_ex_id'];

    // Supprimer l'emprunt de la table emprunter
    $delete_sql = "DELETE FROM emprunter WHERE id_user = ? AND id_ex = ?";
    $stmt_delete = $conn->prepare($delete_sql);
    $stmt_delete->bind_param("ii", $user_id, $exemplaire_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Remettre le statut de l'exemplaire à "en rayon"
    $update_status_sql = "UPDATE exemp SET statut = 'en rayon' WHERE id_ex = ?";
    $stmt_update = $conn->prepare($update_status_sql);
    $stmt_update->bind_param("i", $exemplaire_id);
    $stmt_update->execute();
    $stmt_update->close();

    echo "<script>alert('Emprunt supprimé et exemplaire retourné en rayon.');</script>";
}

// Récupérer le mot-clé de recherche
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

// Requête SQL pour les emprunts avec filtre global
$emprunts_sql = "
    SELECT u.id_user, u.nom, u.prenom, ex.id_ex, doc.titre AS nom_document
    FROM emprunter e
    INNER JOIN user u ON e.id_user = u.id_user
    INNER JOIN exemp ex ON e.id_ex = ex.id_ex
    INNER JOIN doc doc ON ex.id_doc = doc.id_doc
    WHERE u.id_user LIKE ?
       OR CONCAT(u.nom, ' ', u.prenom) LIKE ?
       OR ex.id_ex LIKE ?
       OR doc.titre LIKE ?";

$stmt = $conn->prepare($emprunts_sql);
$search_term = "%$search_keyword%";
$stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
$stmt->execute();
$emprunts_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emprunts des utilisateurs</title>
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
            padding: 20px;
        }

        h2 {
            color: #8e24aa;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .search-bar input[type="text"] {
            width: 80%;
            padding: 10px;
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

        .btn {
            color: white;
            background-color: #d9534f;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Liste des Emprunts des Utilisateurs</h2>

    <!-- Barre de recherche globale -->
    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Rechercher dans toutes les colonnes" value="<?php echo htmlspecialchars($search_keyword); ?>">
        <button type="submit">Rechercher</button>
    </form>

    <?php
    // Afficher les résultats des emprunts
    if ($emprunts_result && $emprunts_result->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>ID Utilisateur</th><th>Nom de l'utilisateur</th><th>ID Exemplaire</th><th>Nom Exemplaire</th><th>Action</th></tr></thead>";
        echo "<tbody>";

        while ($emprunt = $emprunts_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($emprunt['id_user']) . "</td>";
            echo "<td>" . htmlspecialchars($emprunt['nom']) . " " . htmlspecialchars($emprunt['prenom']) . "</td>";
            echo "<td>" . htmlspecialchars($emprunt['id_ex']) . "</td>";
            echo "<td>" . htmlspecialchars($emprunt['nom_document']) . "</td>";
            echo "<td><a href='?delete_user_id=" . $emprunt['id_user'] . "&delete_ex_id=" . $emprunt['id_ex'] . "' class='btn'>Retourner</a></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Aucun emprunt trouvé.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>

</body>
</html>
