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

// Vérification des paramètres de recherche
$id_user_search = isset($_GET['id_user']) ? $_GET['id_user'] : '';
$nom_prenom_search = isset($_GET['nom_prenom']) ? $_GET['nom_prenom'] : '';

// Requête SQL pour obtenir le nombre d'emprunts de chaque utilisateur avec des filtres
$emprunts_sql = "
    SELECT u.id_user, u.nom, u.prenom, COUNT(e.id_ex) AS nbr_emprunts
    FROM user u
    LEFT JOIN emprunter e ON u.id_user = e.id_user
    WHERE 1
";

// Ajout de conditions de filtre si des valeurs sont fournies
if ($id_user_search) {
    $emprunts_sql .= " AND u.id_user = " . intval($id_user_search);
}

if ($nom_prenom_search) {
    // Recherche par nom et prénom combinés
    $emprunts_sql .= " AND (CONCAT(u.nom, ' ', u.prenom) LIKE '%" . $conn->real_escape_string($nom_prenom_search) . "%')";
}

$emprunts_sql .= " GROUP BY u.id_user";

$emprunts_result = $conn->query($emprunts_sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nombre d'Emprunts par Utilisateur</title>
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

        .search-form {
            margin-bottom: 20px;
        }

        .search-form input {
            padding: 8px;
            margin-right: 10px;
        }

        .search-form button {
            padding: 8px 16px;
            background-color: #8e24aa;
            color: white;
            border: none;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #7b1fa2;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Nombre d'Emprunts par Utilisateur</h2>

    <!-- Formulaire de recherche -->
    <form class="search-form" method="GET" action="">
        <input type="text" name="id_user" placeholder="ID Utilisateur" value="<?= htmlspecialchars($id_user_search) ?>">
        <input type="text" name="nom_prenom" placeholder="Nom et Prénom de l'utilisateur" value="<?= htmlspecialchars($nom_prenom_search) ?>">
        <button type="submit">Rechercher</button>
    </form>

    <?php
    // Vérifier si des résultats sont présents
    if ($emprunts_result && $emprunts_result->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>ID Utilisateur</th><th>Nom de l'utilisateur</th><th>Nombre d'Emprunts</th></tr></thead>";
        echo "<tbody>";

        // Affichage des résultats dans un tableau
        while ($row = $emprunts_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id_user']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nom']) . " " . htmlspecialchars($row['prenom']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nbr_emprunts']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Aucun emprunt trouvé.</p>";
    }

    // Fermer la connexion
    $conn->close();
    ?>

</div>

</body>
</html>
