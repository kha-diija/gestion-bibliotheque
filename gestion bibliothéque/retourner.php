<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    die("Erreur : Vous devez être connecté pour retourner un livre.");
}

// Récupérer l'identifiant de l'utilisateur connecté
$id_user = $_SESSION['id_user'];

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

// Vérifier si l'ID de l'emprunt est passé
if (!isset($_POST['id_emp'])) {
    die("Erreur : Aucun emprunt spécifié.");
}

$id_emprunt = intval($_POST['id_emp']); // Sécuriser l'ID de l'emprunt

// Vérifier que l'emprunt appartient à l'utilisateur et qu'il n'est pas déjà retourné
$sql_verification = "
    SELECT *
    FROM Emprunter
    WHERE id_emp = ? AND id_user = ? AND date_retour IS NULL
";

$stmt_verification = $conn->prepare($sql_verification);
$stmt_verification->bind_param("ii", $id_emprunt, $id_user);
$stmt_verification->execute();
$result_verification = $stmt_verification->get_result();

if ($result_verification->num_rows === 0) {
    die("Erreur : Aucun emprunt en cours trouvé pour cet utilisateur.");
}

// Mettre à jour la table Emprunter avec la date de retour
$sql_retourner = "
    UPDATE Emprunter
    SET date_retour = NOW()
    WHERE id_emp = ?
";

$stmt_retourner = $conn->prepare($sql_retourner);
$stmt_retourner->bind_param("i", $id_emprunt);

if ($stmt_retourner->execute()) {
    echo "Le livre a été retourné avec succès.";
} else {
    echo "Erreur : Impossible de retourner le livre.";
}

// Fermer les connexions
$stmt_verification->close();
$stmt_retourner->close();
$conn->close();
?>
