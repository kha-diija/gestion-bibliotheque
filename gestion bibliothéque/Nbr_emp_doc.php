<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nombre d'Emprunts par Document</title>
    <link rel="stylesheet" href="stylestest.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            text-align: center;
        }
        h1 {
            color: #8e24aa;
        }
        .search-bar {
            margin: 20px 0;
        }
        .search-bar input {
            padding: 10px;
            font-size: 16px;
            width: 50%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #8e24aa;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'mylast_biblio';

$conn = new mysqli($host, $user, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("<p style='color: red; text-align: center;'>Connexion échouée : " . $conn->connect_error . "</p>");
}

// Vérifier si un terme de recherche est soumis
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Requête pour récupérer le nombre d'emprunts par document avec possibilité de filtrage
$sql_emprunts = "
SELECT 
    d.id_doc AS ID_Document,
    d.titre AS Titre,
    COUNT(e.id_ex) AS Nombre_Emprunts
FROM doc d
LEFT JOIN exemp ex ON d.id_doc = ex.id_doc
LEFT JOIN emprunter e ON ex.id_ex = e.id_ex
WHERE d.id_doc LIKE ? OR d.titre LIKE ? 
GROUP BY d.id_doc
ORDER BY Nombre_Emprunts DESC;";

// Préparer la requête avec les critères de recherche
$stmt = $conn->prepare($sql_emprunts);
$searchParam = "%$searchTerm%";
$stmt->bind_param('ss', $searchParam, $searchParam);
$stmt->execute();
$result_emprunts = $stmt->get_result();
?>

<div class="container">
    <h1>Nombre d'Emprunts par Document</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Rechercher par ID ou Titre du Document" value="<?php echo htmlspecialchars($searchTerm); ?>">
        </form>
    </div>

    <!-- Tableau des emprunts par document -->
    <table>
        <thead>
            <tr>
                <th>ID du Document</th>
                <th>Titre du Document</th>
                <th>Nombre d'Emprunts</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_emprunts && $result_emprunts->num_rows > 0) {
                while ($row = $result_emprunts->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ID_Document']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Titre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nombre_Emprunts']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Aucun emprunt trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
?>

</body>
</html>
