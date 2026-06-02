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
  <h2>   </h2>

  <div class="dropdown">
    <button onclick="toggleDropdown('dropdownLivres')">Livres</button>
    <div class="dropdown-content" id="dropdownLivres">
      <a href="liste_livres.php" target="mainFrame">Liste des livres</a>
      <a href="ordre_par_titre.php" target="mainFrame">Ordre par titre</a>
      <a href="ordre_prix_croissant.php" target="mainFrame">Ordre par prix croissant</a>
      <a href="ordre_prix_decroissant.php" target="mainFrame">Ordre par prix décroissant</a>

	 
    </div>
  </div>
  
  <div class="dropdown">
    <button onclick="toggleDropdown('dropdownPeriodiques')">Périodiques</button>
    <div class="dropdown-content" id="dropdownPeriodiques">
      <a href="liste_periodiques.php" target="mainFrame">Liste des périodiques</a>
      <a href="ordre_par_titre_periodiques.php" target="mainFrame">Ordre par titre</a>
      <a href="ordre_prix_croissant_periodiques.php" target="mainFrame">Ordre par prix croissant</a>
      <a href="ordre_prix_decroissant_periodiques.php" target="mainFrame">Ordre par prix décroissant</a>
    </div>
</div>



  <div class="dropdown">
    <button onclick="toggleDropdown('dropdownEmprunts')">Emprunts</button>
    <div class="dropdown-content" id="dropdownEmprunts">
      <a href="liste_emprunts.php" target="mainFrame">Liste des emprunts</a>
      <a href="ordre_par_date.php" target="mainFrame">Ordre par date d'emprunt</a>
	   <a href="util_nbr_emp.php" target="mainFrame">Nbr d'empunte/utilisateur</a>
	    <a href="aujoudr_emp.php" target="mainFrame">emprunte(s) d'Aujourd'hui</a>
		<a href="plus_emp.php" target="mainFrame">document plus emprunter</a>
		<a href="Nbr_emp_doc.php" target="mainFrame">Nbr d'empunte/document</a>
    </div>
  </div>


  <div class="dropdown">
    <button onclick="toggleDropdown('dropdownUtilisateurs')">Utilisateur</button>
    <div class="dropdown-content" id="dropdownUtilisateurs">
      <a href="liste_utilisateurs.php" target="mainFrame">Liste des utilisateurs</a>
	  <a href="util_bloc.php" target="mainFrame">Utilisateurs bloqués</a>
	  <a href="empint_utilisat.php" target="mainFrame">emprunt d'un utilisateur</a>
	  <a href="ajouter_empint_uti.php" target="mainFrame">emprunter à l'utilisateur</a>
	  <a href="supp_empint_uti.php" target="mainFrame">retourner une emprunte</a>
    </div>
  </div>



</body>
</html>
