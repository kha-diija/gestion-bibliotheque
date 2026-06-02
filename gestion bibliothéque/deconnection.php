<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Déconnexion</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #4b1f5d;
      color: white;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: flex-end; /* Place le contenu à droite */
      align-items: flex-end; /* Place le contenu en bas */
      height: 100vh; /* Occupe toute la hauteur de la page */
      box-sizing: border-box;
    }
    button {
      background-color: #8e44ad;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 1rem;
      border-radius: 5px;
      margin: 20px;
      cursor: pointer;
    }
    button:hover {
      background-color: #732d91;
    }
  </style>
  <script>
    function confirmLogout() {
      // Affiche une fenêtre de confirmation
      var confirmation = confirm("Êtes-vous sûr de vouloir vous déconnecter ?");
      
      if (confirmation) {
        // Si l'utilisateur clique sur "OK", redirige vers page1.php dans la fenêtre principale
        console.log("Déconnexion confirmée, redirection vers login.php");
        window.top.location.href = "login.php"; // Redirige la fenêtre principale
      } else {
        console.log("Déconnexion annulée");
      }
    }
  </script>
</head>
<body>
  <button onclick="confirmLogout()">Déconnexion</button>
</body>
</html>
