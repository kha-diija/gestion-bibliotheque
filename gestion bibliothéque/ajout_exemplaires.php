<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Exemplaire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid transparent;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un Nouvel Exemplaire</h1>

        <?php
        // Connexion à la base de données
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'mylast_biblio';

        $conn = new mysqli($host, $user, $password, $database);

        if ($conn->connect_error) {
            die("<div class='message error'>Erreur de connexion : " . $conn->connect_error . "</div>");
        }

        $message = '';
        $message_type = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupérer les données du formulaire
            $nom_document = $conn->real_escape_string($_POST['nom_document']);
            $prix_exemplaire = (float) $_POST['prix_exemplaire'];

            // Vérifier si le document existe dans la table doc
            $sql_doc = "SELECT id_doc FROM doc WHERE titre = ?";
            $stmt_doc = $conn->prepare($sql_doc);
            $stmt_doc->bind_param("s", $nom_document);
            $stmt_doc->execute();
            $result_doc = $stmt_doc->get_result();

            if ($result_doc->num_rows > 0) {
                $doc = $result_doc->fetch_assoc();
                $id_doc = $doc['id_doc'];

                // Ajouter un nouvel exemplaire à la table exemp
                $etat = 'neuf';
                $statut = 'en rayon';
                $achat = date('Y-m-d');

                $sql_exemp = "INSERT INTO exemp (id_doc, achat, etat, statut, prix) VALUES (?, ?, ?, ?, ?)";
                $stmt_exemp = $conn->prepare($sql_exemp);
                $stmt_exemp->bind_param("isssd", $id_doc, $achat, $etat, $statut, $prix_exemplaire);

                if ($stmt_exemp->execute()) {
                    $message = "Exemplaire ajouté avec succès pour le document '$nom_document' (Prix : $prix_exemplaire DH).";
                    $message_type = 'success';
                } else {
                    $message = "Erreur lors de l'ajout de l'exemplaire : " . $conn->error;
                    $message_type = 'error';
                }

                $stmt_exemp->close();
            } else {
                $message = "Le document avec le titre '$nom_document' n'existe pas dans la base de données.";
                $message_type = 'error';
            }

            $stmt_doc->close();
        }

        $conn->close();
        ?>

        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="nom_document">Nom du Document :</label>
            <input type="text" id="nom_document" name="nom_document" required placeholder="Entrez le nom du document">

            <label for="prix_exemplaire">Prix de l'Exemplaire (DH) :</label>
            <input type="number" id="prix_exemplaire" name="prix_exemplaire" required step="0.01" placeholder="Entrez le prix de l'exemplaire">

            <button type="submit">Ajouter l'Exemplaire</button>
        </form>
    </div>
</body>
</html>
