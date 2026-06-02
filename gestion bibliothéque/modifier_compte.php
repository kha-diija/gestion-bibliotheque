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
$sql = "SELECT nom, prenom, dateNaissance, ville, codePostal, tel, email, type, password FROM User WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si l'utilisateur existe
if ($result->num_rows === 0) {
    echo "Utilisateur non trouvé.";
    exit;
}

// Extraire les données de l'utilisateur
$user_data = $result->fetch_assoc();

// Fermer la requête préparée
$stmt->close();

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $ville = $_POST['ville'];
    $code_postal = $_POST['code_postal'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $password = $_POST['password'];  // Mot de passe saisi par l'utilisateur

    // Si un mot de passe a été modifié, on met à jour le mot de passe
    if (!empty($password)) {
        // Le mot de passe est directement utilisé sans hachage (non recommandé)
        $sql = "UPDATE User SET nom = ?, prenom = ?, dateNaissance = ?, ville = ?, codePostal = ?, tel = ?, email = ?, password = ? WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $nom, $prenom, $date_naissance, $ville, $code_postal, $telephone, $email, $password, $user_id);
    } else {
        // Si le mot de passe n'est pas modifié, on ne le met pas à jour
        $sql = "UPDATE User SET nom = ?, prenom = ?, dateNaissance = ?, ville = ?, codePostal = ?, tel = ?, email = ? WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $nom, $prenom, $date_naissance, $ville, $code_postal, $telephone, $email, $user_id);
    }

    // Exécuter la requête
    if ($stmt->execute()) {
        $message = "Vos informations ont été mises à jour avec succès.";
    } else {
        $message = "Erreur lors de la mise à jour des informations.";
    }

    // Fermer la requête préparée
    $stmt->close();
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier mon compte</title>
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
    input, select, button {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      background-color: #8e44ad;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background-color: #732d91;
    }
    .alert-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      display: none; /* Cachée par défaut */
    }
    .alert-box {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
      width: 300px;
    }
    .alert-box h2 {
      color: #4b1f5d;
      font-size: 1.5rem;
    }
    .alert-box p {
      color: #333;
      font-size: 1rem;
      margin: 15px 0;
    }
    .alert-box button {
      background-color: #8e44ad;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 1rem;
      cursor: pointer;
    }
    .alert-box button:hover {
      background-color: #732d91;
    }
  </style>
</head>
<body>
  <h2>Modifier mon compte</h2>
  <div class="form-container">
    <!-- Affichage des messages -->
    <?php if (isset($message)): ?>
      <div class="alert-overlay" id="customAlert" style="display: flex;">
        <div class="alert-box">
          <h2>Succès !</h2>
          <p><?= htmlspecialchars($message) ?></p>
          <button id="closeAlert">OK</button>
        </div>
      </div>
    <?php endif; ?>

    <form method="POST">
      <label for="nom">Nom :</label>
      <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user_data['nom']) ?>" required>

      <label for="prenom">Prénom :</label>
      <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user_data['prenom']) ?>" required>

      <label for="date_naissance">Date de naissance :</label>
      <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($user_data['dateNaissance']) ?>" required>

      <label for="ville">Ville :</label>
      <input type="text" id="ville" name="ville" value="<?= htmlspecialchars($user_data['ville']) ?>" required>

      <label for="code_postal">Code postal :</label>
      <input type="text" id="code_postal" name="code_postal" value="<?= htmlspecialchars($user_data['codePostal']) ?>" required>

      <label for="telephone">Numéro de téléphone :</label>
      <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($user_data['tel']) ?>" required>

      <label for="email">Adresse e-mail :</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>

      <label for="password">Nouveau mot de passe :</label>
      <input type="password" id="password" name="password" placeholder="Entrez un nouveau mot de passe">

      <button type="submit">Mettre à jour</button>
    </form>
  </div>

  <script>
    const closeAlertButton = document.getElementById("closeAlert");
    const alertOverlay = document.getElementById("customAlert");

    closeAlertButton.addEventListener("click", () => {
      alertOverlay.style.display = "none";
    });
  </script>
</body>
</html>
