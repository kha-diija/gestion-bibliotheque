<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Bibliothécaires</title>
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

// Requête pour récupérer les bibliothécaires
$sql = "SELECT nom, prenom, poste FROM biblio";
$result = $conn->query($sql);
?>

<div class="container">
    <h1>Liste des Bibliothécaires</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher un bibliothécaire...">
    </div>

    <!-- Tableau des bibliothécaires -->
    <table id="biblioTable">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Poste</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['poste']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Aucun bibliothécaire trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
// Filtrer le tableau avec la barre de recherche
function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#biblioTable tbody tr');
    
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
