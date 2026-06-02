<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'mylast_biblio';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$user_id = isset($_GET['id_user']) ? intval($_GET['id_user']) : 0;
if ($user_id <= 0) {
    die("ID utilisateur invalide.");
}

// Requête SQL mise à jour pour correspondre à votre structure de base
$sql = "
    SELECT 
        d.titre AS Titre,
        d.annee AS Annee,
        d.editeur AS Editeur,
        d.cat AS Categorie,
        d.ref AS Reference,
        l.isbn AS ISBN,
        p.issn AS ISSN,
        ex.etat AS Etat,
        ex.statut AS Statut,
        e.date_emprunt AS DateEmprunt,
        e.date_retour AS DateRetour
    FROM emprunter e
    JOIN exemp ex ON e.id_ex = ex.id_ex
    JOIN doc d ON ex.id_doc = d.id_doc
    LEFT JOIN livre l ON d.id_doc = l.id_doc
    LEFT JOIN perio p ON d.id_doc = p.id_doc
    WHERE e.id_user = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents Empruntés</title>
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

        /* Style pour la barre de recherche */
        .search-bar {
            margin: 20px 0;
            text-align: center;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 400px;
            font-size: 16px;
            color: #6a1b9a;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Documents Empruntés</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher dans les emprunts...">
    </div>

    <!-- Tableau des documents empruntés -->
    <table id="borrowedDocsTable">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Année</th>
                <th>Éditeur</th>
                <th>Catégorie</th>
                <th>Référence</th>
                <th>ISBN</th>
                <th>ISSN</th>
                <th>État</th>
                <th>Statut</th>
                <th>Date d'emprunt</th>
                <th>Date de retour</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                // Affichage des documents empruntés
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Titre'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Annee'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Editeur'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Categorie'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Reference'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['ISBN'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['ISSN'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Etat'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Statut'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['DateEmprunt'] ?? 'Non disponible') . "</td>";
                    echo "<td>" . htmlspecialchars($row['DateRetour'] ?? 'Non disponible') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11'>Aucun document emprunté</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script>
// Fonction de filtrage des colonnes avec la barre de recherche
function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#borrowedDocsTable tbody tr');
    
    rows.forEach(row => {
        const rowText = Array.from(row.cells)
            .map(cell => cell.textContent.toLowerCase())
            .join(' ');
        row.style.display = rowText.includes(input) ? '' : 'none';
    });
}

document.getElementById('searchInput').addEventListener('keyup', filterTable);
</script>

</body>
</html>
