<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nombre d'Exemplaires par Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        h1 {
            text-align: center;
            color: #8e24aa;
        }

        .search-bar {
            margin: 20px 0;
            text-align: center;
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

// Requête pour récupérer le nombre d'exemplaires par document avec possibilité de filtrage
$sql = "
SELECT 
    d.id_doc AS ID_Document,
    d.titre AS Nom_Document,
    COUNT(ex.id_ex) AS Nbr_Exemplaires
FROM Exemp ex
INNER JOIN Doc d ON ex.id_doc = d.id_doc
WHERE d.id_doc LIKE ? OR d.titre LIKE ?
GROUP BY d.id_doc
ORDER BY Nbr_Exemplaires DESC;
";

// Préparer la requête avec les critères de recherche
$stmt = $conn->prepare($sql);
$searchParam = "%$searchTerm%";
$stmt->bind_param('ss', $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1>Nombre d'Exemplaires par Document</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Rechercher par ID ou Nom du Document" value="<?php echo htmlspecialchars($searchTerm); ?>">
        </form>
    </div>

    <!-- Tableau des résultats -->
    <table>
        <thead>
            <tr>
                <th>ID du Document</th>
                <th>Nom du Document</th>
                <th>Nombre d'Exemplaires</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ID_Document']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nom_Document']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nbr_Exemplaires']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align: center;'>Aucun document trouvé</td></tr>";
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
