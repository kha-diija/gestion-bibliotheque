<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes informations</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4ebf7;
      color: #4b1f5d;
      padding: 15px;
    }
    .form-container {
      max-width: 600px;
      margin: auto;
      padding: 15px;
      background-color: rgba(142, 68, 173, 0.1);
      border-radius: 10px;
    }
    label {
      font-weight: bold;
      display: block;
      margin: 10px 0 5px;
    }
    input {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background-color: #fff;
    }
    input:disabled {
      background-color: #eaeaea;
    }
  </style>
</head>
<body>
<?php

session_start();
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

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$sql = "SELECT nom, prenom,dateNaissance,ville,codePostal, tel,email, type FROM User WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si les informations existent
if ($result->num_rows === 0) {
    echo "Utilisateur non trouvé.";
    exit;
}
// Extraire les données de l'utilisateur
$user_data = $result->fetch_assoc();

// Fermer la requête préparée
$stmt->close();


// Fermer la connexion
$conn->close();
?>
  <h2>Mes informations</h2>
  <div class="form-container">
    <form>
    <label for="nom">Nom :</label>
      <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user_data['nom']) ?>" disabled>
      
      <label for="prenom">Prénom :</label>
      <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user_data['prenom']) ?>" disabled>
      
      <label for="date_naissance">Date de naissance :</label>
      <input type="text" id="date_naissance" value="<?= htmlspecialchars($user_data['dateNaissance']) ?>" disabled>
      
      <label for="ville">Ville :</label>
      <input type="text" id="ville" value="<?= htmlspecialchars($user_data['ville']) ?>" disabled>
      
      <label for="code_postal">Code postal :</label>
      <input type="text" id="code_postal" value="<?= htmlspecialchars($user_data['codePostal']) ?>" disabled>
      
      <label for="telephone">Numéro de téléphone :</label>
      <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($user_data['tel']) ?>" disabled>
      
	   <label for="email">Adresse e-mail :</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" disabled>
      
	  <label for="categorie">Catégorie d'utilisateur :</label>
      <input type="text" id="categorie" value="<?= htmlspecialchars($user_data['type']) ?>" disabled>
    </form>
  </div>
</body>
</html>
