<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Périodique</title>
    <style>
        form {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        form div {
            margin-bottom: 15px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input, form textarea, form button {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        form button {
            background-color: #6a1b9a;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #8e24aa;
        }
        .message {
            text-align: center;
            padding: 10px;
            margin: 20px auto;
            max-width: 500px;
            border-radius: 5px;
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
    </style>
</head>
<body>

<h1 style="text-align: center;">Ajouter un Périodique</h1>

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $issn = $_POST['issn'];
    $num = $_POST['num'];
    $vol = $_POST['vol'];
    $prix = $_POST['prix'];
    $editeur = $_POST['editeur']; // Champ éditeur ajouté
    $annee = $_POST['annee']; // Champ année ajouté

    if (empty($titre) || empty($issn) || empty($num) || empty($vol) || empty($prix) || empty($editeur) || empty($annee)) {
        echo "<div class='message error'>Tous les champs sont obligatoires.</div>";
    } else {
        $conn->begin_transaction();

        try {
            // Définir la catégorie par défaut comme 'periodique'
            $cat = 'periodique';

            // Insérer dans la table Doc avec 'cat' défini à 'periodique', 'editeur', et 'annee'
            $stmtDoc = $conn->prepare("INSERT INTO Doc (titre, cat, editeur, annee) VALUES (?, ?, ?, ?)");
            $stmtDoc->bind_param("ssss", $titre, $cat, $editeur, $annee);
            $stmtDoc->execute();

            // Récupérer l'ID généré
            $idDoc = $stmtDoc->insert_id;

            // Générer la référence sous le format 'DOC' suivi de 6 chiffres aléatoires
            $ref = 'DOC' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Mettre à jour la table Doc avec la référence générée
            $stmtUpdateRef = $conn->prepare("UPDATE Doc SET ref = ? WHERE id_doc = ?");
            $stmtUpdateRef->bind_param("si", $ref, $idDoc);
            $stmtUpdateRef->execute();

            // Insérer dans la table Perio (Périodique)
            $stmtPerio = $conn->prepare("INSERT INTO Perio (id_doc, issn, num, vol, prixp) VALUES (?, ?, ?, ?, ?)");
            $stmtPerio->bind_param("issds", $idDoc, $issn, $num, $vol, $prix);
            $stmtPerio->execute();

            // Validation de la transaction
            $conn->commit();
            echo "<div class='message success'>Le périodique a été ajouté avec succès.</div>";
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $conn->rollback();
            echo "<div class='message error'>Erreur lors de l'ajout : " . $e->getMessage() . "</div>";
        }

        // Fermeture des requêtes préparées
        if (isset($stmtDoc)) {
            $stmtDoc->close();
        }
        if (isset($stmtUpdateRef)) {
            $stmtUpdateRef->close();
        }
        if (isset($stmtPerio)) {
            $stmtPerio->close();
        }
    }
}
$conn->close();
?>

<form method="POST">
    <div>
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre" required>
    </div>
    <div>
        <label for="issn">ISSN(perio.PRIMARY)</label>
        <input type="text" id="issn" name="issn" required>
    </div>
    <div>
        <label for="num">Numéro</label>
        <input type="number" id="num" name="num" required>
    </div>
    <div>
        <label for="vol">Volume</label>
        <input type="number" id="vol" name="vol" required>
    </div>
    <div>
        <label for="prix">Prix (DH)</label>
        <input type="number" id="prix" name="prix" step="0.01" required>
    </div>
    <div>
        <label for="editeur">Éditeur</label>
        <input type="text" id="editeur" name="editeur" required>
    </div>
    <div>
        <label for="annee">Année</label>
        <input type="number" id="annee" name="annee" required>
    </div>
    <button type="submit">Ajouter le Périodique</button>
</form>

</body>
</html>
