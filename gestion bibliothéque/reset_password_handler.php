<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'LocationVoitures';

header('Content-Type: application/json');

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données du formulaire
    $email = $_POST['email'] ?? '';
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Vérifications de base
    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => "L'email, le nouveau mot de passe et la confirmation sont obligatoires."]);
        exit;
    }

    // Vérification de la correspondance entre les nouveaux mots de passe
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => "Les mots de passe ne correspondent pas."]);
        exit;
    }

    // Récupérer l'utilisateur de la table biblio
    $stmt = $pdo->prepare("SELECT mot_de_passe FROM biblio WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => "Utilisateur introuvable."]);
        exit;
    }

    // Vérification de l'ancien mot de passe (sans hachage)
    if ($user['mot_de_passe'] !== $oldPassword) {
        echo json_encode(['success' => false, 'message' => "Ancien mot de passe incorrect."]);
        exit;
    }

    // Mise à jour du mot de passe dans la table biblio
    $stmt = $pdo->prepare("UPDATE biblio SET mot_de_passe = :new_password WHERE email = :email");
    $stmt->execute(['new_password' => $newPassword, 'email' => $email]);

    // Redirection vers la page de login après le succès
    header('Location: login.html');
    exit; // Terminer le script après la redirection

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
?>
