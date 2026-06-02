<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>admin</title>
</head>
<frameset rows="10%,90%">
  <!-- Barre supérieure : Déconnexion -->
  <frame src="deconnection.php" name="topFrame">

  <!-- Contenu principal : Dashboard et contenu -->
  <frameset cols="30%,70%">
    <!-- Dashboard à gauche -->
    <frame src="dashboardadm.php" name="menuFrame">

    <!-- Contenu dynamique à droite -->
    <frame src="defaultadm.html" name="mainFrame">
  </frameset>
</frameset>
</html>
