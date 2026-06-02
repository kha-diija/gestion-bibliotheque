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
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Style du formulaire de modification */
        #editForm {
            display: none;
            background-color: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        #editForm h2 {
            color: #8e24aa;
            margin-bottom: 20px;
        }
        #editForm form {
            display: flex;
            flex-direction: column;
        }
        #editForm form div {
            margin-bottom: 15px;
        }
        #editForm label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        #editForm input[type="text"],
        #editForm input[type="number"] {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #editForm button {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #8e24aa;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 15px;
        }
        #editForm button[type="button"] {
            background-color: #e0e0e0;
            color: #333;
        }
        #editForm button:hover {
            background-color: #7b1fa2;
        }
        #editForm button[type="button"]:hover {
            background-color: #bdbdbd;
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

// Modifier un livre si une requête de modification est reçue
if (isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $new_title = $_POST['new_title'];
    $new_authors = $_POST['new_authors'];
    $new_year = $_POST['new_year'];
    $new_publisher = $_POST['new_publisher'];
    $new_isbn = $_POST['new_isbn'];
    $new_price = $_POST['new_price']; // Nouveau champ prix

    // Requête pour mettre à jour les informations du livre
    $stmt = $conn->prepare("UPDATE Doc d JOIN Livre l ON d.id_doc = l.id_doc SET d.titre = ?, l.auteurs = ?, d.annee = ?, d.editeur = ?, l.isbn = ?, l.prixl = ? WHERE d.id_doc = ?");
    $stmt->bind_param("ssissdi", $new_title, $new_authors, $new_year, $new_publisher, $new_isbn, $new_price, $update_id);
    $stmt->execute();
    $stmt->close();

    echo "<div class='message success'>Le livre a été modifié avec succès.</div>";
}

// Requête pour récupérer tous les livres
$sql = "
    SELECT 
        d.id_doc AS ID,
        d.titre AS Titre,
        l.auteurs AS Auteurs,
        d.annee AS Annee,
        d.editeur AS Editeur,
        l.isbn AS ISBN,
        l.prixl AS Prix
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
                <th>Prix</th> <!-- Colonne pour le prix -->
                <th>Actions</th> <!-- Nouvelle colonne pour modifier -->
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
                    echo "<td>" . htmlspecialchars($row['Prix']) . " </td>"; // Affichage du prix
                    // Ajouter un bouton pour modifier
                    echo "<td><button onclick='showEditForm(" . $row['ID'] . ", \"" . addslashes($row['Titre']) . "\", \"" . addslashes($row['Auteurs']) . "\", " . $row['Annee'] . ", \"" . addslashes($row['Editeur']) . "\", \"" . addslashes($row['ISBN']) . "\", " . $row['Prix'] . ")'>Modifier</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Aucun livre trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Formulaire de modification -->
    <div id="editForm">
        <h2>Modifier un Livre</h2>
        <form method="POST">
            <input type="hidden" id="update_id" name="update_id">
            <div>
                <label for="new_title">Titre</label>
                <input type="text" id="new_title" name="new_title" required>
            </div>
            <div>
                <label for="new_authors">Auteur(s)</label>
                <input type="text" id="new_authors" name="new_authors" required>
            </div>
            <div>
                <label for="new_year">Année</label>
                <input type="number" id="new_year" name="new_year" required>
            </div>
            <div>
                <label for="new_publisher">Éditeur</label>
                <input type="text" id="new_publisher" name="new_publisher" required>
            </div>
            <div>
                <label for="new_isbn">ISBN</label>
                <input type="text" id="new_isbn" name="new_isbn" required>
            </div>
            <div>
                <label for="new_price">Prix (dh)</label>
                <input type="number" id="new_price" name="new_price" required step="0.01">
            </div>
            <button type="submit">Mettre à jour</button>
            <button type="button" onclick="document.getElementById('editForm').style.display='none';">Annuler</button>
        </form>
    </div>
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

// Fonction pour afficher le formulaire de modification avec les données du livre
function showEditForm(id, title, authors, year, publisher, isbn, price) {
    document.getElementById('update_id').value = id;
    document.getElementById('new_title').value = title;
    document.getElementById('new_authors').value = authors;
    document.getElementById('new_year').value = year;
    document.getElementById('new_publisher').value = publisher;
    document.getElementById('new_isbn').value = isbn;
    document.getElementById('new_price').value = price;
    document.getElementById('editForm').style.display = 'block';
}
</script>

<?php
$conn->close();
?>

</body>
</html>
