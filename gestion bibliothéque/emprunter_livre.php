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
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
    die("Connexion échouée : " . $conn->connect_error);
}

// Messages de succès ou d'erreur
$message = '';
$message_type = '';

if (isset($_GET['success'])) {
    $message = "Le livre a été emprunté avec succès.";
    $message_type = 'success';
} elseif (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'not_logged_in':
            $message = "Vous devez être connecté pour emprunter un livre.";
            break;
        case 'missing_id_doc':
            $message = "Aucun livre spécifié.";
            break;
        case 'user_not_found':
            $message = "Utilisateur non trouvé.";
            break;
        case 'limit_reached':
            $message = "Vous avez atteint votre limite d'emprunts.";
            break;
        case 'no_available_copy':
            $message = "Aucun exemplaire disponible pour ce livre.";
            break;
        case 'db_connection':
            $message = "Erreur de connexion à la base de données.";
            break;
        case 'db_insert':
            $message = "Erreur lors de l'emprunt du livre.";
            break;
        default:
            $message = "Une erreur est survenue.";
    }
    $message_type = 'error';
}

// Requête pour récupérer les livres
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

    <!-- Affichage des messages -->
    <?php if (!empty($message)): ?>
        <div class="message <?= htmlspecialchars($message_type) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher un livre...">
    </div>

    <!-- Tableau des livres -->
    <table id="bookTable" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur(s)</th>
                <th>Année</th>
                <th>Éditeur</th>
                <th>ISBN</th>
                <th>Action</th>
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
                    echo "<td>
                            <form method='POST' action='emprunter.php'>
                                <input type='hidden' name='id_doc' value='" . htmlspecialchars($row['ID']) . "'>
                                <button type='submit'>Emprunter</button>
                            </form>
                          </td>";
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
