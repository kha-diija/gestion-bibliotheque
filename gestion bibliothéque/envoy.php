<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emprunts en Retard</title>
    <link rel="stylesheet" href="stylestest.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            text-align: center;
        }
        h1 {
            color: #d32f2f;
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
            background-color: #d32f2f;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .warning {
            color: red;
            font-weight: bold;
        }
        .button-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        .send-button {
            padding: 10px 15px;
            background-color: #d32f2f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .send-button:hover {
            background-color: #c62828;
        }
        .popup {
            display: none;
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #d32f2f;
            color: white;
            padding: 40px;
            border-radius: 10px;
            font-size: 20px;
            z-index: 1000;
            width: 400px;
            text-align: center;
        }
        .popup.show {
            display: block;
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

// Mise à jour automatique du champ interdit pour les utilisateurs avec 3 rappels ou plus
$updateQuery = "
    UPDATE user
    SET interdit = 1
    WHERE id_user IN (
        SELECT e.id_user
        FROM emprunter e
        JOIN exemp ex ON e.id_ex = ex.id_ex
        WHERE e.date_retour < CURDATE() 
          AND DATEDIFF(CURDATE(), e.date_retour) >= 21 
          AND ex.statut = 'en prêt'
    );
";
$conn->query($updateQuery);

// Requête pour récupérer les emprunts en retard 
$query = "
    SELECT 
        u.id_user, 
        u.nom, 
        u.prenom, 
        e.id_ex, 
        e.date_retour, 
        DATEDIFF(CURDATE(), e.date_retour) AS jours_retard,
        CASE 
            WHEN DATEDIFF(CURDATE(), e.date_retour) >= 21 THEN 4
            WHEN DATEDIFF(CURDATE(), e.date_retour) >= 14 THEN 3
            WHEN DATEDIFF(CURDATE(), e.date_retour) >= 7 THEN 2
            ELSE 1
        END AS rappel,
        'Cet exemplaire doit etre retourner immédiatement.' AS message
    FROM emprunter e
    JOIN user u ON e.id_user = u.id_user
    JOIN exemp ex ON e.id_ex = ex.id_ex
    WHERE e.date_retour < CURDATE() 
      AND ex.statut = 'en retard'
    ORDER BY u.nom, e.date_retour;
";
$result = $conn->query($query);
?>

<div class="container">
    <h1>Liste des Utilisateurs Retardataires</h1>

    <table>
        <thead>
            <tr>
                <th>Envoyer</th> <!-- Nouvelle colonne pour le bouton "Envoyer" -->
                <th>ID Utilisateur</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>ID Exemplaire</th>
                <th>Date de Retour</th>
                <th>Rappel</th>
                <th>Message</th> <!-- Déplacée à droite -->
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    // Nouvelle cellule pour le bouton "Envoyer"
                    echo "<td><button class='send-button' onclick='showPopup()'>Envoyer</button></td>";
                    echo "<td>" . htmlspecialchars($row['id_user']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['id_ex']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_retour']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['rappel']) . "</td>";
                    echo "<td class='warning'>" . htmlspecialchars($row['message']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Aucun emprunt en retard trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Pop-up Message -->
<div id="popup" class="popup">
    Message envoyé
</div>

<?php
$conn->close();
?>

<script>
// Fonction pour afficher la pop-up
function showPopup() {
    var popup = document.getElementById('popup');
    popup.classList.add('show');
    
    // Masquer la pop-up après 3 secondes
    setTimeout(function() {
        popup.classList.remove('show');
    }, 3000);
}
</script>

</body>
</html>
