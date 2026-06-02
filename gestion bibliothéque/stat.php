<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de la Bibliothèque</title>
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
        .stat {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
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

// Requête pour récupérer le nombre total de documents
$sql_documents = "SELECT COUNT(*) AS total_documents FROM doc;";
$result_documents = $conn->query($sql_documents);
$total_documents = $result_documents->fetch_assoc()['total_documents'];

// Requête pour récupérer le nombre total d'exemplaires
$sql_exemplaires = "SELECT COUNT(*) AS total_exemplaires FROM exemp;";
$result_exemplaires = $conn->query($sql_exemplaires);
$total_exemplaires = $result_exemplaires->fetch_assoc()['total_exemplaires'];

// Requête pour récupérer le nombre total de livres
$sql_livres = "SELECT COUNT(*) AS total_livres FROM livre;";
$result_livres = $conn->query($sql_livres);
$total_livres = $result_livres->fetch_assoc()['total_livres'];

// Requête pour récupérer le nombre total de périodiques
$sql_periodiques = "SELECT COUNT(*) AS total_periodiques FROM perio;";
$result_periodiques = $conn->query($sql_periodiques);
$total_periodiques = $result_periodiques->fetch_assoc()['total_periodiques'];

// Requête pour récupérer le nombre total d'utilisateurs
$sql_utilisateurs = "SELECT COUNT(*) AS total_utilisateurs FROM user;";
$result_utilisateurs = $conn->query($sql_utilisateurs);
$total_utilisateurs = $result_utilisateurs->fetch_assoc()['total_utilisateurs'];
?>

<div class="container">
    <h1>Statistiques de la Bibliothèque</h1>

    <div class="stat">
        <h2>Nombre total de documents :</h2>
        <p><?php echo htmlspecialchars($total_documents); ?></p>
    </div>

    <div class="stat">
        <h2>Nombre total d'exemplaires :</h2>
        <p><?php echo htmlspecialchars($total_exemplaires); ?></p>
    </div>

    <div class="stat">
        <h2>Nombre total de livres :</h2>
        <p><?php echo htmlspecialchars($total_livres); ?></p>
    </div>

    <div class="stat">
        <h2>Nombre total de périodiques :</h2>
        <p><?php echo htmlspecialchars($total_periodiques); ?></p>
    </div>

    <div class="stat">
        <h2>Nombre total d'utilisateurs :</h2>
        <p><?php echo htmlspecialchars($total_utilisateurs); ?></p>
    </div>
</div>

<?php
$conn->close();
?>

</body>
</html>
