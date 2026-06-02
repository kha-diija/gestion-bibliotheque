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

        .modify-date {
            text-align: center;
        }

        .modify-date input {
            width: 100px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .modify-date button {
            padding: 5px 10px;
            background-color: #8e24aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modify-date button:hover {
            background-color: #7b1fa2;
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

// Si le formulaire de mise à jour de la date de retour est soumis
if (isset($_POST['update_date_retour'])) {
    $id_user = $_POST['id_user'];
    $id_ex = $_POST['id_ex'];
    $new_date_retour = $_POST['date_retour'];

    // Mise à jour de la date de retour
    $update_sql = "UPDATE emprunter SET date_retour = ? WHERE id_user = ? AND id_ex = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $new_date_retour, $id_user, $id_ex);

    if ($stmt->execute()) {
        echo "<script>alert('Date de retour mise à jour avec succès !');</script>";
    } else {
        echo "<script>alert('Erreur lors de la mise à jour de la date de retour.');</script>";
    }
    $stmt->close();
}

// Requête pour récupérer les informations nécessaires
$sql = "
SELECT 
    e.id_user AS ID_Emprunteur,
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
                <th>Modifier Date de Retour</th>
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
                    echo "<td class='modify-date'>
                            <form method='POST'>
                                <input type='date' name='date_retour' required>
                                <input type='hidden' name='id_user' value='" . $row['ID_Emprunteur'] . "'>
                                <input type='hidden' name='id_ex' value='" . $row['ID_Exemplaire'] . "'>
                                <button type='submit' name='update_date_retour'>Modifier</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' style='text-align: center;'>Aucun emprunt trouvé</td></tr>";
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
