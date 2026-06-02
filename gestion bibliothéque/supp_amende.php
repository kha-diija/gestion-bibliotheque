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

// Variable pour afficher le message
$message = "";

// Traitement de la suppression de l'amende
if (isset($_GET['delete_id_user']) && isset($_GET['delete_id_ex'])) {
    // Récupérer les identifiants de l'utilisateur et de l'exemplaire
    $id_user = $_GET['delete_id_user'];
    $id_ex = $_GET['delete_id_ex'];

    // Requête pour supprimer l'amende
    $sql_delete = "DELETE FROM amende WHERE id_user = ? AND id_ex = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("ii", $id_user, $id_ex);
    $stmt->execute();

    // Vérifier si la suppression a réussi
    if ($stmt->affected_rows > 0) {
        $message = "Amende supprimée avec succès.";
    } else {
        $message = "Échec de la suppression de l'amende.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amendes</title>
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

        .search-bar {
            margin: 20px 0;
            text-align: center;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 400px;
            font-size: 16px;
            color: #6a1b9a;
            border: 1px solid #ddd;
            border-radius: 4px;
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

        .delete-btn {
            color: red;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }

        .no-data-message {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #8e24aa;
            font-weight: bold;
        }

        .message {
            text-align: center;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            border-radius: 5px;
        }

        .success {
            background-color: #4caf50;
            color: white;
        }

        .error {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des Amendes</h1>

    <!-- Afficher un message de succès ou d'erreur -->
    <?php if ($message != ""): ?>
        <div class="message <?php echo strpos($message, "succès") !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher dans les amendes..." onkeyup="filterTable()">
    </div>

    <!-- Tableau des amendes -->
    <table id="fineTable">
        <thead>
            <tr>
                <th>ID Utilisateur</th>
                <th>ID Exemplaire</th>
                <th>Montant (DH)</th>
                <th>Raison</th>
                <th>Actions</th> <!-- Nouvelle colonne pour le bouton supprimer -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Requête pour récupérer les amendes
            $sql = "SELECT id_user, id_ex, montant, raison FROM amende";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                // Affichage des résultats dans le tableau
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id_user']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['id_ex']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['montant']) . " </td>";
                    echo "<td>" . htmlspecialchars($row['raison']) . "</td>";
                    echo "<td><a href='?delete_id_user=" . $row['id_user'] . "&delete_id_ex=" . $row['id_ex'] . "' class='delete-btn'>Supprimer</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='no-data-message'>Aucune amende à payer</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script>
// Filtrer le tableau avec la barre de recherche
function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#fineTable tbody tr');
    
    rows.forEach(row => {
        const rowText = Array.from(row.cells)
            .map(cell => cell.textContent.toLowerCase())
            .join(' ');
        row.style.display = rowText.includes(input) ? '' : 'none';
    });
}
</script>

</body>
</html>
