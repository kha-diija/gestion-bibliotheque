<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Livres</title>
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

        /* Style du bouton Supprimer */
        .delete-button {
            background-color: #8e24aa; /* Mauve */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        /* Couleur rouge au survol */
        .delete-button:hover {
            background-color: #e53935; /* Rouge */
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

// Supprimer un livre si une requête de suppression est reçue
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Supprimer le livre de la table Livre
    $stmt = $conn->prepare("DELETE FROM Livre WHERE id_doc = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Supprimer également de la table Doc
    $stmt = $conn->prepare("DELETE FROM Doc WHERE id_doc = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    echo "<div class='message' style='background-color: #d4edda; color: #155724;'>Le livre a été supprimé avec succès.</div>";
}

// Requête pour récupérer tous les livres
$sql = "
    SELECT 
        d.id_doc AS ID,
        d.titre AS Titre,
        l.auteurs AS Auteurs,
        d.annee AS Annee,
        d.editeur AS Editeur,
        l.isbn AS ISBN
    FROM Doc d
    JOIN Livre l ON d.id_doc = l.id_doc;
";

$result = $conn->query($sql);
?>

<div class="container">
    <h1>Liste des Livres</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher un livre...">
    </div>

    <!-- Tableau des livres -->
    <table id="bookTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur(s)</th>
                <th>Année</th>
                <th>Éditeur</th>
                <th>ISBN</th>
                <th>Actions</th> <!-- Nouvelle colonne pour supprimer -->
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Titre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Auteurs']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Annee']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Editeur']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ISBN']) . "</td>";
                    // Ajouter un bouton pour supprimer
                    echo "<td><a href='?delete_id=" . $row['ID'] . "' class='delete-button' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce livre ?\");'>Supprimer</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Aucun livre trouvé</td></tr>";
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
