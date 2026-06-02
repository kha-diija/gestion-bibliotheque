<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Emprunts</title>
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
            width: 50%;
            padding: 10px;
            font-size: 16px;
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

// Requête pour récupérer les informations nécessaires (emprunts effectués aujourd'hui)
$sql = "
SELECT 
    u.id_user AS ID_Emprunteur,
    u.nom AS Nom_Emprunteur,
    ex.id_ex AS ID_Exemplaire,
    d.titre AS Nom_Document,
    CASE 
        WHEN l.id_doc IS NOT NULL THEN 'Livre'
        WHEN p.id_doc IS NOT NULL THEN 'Périodique'
        ELSE 'Inconnu'
    END AS Type_Document,
    e.date_emprunt AS Date_Emprunt,
    e.date_retour AS Date_Retour
FROM emprunter e
INNER JOIN User u ON e.id_user = u.id_user
INNER JOIN Exemp ex ON e.id_ex = ex.id_ex
INNER JOIN Doc d ON ex.id_doc = d.id_doc
LEFT JOIN Livre l ON d.id_doc = l.id_doc
LEFT JOIN Perio p ON d.id_doc = p.id_doc
WHERE DATE(e.date_emprunt) = CURDATE()  -- Filtrer pour afficher les emprunts d'aujourd'hui
ORDER BY e.date_emprunt DESC;
";

$result = $conn->query($sql);
?>

<div class="container">
    <h1>Liste des Emprunts</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher un emprunt...">
    </div>

    <!-- Tableau des emprunts -->
    <table id="loanTable">
        <thead>
            <tr>
                <th>ID Utilisateur</th>
                <th>Nom de l'Utilisateur</th>
                <th>ID Exemplaire</th>
                <th>Nom du Document</th>
                <th>Type du Document</th>
                <th>Date d'Emprunt</th>
                <th>Date de Retour</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ID_Emprunteur']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nom_Emprunteur']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ID_Exemplaire']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nom_Document']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Type_Document']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Date_Emprunt']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Date_Retour']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align: center;'>Aucun emprunt trouvé pour aujourd'hui</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
// Filtrer le tableau avec la barre de recherche
document.getElementById('searchInput').addEventListener('keyup', function () {
    const input = this.value.toLowerCase();
    const rows = document.querySelectorAll('#loanTable tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
});
</script>

<?php
$conn->close();
?>

</body>
</html>
