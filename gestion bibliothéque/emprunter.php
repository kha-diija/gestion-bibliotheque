<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: emprunter_livre.php?error=not_logged_in");
    exit();
}

// Récupérer les données nécessaires
$id_user = $_SESSION['id_user'];

if (!isset($_POST['id_doc'])) {
    header("Location: emprunter_livre.php?error=missing_id_doc");
    exit();
}

$id_doc = intval($_POST['id_doc']);

// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'mylast_biblio';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    header("Location: emprunter_livre.php?error=db_connection");
    exit();
}

// Vérifier le type d'utilisateur
$sql_user_type = "SELECT type FROM User WHERE id_user = ?";
$stmt_user_type = $conn->prepare($sql_user_type);
$stmt_user_type->bind_param("i", $id_user);
$stmt_user_type->execute();
$result_user_type = $stmt_user_type->get_result();

if ($result_user_type->num_rows === 0) {
    header("Location: emprunter_livre.php?error=user_not_found");
    exit();
}

$row_user_type = $result_user_type->fetch_assoc();
$type_client = strtolower($row_user_type['type']);

// Vérifier les restrictions pour un utilisateur "occasionnel"
if ($type_client === 'occasionnel') {
    $sql_check_emprunt = "SELECT COUNT(*) AS count FROM Emprunter WHERE id_user = ?";
    $stmt_check_emprunt = $conn->prepare($sql_check_emprunt);
    $stmt_check_emprunt->bind_param("i", $id_user);
    $stmt_check_emprunt->execute();
    $result_check_emprunt = $stmt_check_emprunt->get_result();
    $row_check_emprunt = $result_check_emprunt->fetch_assoc();

    if ($row_check_emprunt['count'] > 0) {
        header("Location: emprunter_livre.php?error=limit_reached");
        exit();
    }
}

// Déterminer la durée d'emprunt
$duration = ($type_client === 'occasionnel') ? 15 : 30;
$date_retour = date('Y-m-d', strtotime("+$duration days"));

// Vérifier la disponibilité d'un exemplaire
$sql_exemplaire = "
    SELECT ex.id_ex
    FROM Exemp ex
    WHERE ex.id_doc = ? AND ex.id_ex NOT IN (
        SELECT e.id_ex FROM Emprunter e WHERE e.date_retour IS NULL
    )
    LIMIT 1
";

$stmt_exemplaire = $conn->prepare($sql_exemplaire);
$stmt_exemplaire->bind_param("i", $id_doc);
$stmt_exemplaire->execute();
$result_exemplaire = $stmt_exemplaire->get_result();

if ($result_exemplaire->num_rows === 0) {
    header("Location: emprunter_livre.php?error=no_available_copy");
    exit();
}

$row_exemplaire = $result_exemplaire->fetch_assoc();
$id_exemplaire = $row_exemplaire['id_ex'];

// Insérer l'emprunt
$sql_emprunter = "INSERT INTO Emprunter (id_user, id_ex, date_emprunt, date_retour) VALUES (?, ?, NOW(), ?)";
$stmt_emprunter = $conn->prepare($sql_emprunter);
$stmt_emprunter->bind_param("iis", $id_user, $id_exemplaire, $date_retour);

if ($stmt_emprunter->execute()) {
    header("Location: emprunter_livre.php?success=1");
} else {
    header("Location: emprunter_livre.php?error=db_insert");
}

$conn->close();
exit();
