<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - Bibliothèque</title>
  <link rel="stylesheet" href="styleinsc.css">
  <script>
    // Validation du formulaire
    function validateSignupForm(event) {
      const nom = document.getElementById("nom").value.trim();
      const prenom = document.getElementById("prenom").value.trim();
      const dateNaissance = document.getElementById("dateNaissance").value.trim();
      const ville = document.getElementById("ville").value.trim();
      const codePostal = document.getElementById("codePostal").value.trim();
      const tel = document.getElementById("telephone").value.trim();
      const email = document.getElementById("email").value.trim();
      const type = document.getElementById("typeUtilisateur").value;
   

      // Vérification des champs requis
      if (!nom || !prenom || !dateNaissance || !ville || !codePostal || !tel || !email || !type || !password) {
        alert("Tous les champs doivent être remplis.");
        event.preventDefault();
        return false;
      }

      // Validation du numéro de téléphone
      if (!/^\d+$/.test(tel)) {
        alert("Le numéro de téléphone doit contenir uniquement des chiffres.");
        event.preventDefault();
        return false;
      }

      return true;
    }
  </script>
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

$successMessage = ""; // Variable pour le message de succès

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sécuriser les données de formulaire
    $nom = $conn->real_escape_string($_POST['nom']);
    $prenom = $conn->real_escape_string($_POST['prenom']);
    $dateNaissance = $conn->real_escape_string($_POST['dateNaissance']);
    $ville = $conn->real_escape_string($_POST['ville']);
    $codePostal = $conn->real_escape_string($_POST['codePostal']);
    $tel = $conn->real_escape_string($_POST['telephone']);
    $email = $conn->real_escape_string($_POST['email']);
    $type = $conn->real_escape_string($_POST['typeUtilisateur']);


    // Vérifier si l'email existe déjà
    $checkEmailQuery = "SELECT id_user FROM User WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        echo "<script>
            alert('Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.');
            window.history.back();
        </script>";
        exit(); // Empêche l'exécution du reste du script
    }

    // Insertion dans la base de données
    $sql = "INSERT INTO User (nom, prenom, ville, codePostal, tel, email, type, dateNaissance, inscr, interdit)
            VALUES ('$nom', '$prenom', '$ville', '$codePostal', '$tel', '$email', '$type', '$dateNaissance', NOW(), 0)";

    if ($conn->query($sql) === TRUE) {
        $successMessage = "Inscription réussie !"; // Message de succès
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}
?>

  <div class="login-container">
    <h1>Inscription</h1>
    
    <!-- Affichage du message de succès -->
    <?php if ($successMessage): ?>
      <div class="success-message"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form id="signupForm" method="post" onsubmit="return validateSignupForm(event)">
      <div class="input-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
      </div>
      <div class="input-group">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>
      </div>
      <div class="input-group">
        <label for="dateNaissance">Date de naissance</label>
        <input type="date" id="dateNaissance" name="dateNaissance" required>
      </div>
      <div class="input-group">
        <label for="ville">Ville</label>
        <input type="text" id="ville" name="ville" placeholder="Entrez votre ville" required>
      </div>
      <div class="input-group">
        <label for="codePostal">Code Postal</label>
        <input type="text" id="codePostal" name="codePostal" placeholder="Entrez votre code postal" required>
      </div>
      <div class="input-group">
        <label for="telephone">Numéro de téléphone</label>
        <input type="tel" id="telephone" name="telephone" placeholder="Entrez votre numéro de téléphone" required>
      </div>
      <div class="input-group">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" placeholder="Entrez votre adresse e-mail" required>
      </div>
      <div class="input-group">
        <label for="typeUtilisateur">Type d'utilisateur</label>
        <select id="typeUtilisateur" name="typeUtilisateur" required>
          <option value="" disabled selected>Choisissez un type</option>
          <option value="Occasionnel">Utilisateur occasionnel</option>
          <option value="Abonne">Abonné</option>
          <option value="Priviliegie">Abonné privilégié</option>
        </select>
      </div>
      
      <button type="submit">S'inscrire</button>
    </form>
  </div>

</body>
</html>
