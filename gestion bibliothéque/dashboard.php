<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4ebf7;
      color: #4b1f5d;
      padding: 10px;
    }
    .dropdown {
      margin-bottom: 15px;
    }
    .dropdown button {
      background-color: #8e44ad;
      color: white;
      padding: 20px;
      font-size: 1.3rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      text-align: left;
    }
    .dropdown-content {
      display: none;
      background-color: #f4ebf7;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      padding: 10px;
      border-radius: 5px;
    }
    .dropdown button:hover {
      background-color: #732d91;
    }
    .dropdown-content a {
      text-decoration: none;
      color: #4b1f5d;
      display: block;
      margin: 5px 0;
    }
    .dropdown-content a:hover {
      color: #8e44ad;
    }
  </style>
  <script>
    function toggleDropdown(id) {
      const content = document.getElementById(id);
      content.style.display = content.style.display === "block" ? "none" : "block";
    }
  </script>
</head>
<body>
  <h2> </h2>
  <div class="dropdown">
    <button onclick="toggleDropdown('dropdownLivres')">Livres</button>
    <div class="dropdown-content" id="dropdownLivres">
      <a href="livres_empruntes.php" target="mainFrame">Livres empruntés</a>
      <a href="emprunter_livre.php" target="mainFrame">Emprunter un livre</a>
    </div>
  </div>
  <div class="dropdown">
    <button onclick="toggleDropdown('dropdownCompte')">Compte</button>
    <div class="dropdown-content" id="dropdownCompte">
      <a href="mes_informations.php" target="mainFrame">Mes informations</a>
      <a href="modifier_compte.php" target="mainFrame">Modifier mes informations</a>
    </div>
  </div>
</body>
</html>
