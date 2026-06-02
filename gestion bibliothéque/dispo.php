<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taux de Disponibilité des Exemplaires</title>
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
            color: #8e24aa;
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

// Requête pour récupérer le nombre d'exemplaires par statut
$sql_statuts = "SELECT statut, COUNT(*) AS nombre FROM exemp GROUP BY statut;";
$result_statuts = $conn->query($sql_statuts);

// Calculer le total d'exemplaires
$total_exemplaires = 0;
$disponible = 0;

if ($result_statuts && $result_statuts->num_rows > 0) {
    while ($row = $result_statuts->fetch_assoc()) {
        $total_exemplaires += $row['nombre'];
        
        // Compter les exemplaires disponibles (en rayon)
        if ($row['statut'] == 'en rayon') {
            $disponible += $row['nombre'];
        }
    }
}

// Calculer le taux de disponibilité
$taux_disponibilite = ($total_exemplaires > 0) ? ($disponible / $total_exemplaires) * 100 : 0;

?>

<div class="container">
    <h1>Taux de Disponibilité des Exemplaires</h1>

    <table>
        <thead>
            <tr>
                <th>Statut</th>
                <th>Nombre d'Exemplaires</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Réinitialiser le curseur pour afficher les statuts
            $result_statuts->data_seek(0);
            
            if ($result_statuts && $result_statuts->num_rows > 0) {
                while ($row = $result_statuts->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['statut']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Aucun exemplaire trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <h2>Taux de Disponibilité : <?php echo number_format($taux_disponibilite, 2); ?>%</h2>
    </div>
</div>

<?php
$conn->close();
?>

</body>
</html>
