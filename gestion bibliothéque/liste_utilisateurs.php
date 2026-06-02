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

// Initialisation des variables de recherche pour chaque colonne
$search_nom = isset($_GET['search_nom']) ? $_GET['search_nom'] : '';
$search_prenom = isset($_GET['search_prenom']) ? $_GET['search_prenom'] : '';
$search_dateNaissance = isset($_GET['search_dateNaissance']) ? $_GET['search_dateNaissance'] : '';
$search_ville = isset($_GET['search_ville']) ? $_GET['search_ville'] : '';
$search_email = isset($_GET['search_email']) ? $_GET['search_email'] : '';
$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : '';

// Construction dynamique de la requête SQL
$sql = "SELECT id_user, nom, prenom, dateNaissance, ville, email, type 
        FROM user 
        WHERE 1=1";

// Appliquer les filtres uniquement si les champs sont remplis
if (!empty($search_nom)) {
    $sql .= " AND nom LIKE '%$search_nom%'";
}
if (!empty($search_prenom)) {
    $sql .= " AND prenom LIKE '%$search_prenom%'";
}
if (!empty($search_dateNaissance)) {
    $sql .= " AND dateNaissance LIKE '%$search_dateNaissance%'";
}
if (!empty($search_ville)) {
    $sql .= " AND ville LIKE '%$search_ville%'";
}
if (!empty($search_email)) {
    $sql .= " AND email LIKE '%$search_email%'";
}
if (!empty($search_type)) {
    $sql .= " AND type LIKE '%$search_type%'";
}

$result = $conn->query($sql);
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
            padding: 20px;
        }

        h1 {
            color: #8e24aa;
            text-align: center;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 200px;
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
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des Utilisateurs</h1>

    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search_nom" placeholder="Nom" value="<?php echo htmlspecialchars($search_nom); ?>">
            <input type="text" name="search_prenom" placeholder="Prénom" value="<?php echo htmlspecialchars($search_prenom); ?>">
            <input type="text" name="search_dateNaissance" placeholder="Date de Naissance" value="<?php echo htmlspecialchars($search_dateNaissance); ?>">
            <input type="text" name="search_ville" placeholder="Ville" value="<?php echo htmlspecialchars($search_ville); ?>">
            <input type="text" name="search_email" placeholder="Email" value="<?php echo htmlspecialchars($search_email); ?>">
            <input type="text" name="search_type" placeholder="Type" value="<?php echo htmlspecialchars($search_type); ?>">
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
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Aucun utilisateur trouvé</td></tr>";
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
