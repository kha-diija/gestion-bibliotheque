<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liste des Amendes</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 80%;
      margin: 0 auto;
      padding: 20px;
    }

    h1 {
      color: #8e24aa;
      text-align: center;
    }

    .search-bar {
      margin: 20px 0;
      text-align: center;
    }

    .search-bar input[type="text"] {
      padding: 10px;
      width: 400px;
      font-size: 16px;
      color: #6a1b9a;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table th, table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    table th {
      background-color: #8e24aa;
      color: white;
    }

    table tbody tr:nth-child(even) {
      background-color: #f9f9f9;
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
    die("Connexion échouée : " . $conn->connect_error);
}

// Requête pour récupérer les amendes
$sql = "
  SELECT 
    a.id_user,
    a.id_ex,
    a.montant,
    a.raison
  FROM 
    amende a
";
$result = $conn->query($sql);

?>

<div class="container">
  <h1>Liste des Amendes</h1>

  <!-- Barre de recherche -->
  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Rechercher dans les amendes..." onkeyup="filterTable()">
  </div>

  <!-- Tableau des amendes -->
  <table id="amendeTable">
    <thead>
      <tr>
        <th>ID Utilisateur</th>
        <th>ID Exemplaire</th>
        <th>Montant (DH)</th>
        <th>Raison</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['id_user']); ?></td>
            <td><?php echo htmlspecialchars($row['id_ex']); ?></td>
            <td><?php echo htmlspecialchars($row['montant']); ?></td>
            <td><?php echo htmlspecialchars($row['raison']); ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="4">Aucune amende trouvée.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
  // Fonction de recherche pour filtrer les résultats
  function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#amendeTable tbody tr');
    
    rows.forEach(row => {
      const rowText = Array.from(row.cells)
        .map(cell => cell.textContent.toLowerCase())
        .join(' ');
      row.style.display = rowText.includes(input) ? '' : 'none';
    });
  }
</script>

</body>
</html>
