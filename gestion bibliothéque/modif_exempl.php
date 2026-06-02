<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Exemplaires</title>
    <link rel="stylesheet" href="stylestest.css">
    <style>
        .search-bar {
            margin-top: 35px;
            margin-bottom: 35px;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 400px;
            font-size: 16px;
            color: #6a1b9a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 450px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal input[type="text"],
        .modal select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .modal button {
            padding: 10px 20px;
            background-color: #8e24aa;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .modal button:hover {
            background-color: #6a1b9a;
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
    die("<div class='message error'>Connexion échouée : " . $conn->connect_error . "</div>");
}

// Traitement de la mise à jour de l'état et du statut
if (isset($_POST['update'])) {
    $id_ex = $_POST['id_ex'];
    $etat = $_POST['etat'];
    $statut = $_POST['statut'];

    // Requête de mise à jour
    $update_sql = "UPDATE exemp SET etat = ?, statut = ? WHERE id_ex = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $etat, $statut, $id_ex);
    if ($stmt->execute()) {
        echo "<div class='message success'>Exemplaire mis à jour avec succès.</div>";
    } else {
        echo "<div class='message error'>Erreur lors de la mise à jour de l'exemplaire.</div>";
    }
    $stmt->close();
}

// Requête pour récupérer tous les exemplaires avec ID
$sql = "
SELECT 
    e.id_ex AS ID_Exemplaire,
    d.titre AS Titre,
    CASE 
        WHEN l.id_doc IS NOT NULL THEN 'Livre'
        WHEN p.id_doc IS NOT NULL THEN 'Périodique'
        ELSE 'Autre'
    END AS Type,
    e.etat AS Etat,
    e.statut AS Statut
FROM exemp e
INNER JOIN doc d ON e.id_doc = d.id_doc
LEFT JOIN livre l ON d.id_doc = l.id_doc
LEFT JOIN perio p ON d.id_doc = p.id_doc;
";

$result = $conn->query($sql);
?>

<div class="container">
    <h1>Liste des Exemplaires</h1>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher par ID ou Statut...">
    </div>

    <!-- Tableau des exemplaires -->
    <table id="bookTable">
        <thead>
            <tr>
                <th>ID Exemplaire</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Statut</th>
                <th>État</th>
                <th>Modifier</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ID_Exemplaire']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Titre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Statut']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Etat']) . "</td>";
                    echo "<td><button onclick='openModal(" . $row['ID_Exemplaire'] . ", \"" . htmlspecialchars($row['Etat']) . "\", \"" . htmlspecialchars($row['Statut']) . "\")'>Modifier</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Aucun exemplaire trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal pour la modification -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Modifier l'Exemplaire</h2>
        <form method="post" onsubmit="return validateForm()">
            <input type="hidden" id="id_ex" name="id_ex">
            <label for="etat">État :</label>
            <select id="etat" name="etat" required>
                <option value="très Bon">très Bon</option>
                <option value="bon">bon</option>
                <option value="usagé">usagé</option>
                <option value="endommagé">endommagé</option>
            </select>
            <label for="statut">Statut :</label>
            <select id="statut" name="statut" required>
                <option value="en rayon">en rayon</option>
                <option value="en réserve">en réserve</option>
                <option value="en retard">en retard</option>
                <option value="en travaux">en travaux</option>
            
            </select>
            <button type="submit" name="update">Mettre à jour</button>
        </form>
    </div>
</div>

<script>
// Filtrer le tableau avec la barre de recherche
function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#bookTable tbody tr');
    
    rows.forEach(row => {
        const rowText = Array.from(row.cells)
            .map(cell => cell.textContent.toLowerCase())
            .join(' ');
        row.style.display = rowText.includes(input) ? '' : 'none';
    });
}

document.getElementById('searchInput').addEventListener('keyup', filterTable);

// Ouvrir la modale
function openModal(id_ex, etat, statut) {
    document.getElementById('id_ex').value = id_ex;
    document.getElementById('etat').value = etat;
    document.getElementById('statut').value = statut;
    document.getElementById('myModal').style.display = 'flex';
}

// Fermer la modale
function closeModal() {
    document.getElementById('myModal').style.display = 'none';
}

// Valider le formulaire avant la soumission
function validateForm() {
    const etat = document.getElementById('etat').value;
    const statut = document.getElementById('statut').value;
    if (!etat || !statut) {
        alert("Veuillez remplir tous les champs.");
        return false;
    }
    return true;
}
</script>

<?php
$conn->close();
?>

</body>
</html>
