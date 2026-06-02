<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Exemplaires</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 8px 12px;
            background-color: #5bc0de;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #31b0d5;
        }
        .form-container {
            display: none;
            margin-top: 20px;
            background-color: #f7f7f7;
            padding: 20px;
            border: 1px solid #ccc;
        }
        .form-container input, .form-container select {
            padding: 8px;
            margin-bottom: 10px;
            width: 100%;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .form-container button {
            background-color: #d9534f;
            border-radius: 5px;
        }
        .form-container button:hover {
            background-color: #c9302c;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input {
            padding: 8px;
            width: 48%;
            margin-right: 4%;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .search-container button {
            padding: 8px 12px;
            background-color: #5bc0de;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Liste des Exemplaires</h1>

        <!-- Formulaire de recherche -->
        <div class="search-container">
            <form method="GET">
                <input type="text" name="search_id" placeholder="Rechercher par ID Exemplaire" value="<?= isset($_GET['search_id']) ? htmlspecialchars($_GET['search_id']) : '' ?>">
                <input type="text" name="search_title" placeholder="Rechercher par Titre" value="<?= isset($_GET['search_title']) ? htmlspecialchars($_GET['search_title']) : '' ?>">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <?php
        // Connexion à la base de données
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'mylast_biblio';

        $conn = new mysqli($host, $user, $password, $database);

        if ($conn->connect_error) {
            die("<p style='color: red;'>Erreur de connexion : " . $conn->connect_error . "</p>");
        }

        // Récupérer les critères de recherche
        $search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';
        $search_title = isset($_GET['search_title']) ? $_GET['search_title'] : '';

        // Construire la requête SQL avec conditions dynamiques
        $sql = "SELECT e.id_ex, d.titre, d.cat AS type, e.statut, e.etat 
                FROM exemp e 
                JOIN doc d ON e.id_doc = d.id_doc 
                WHERE 1";

        if ($search_id) {
            $sql .= " AND e.id_ex LIKE ?";
        }
        if ($search_title) {
            $sql .= " AND d.titre LIKE ?";
        }

        // Préparer la requête
        $stmt = $conn->prepare($sql);

        // Lier les paramètres dynamiques
        if ($search_id && $search_title) {
            $search_id = "%" . $search_id . "%";
            $search_title = "%" . $search_title . "%";
            $stmt->bind_param("ss", $search_id, $search_title);
        } elseif ($search_id) {
            $search_id = "%" . $search_id . "%";
            $stmt->bind_param("s", $search_id);
        } elseif ($search_title) {
            $search_title = "%" . $search_title . "%";
            $stmt->bind_param("s", $search_title);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Exemplaire</th>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>État</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id_ex']) ?></td>
                            <td><?= htmlspecialchars($row['titre']) ?></td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td><?= htmlspecialchars($row['statut']) ?></td>
                            <td><?= htmlspecialchars($row['etat']) ?></td>
                            <td>
                                <!-- Bouton Modifier -->
                                <button onclick="showForm(<?= $row['id_ex'] ?>, '<?= htmlspecialchars($row['statut']) ?>', '<?= htmlspecialchars($row['etat']) ?>')">Modifier</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun exemplaire trouvé.</p>
        <?php endif; ?>

        <!-- Formulaire de modification -->
        <div id="form-container" class="form-container">
            <h3>Modifier l'exemplaire</h3>
            <form method="POST">
                <input type="hidden" name="id_ex" id="id_ex">
                <label for="statut">Statut :</label>
                <select name="statut" id="statut">
                    <option value="en travaux">en travaux</option>
                    <option value="en prêt">En prêt</option>
                    <option value="en rayon">en rayon</option>
                    <option value="en réserve">en réserve</option>
                    <option value="en retard">en retard</option>
                </select>
                <label for="etat">État :</label>
                <select name="etat" id="etat">
                    <option value="neuf">Neuf</option>
                    <option value="bon">Bon</option>
                    <option value="très bon">très bon</option>
                    <option value="usagé">usagé</option>
                    <option value="endommagé">endommagé</option>
                </select>
                <button type="submit" name="submit">Mettre à jour</button>
            </form>
        </div>

        <?php
        // Traitement de la modification
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
            $id_ex = $_POST['id_ex'];
            $statut = $_POST['statut'];
            $etat = $_POST['etat'];

            // Mise à jour de l'exemplaire
            $update_sql = "UPDATE exemp SET statut = ?, etat = ? WHERE id_ex = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("ssi", $statut, $etat, $id_ex);

            if ($stmt_update->execute()) {
                echo "<p style='color: green;'>Exemplaire mis à jour avec succès.</p>";
            } else {
                echo "<p style='color: red;'>Erreur lors de la mise à jour de l'exemplaire.</p>";
            }
            $stmt_update->close();
        }

        $conn->close();
        ?>

    </div>

    <script>
        // Fonction pour afficher le formulaire de modification
        function showForm(id_ex, statut, etat) {
            // Afficher le formulaire
            document.getElementById('form-container').style.display = 'block';

            // Remplir les champs du formulaire
            document.getElementById('id_ex').value = id_ex;
            document.getElementById('statut').value = statut;
            document.getElementById('etat').value = etat;
        }
    </script>
</body>
</html>
