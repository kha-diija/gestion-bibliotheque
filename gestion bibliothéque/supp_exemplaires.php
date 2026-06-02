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
        .search-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .search-container div {
            flex: 1;
            margin-right: 10px;
        }
        .search-container div:last-child {
            margin-right: 0;
        }
        .search-container label {
            display: block;
            margin-bottom: 5px;
        }
        .search-container input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Liste des Exemplaires </h1>

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

        // Suppression d'un exemplaire si un ID est fourni
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_ex'])) {
            $id_ex = intval($_POST['id_ex']);
            $delete_sql = "DELETE FROM exemp WHERE id_ex = ?";
            $stmt_delete = $conn->prepare($delete_sql);
            $stmt_delete->bind_param("i", $id_ex);

            if ($stmt_delete->execute()) {
                echo "<p style='color: green;'>Exemplaire supprimé avec succès.</p>";
            } else {
                echo "<p style='color: red;'>Erreur lors de la suppression de l'exemplaire.</p>";
            }

            $stmt_delete->close();
        }

        // Récupérer les valeurs des champs de recherche
        $id_ex_search = isset($_GET['id_ex']) ? $_GET['id_ex'] : '';
        $titre_search = isset($_GET['titre']) ? $_GET['titre'] : '';
        $type_search = isset($_GET['type']) ? $_GET['type'] : '';
        $statut_search = isset($_GET['statut']) ? $_GET['statut'] : '';
        $etat_search = isset($_GET['etat']) ? $_GET['etat'] : '';

        // Requête de récupération des exemplaires qui ne sont pas en prêt avec des filtres
        $sql = "
            SELECT 
                e.id_ex, 
                d.titre, 
                d.cat AS type, 
                e.statut, 
                e.etat
            FROM 
                exemp e
            JOIN 
                doc d ON e.id_doc = d.id_doc
            WHERE 
                e.statut != 'en prêt'
				AND e.statut != 'en retard'
                AND (e.id_ex LIKE ?)
                AND (d.titre LIKE ?)
                AND (d.cat LIKE ?)
                AND (e.statut LIKE ?)
                AND (e.etat LIKE ?)
        ";

        $stmt = $conn->prepare($sql);
        $id_ex_search = "%" . $id_ex_search . "%";
        $titre_search = "%" . $titre_search . "%";
        $type_search = "%" . $type_search . "%";
        $statut_search = "%" . $statut_search . "%";
        $etat_search = "%" . $etat_search . "%";

        $stmt->bind_param("sssss", $id_ex_search, $titre_search, $type_search, $statut_search, $etat_search);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <!-- Barre de recherche -->
        <form method="get">
            <div class="search-container">
                <div>
                    <label for="id_ex">ID Exemplaire</label>
                    <input type="text" name="id_ex" id="id_ex" value="<?= htmlspecialchars($id_ex_search) ?>" placeholder="ID Exemplaire">
                </div>
                <div>
                    <label for="titre">Titre</label>
                    <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($titre_search) ?>" placeholder="Titre">
                </div>
                <div>
                    <label for="type">Type</label>
                    <input type="text" name="type" id="type" value="<?= htmlspecialchars($type_search) ?>" placeholder="Type">
                </div>
                <div>
                    <label for="statut">Statut</label>
                    <input type="text" name="statut" id="statut" value="<?= htmlspecialchars($statut_search) ?>" placeholder="Statut">
                </div>
                <div>
                    <label for="etat">État</label>
                    <input type="text" name="etat" id="etat" value="<?= htmlspecialchars($etat_search) ?>" placeholder="État">
                </div>
            </div>

            <button type="submit">Filtrer</button>
        </form>

        <!-- Affichage des résultats -->
        <?php if ($result && $result->num_rows > 0): ?>
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
                                <form method="post" style="margin: 0;">
                                    <input type="hidden" name="id_ex" value="<?= htmlspecialchars($row['id_ex']) ?>">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun exemplaire disponible qui n'est pas en prêt.</p>
        <?php endif;

        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
