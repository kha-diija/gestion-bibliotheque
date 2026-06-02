<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Livre</title>
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

<h1 style="text-align: center;">Ajouter un Livre</h1>

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
    $auteurs = $_POST['auteurs'];
    $annee = $_POST['annee'];
    $editeur = $_POST['editeur'];
    $isbn = $_POST['isbn'];
    $prix = $_POST['prix'];

    if (empty($titre) || empty($auteurs) || empty($annee) || empty($editeur) || empty($isbn) || empty($prix)) {
        echo "<div class='message error'>Tous les champs sont obligatoires.</div>";
    } else {
        $conn->begin_transaction();

        try {
            // Définir la catégorie par défaut comme 'livre'
            $cat = 'livre';

            // Insérer dans la table Doc avec 'cat' défini à 'livre'
            $stmtDoc = $conn->prepare("INSERT INTO Doc (titre, annee, editeur, cat) VALUES (?, ?, ?, ?)");
            $stmtDoc->bind_param("siss", $titre, $annee, $editeur, $cat);
            $stmtDoc->execute();

            // Récupérer l'ID généré
            $idDoc = $stmtDoc->insert_id;

            // Générer la référence sous le format 'DOC' suivi de 6 chiffres aléatoires
            $ref = 'DOC' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Mettre à jour la table Doc avec la référence générée
            $stmtUpdateRef = $conn->prepare("UPDATE Doc SET ref = ? WHERE id_doc = ?");
            $stmtUpdateRef->bind_param("si", $ref, $idDoc);
            $stmtUpdateRef->execute();

            // Insérer dans la table Livre
            $stmtLivre = $conn->prepare("INSERT INTO Livre (id_doc, auteurs, isbn, prixl) VALUES (?, ?, ?, ?)");
            $stmtLivre->bind_param("issd", $idDoc, $auteurs, $isbn, $prix);
            $stmtLivre->execute();

            // Validation de la transaction
            $conn->commit();
            echo "<div class='message success'>Le livre a été ajouté avec succès.</div>";
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
        if (isset($stmtLivre)) {
            $stmtLivre->close();
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
        <label for="auteurs">Auteurs</label>
        <input type="text" id="auteurs" name="auteurs" required>
    </div>
    <div>
        <label for="annee">Année</label>
        <input type="number" id="annee" name="annee" required>
    </div>
    <div>
        <label for="editeur">Éditeur</label>
        <input type="text" id="editeur" name="editeur" required>
    </div>
    <div>
        <label for="isbn">ISBN(livre.PRIMARY)</label>
        <input type="text" id="isbn" name="isbn" required>
    </div>
    <div>
        <label for="prix">Prix (DH)</label>
        <input type="number" id="prix" name="prix" step="0.01" required>
    </div>
    <button type="submit">Ajouter le Livre</button>
</form>

</body>
</html>
