<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livres Empruntés</title>
    <link rel="stylesheet" href="stylestest2.css">
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
        button {
            background-color: #8e24aa;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #6a1b9a;
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
    die("Connexion échouée : " . $conn->connect_error);
}

// Identifiant de l'utilisateur
session_start();
$user_id = $_SESSION['id_user']; 

// Exécuter la requête pour récupérer les livres empruntés par l'utilisateur
$sql = "
    SELECT 
        d.id_doc AS ID,
        d.titre AS Titre,
        l.auteurs AS Auteurs,
        d.annee AS Annee,
        d.editeur AS Editeur,
        l.isbn AS ISBN,
        e.date_emprunt AS DateEmprunt,
        e.date_retour 
    FROM Emprunter e
    JOIN Exemp ex ON e.id_ex = ex.id_ex
    JOIN Doc d ON ex.id_doc = d.id_doc
    JOIN Livre l ON d.id_doc = l.id_doc
    WHERE e.id_user = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1>Livres Empruntés</h1>

    <!-- Barre de recherche unique -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Recherche">
    </div>
    
    <!-- Tableau -->
    <table id="bookTable" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur(s)</th>
                <th>Année</th>
                <th>Éditeur</th>
                <th>ISBN</th>
                <th>Date d'emprunt</th>
                <th>Date de retour</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Générer les lignes du tableau si des résultats sont trouvés
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='row_" . htmlspecialchars($row['ID']) . "'>";
                    echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Titre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Auteurs']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Annee']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Editeur']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ISBN']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['DateEmprunt']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_retour']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Aucun livre trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
// Fermer la connexion
$conn->close();
?>

<script>
// Fonction pour filtrer le tableau avec une seule barre de recherche
function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#bookTable tbody tr');
    
    rows.forEach(row => {
        const rowText = Array.from(row.cells)
            .map(cell => cell.textContent.toLowerCase())
            .join(' ');
        // Afficher ou masquer la ligne en fonction de la correspondance
        row.style.display = rowText.includes(input) ? '' : 'none';
    });
}

// Attacher un écouteur d'événement à la barre de recherche
document.getElementById('searchInput').addEventListener('keyup', filterTable);
</script>

</body>
</html>
