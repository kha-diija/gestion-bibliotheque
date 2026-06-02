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

// Récupération de l'ID utilisateur à partir de l'URL
$id_user = isset($_GET['id']) ? $_GET['id'] : 0;

// Récupérer les informations de l'utilisateur
$sql = "SELECT id_user, nom, prenom, dateNaissance, ville, email, type, interdit 
        FROM user 
        WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Utilisateur non trouvé.");
}

$user = $result->fetch_assoc();

// Traitement de la modification des informations de l'utilisateur
if (isset($_POST['update_user'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateNaissance = $_POST['dateNaissance'];
    $ville = $_POST['ville'];
    $email = $_POST['email'];
    $type = $_POST['type'];
    $interdit = isset($_POST['interdit']) ? 1 : 0;

    // Mise à jour de l'utilisateur dans la base de données
    $update_sql = "UPDATE user 
                   SET nom = ?, prenom = ?, dateNaissance = ?, ville = ?, email = ?, type = ?, interdit = ? 
                   WHERE id_user = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssii", $nom, $prenom, $dateNaissance, $ville, $email, $type, $interdit, $id_user);

    if ($stmt->execute()) {
        // Rediriger vers la page des utilisateurs après modification
        header("Location: modif_ut.php");
        exit(); // Arrêter l'exécution après la redirection
    } else {
        echo "<script>alert('Erreur lors de la mise à jour des informations de l\'utilisateur.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur</title>
    <style>
        /* Styles pour la page de modification */
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

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #8e24aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #7b1fa2;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Modifier l'Utilisateur</h1>

    <form method="POST" action="">
        <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">

        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>

        <label for="dateNaissance">Date de Naissance</label>
        <input type="date" name="dateNaissance" id="dateNaissance" value="<?php echo $user['dateNaissance']; ?>" required>

        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" value="<?php echo htmlspecialchars($user['ville']); ?>" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="type">Type</label>
        <input type="text" name="type" id="type" value="<?php echo htmlspecialchars($user['type']); ?>" required>

        <label for="interdit">Utilisateur interdit</label>
        <input type="checkbox" name="interdit" id="interdit" <?php echo $user['interdit'] ? 'checked' : ''; ?>>

        <button type="submit" name="update_user">Mettre à jour</button>
    </form>
</div>

</body>
</html>

<?php
// Fermer la connexion
$conn->close();
?>
