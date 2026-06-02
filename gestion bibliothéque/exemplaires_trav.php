<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Exemplaires</title>
    <link rel="stylesheet" href="stylestest.css">
    <style>
        .search-bar {
            margin-top: 35px;
            margin-bottom: 35px;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 400px;
            font-size: 16px;
            color: #6a1b9a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
    die("<div class='message error'>Connexion échouée : " . $conn->connect_error . "</div>");
}

// Requête pour récupérer tous les exemplaires avec ID qui sont en
$sql = "
SELECT 
    e.id_ex AS ID_Exemplaire,
    d.titre AS Titre,
    CASE 
        WHEN l.id_doc IS NOT NULL THEN 'Livre'
        WHEN p.id_doc IS NOT NULL THEN 'Périodique'
        ELSE 'Autre'
    END AS Type,
    e.statut AS Statut,
    e.etat AS Etat
FROM exemp e
INNER JOIN doc d ON e.id_doc = d.id_doc
LEFT JOIN livre l ON d.id_doc = l.id_doc
LEFT JOIN perio p ON d.id_doc = p.id_doc
WHERE e.statut = 'en travaux';  
";

$result = $conn->query($sql);
?>

<div class="container">
    <h1>Liste des Exemplaires</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher un exemplaire...">
    </div>

    <!-- Tableau des exemplaires -->
    <table id="bookTable">
        <thead>
            <tr>
                <th>ID Exemplaire</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Statut</th>
                <th>État</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ID_Exemplaire']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Titre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Statut']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Etat']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Aucun exemplaire trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
// Filtrer le tableau avec la barre de recherche
function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#bookTable tbody tr');
    
    rows.forEach(row => {
        const rowText = Array.from(row.cells)
            .map(cell => cell.textContent.toLowerCase())
            .join(' ');
        row.style.display = rowText.includes(input) ? '' : 'none';
    });
}

document.getElementById('searchInput').addEventListener('keyup', filterTable);
</script>

<?php
$conn->close();
?>

</body>
</html>
