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

// Fonction pour calculer la date de retour
function calculerDateRetour($typeUtilisateur) {
    $dateRetour = new DateTime(); // Date actuelle
    if ($typeUtilisateur == 'occasionnel') {
        // Si l'utilisateur est occasionnel, ajouter 15 jours
        $dateRetour->add(new DateInterval('P15D')); // 15 jours
    } else {
        // Si l'utilisateur est abonné ou privilégié, ajouter 30 jours
        $dateRetour->add(new DateInterval('P30D')); // 30 jours
    }
    return $dateRetour->format('Y-m-d'); // Format de la date
}

// Récupérer l'utilisateur
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Récupérer les détails de l'utilisateur, y compris l'attribut "interdit"
    $user_sql = "SELECT type, interdit FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();
    $user_type = $user['type'];
    $user_interdit = $user['interdit']; // 1 si l'utilisateur est interdit
    $stmt->close();
}

// Vérifier si l'utilisateur est interdit d'emprunter
if ($user_interdit == 1) {
    $message = "Vous devez retourner un exemplaire en retard avant de pouvoir emprunter à nouveau.";
    $message_type = 'error';  // Message d'erreur
} else {
    // Continuez avec l'emprunt et la recherche comme prévu
    $message = ''; // Pas de message d'erreur si l'utilisateur n'est pas interdit
}

// Gestion de la recherche
$search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';
$search_nom = isset($_GET['search_nom']) ? $_GET['search_nom'] : '';

// Requête SQL pour récupérer les exemplaires disponibles avec le nom du document
$exemplaires_sql = "
    SELECT ex.id_ex, ex.id_doc, ex.statut, ex.etat, doc.titre AS nom_document 
    FROM exemp ex
    INNER JOIN doc doc ON ex.id_doc = doc.id_doc
    WHERE ex.statut = 'en rayon'";

// Ajouter des filtres si des valeurs de recherche sont présentes
if ($search_id != '') {
    $exemplaires_sql .= " AND ex.id_ex = ?";
} elseif ($search_nom != '') {
    $exemplaires_sql .= " AND doc.titre LIKE ?";
}

// Préparer et exécuter la requête de recherche
$stmt = $conn->prepare($exemplaires_sql);

// Lier les paramètres si nécessaires
if ($search_id != '') {
    $stmt->bind_param("i", $search_id);
} elseif ($search_nom != '') {
    $search_nom = "%$search_nom%"; // Utiliser les pourcentages pour la recherche partielle
    $stmt->bind_param("s", $search_nom);
}

$stmt->execute();
$exemplaires_result = $stmt->get_result();

// Gestion de l'emprunt d'un exemplaire
if (isset($_GET['borrow']) && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $exemplaire_id = $_GET['borrow'];

    // Vérifier si l'exemplaire est disponible
    $check_sql = "SELECT statut FROM exemp WHERE id_ex = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("i", $exemplaire_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    $stmt_check->bind_result($statut);
    $stmt_check->fetch();

    /*if ($statut == 'en rayon') {
        // Mettre à jour l'état de l'exemplaire
        $update_sql = "UPDATE exemp SET statut = 'en prêt' WHERE id_ex = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("i", $exemplaire_id);
        $stmt_update->execute();

        // Calculer la date de retour selon le type d'utilisateur
        $dateRetour = calculerDateRetour($user_type);

        // Ajouter l'emprunt dans la table des emprunts
		
        $insert_sql = "INSERT INTO emprunter (id_user, id_ex, date_emprunt, date_retour) VALUES (?, ?, NOW(), ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("iis", $user_id, $exemplaire_id, $dateRetour);
        if ($stmt_insert->execute()) {
            echo "<script>alert('Exemplaire emprunté avec succès. Date de retour : $dateRetour');</script>";
        } else {
            echo "<script>alert('Erreur lors de l\'ajout de l\'emprunt.');</script>";
        }
        $stmt_insert->close();
    } else {
        echo "<script>alert('L\'exemplaire n\'est pas disponible.');</script>";
    }*/
	
	//test 
	// Gestion de l'emprunt d'un exemplaire
// Gestion de l'emprunt d'un exemplaire
if (isset($_GET['borrow']) && isset($_GET['user_id']) && empty($message)) {
    $user_id = $_GET['user_id'];
    $exemplaire_id = $_GET['borrow'];

    // Définir le droit maximum en fonction du type d'utilisateur
    $droit_max = 0;
    if ($user_type == 'occasionnel') {
        $droit_max = 1;
    } elseif ($user_type == 'abonne') {
        $droit_max = 4;
    } elseif ($user_type == 'Priviliegie') {
        $droit_max = 8;
    }

    // Compter le nombre d'exemplaires déjà empruntés
    $count_sql = "SELECT COUNT(*) AS total_emprunts 
                  FROM emprunter 
                  WHERE id_user = ?  ";
    $stmt_count = $conn->prepare($count_sql);
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $stmt_count->bind_result($total_emprunts);
    $stmt_count->fetch();
    $stmt_count->close();

    // Vérifier si l'utilisateur peut emprunter
    if ($total_emprunts >= $droit_max) {
        $message = "Vous avez atteint la limite d'emprunts autorisés pour votre type d'utilisateur.";
    } else {
        // Vérifier si l'exemplaire est disponible
        $check_sql = "SELECT statut FROM exemp WHERE id_ex = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("i", $exemplaire_id);
        $stmt_check->execute();
        $stmt_check->store_result();
        $stmt_check->bind_result($statut);
        $stmt_check->fetch();

        if ($statut == 'en rayon') {
            // Mettre à jour l'état de l'exemplaire
            $update_sql = "UPDATE exemp SET statut = 'en prêt' WHERE id_ex = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("i", $exemplaire_id);
            $stmt_update->execute();

            // Calculer la date de retour selon le type d'utilisateur
            $dateRetour = calculerDateRetour($user_type);

            // Ajouter l'emprunt dans la table des emprunts
            $insert_sql = "INSERT INTO emprunter (id_user, id_ex, date_emprunt, date_retour) VALUES (?, ?, NOW(), ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("iis", $user_id, $exemplaire_id, $dateRetour);
            if ($stmt_insert->execute()) {
                $message = "Exemplaire emprunté avec succès. Date de retour : $dateRetour";
            } else {
                $message = "Erreur lors de l'ajout de l'emprunt.";
            }
            $stmt_insert->close();
        } else {
            $message = "L'exemplaire n'est pas disponible.";
        }
        $stmt_check->close();
        $stmt_update->close();
    }
}


 //   $stmt_check->close();
   // $stmt_update->close();
}

// Fermer la connexion à la base de données après toutes les opérations
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emprunter Exemplaire</title>
    <style>
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

        h2 {
            color: #8e24aa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .btn {
            color: white;
            background-color: #5cb85c;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #4cae4c;
        }
		
		 .message {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid transparent;
            border-radius: 5px;
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
 <!-- Afficher les messages -->
    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, 'succès') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
	



    <!-- Formulaire de recherche -->
    <form method="GET" action="exemplaires.php">
        <label for="search_id">ID Exemplaire:</label>
        <input type="text" name="search_id" value="<?= htmlspecialchars($search_id) ?>" />
        <label for="search_nom">Nom Exemplaire:</label>
        <input type="text" name="search_nom" value="<?= htmlspecialchars($search_nom) ?>" />
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>" />
        <button type="submit">Rechercher</button>
    </form>

    <h2>Liste des Exemplaires Disponibles</h2>

    <?php
    // Afficher les résultats de recherche
    if ($exemplaires_result && $exemplaires_result->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>ID Exemplaire</th><th>Nom de l'Exemplaire</th><th>Statut</th><th>État</th><th>Action</th></tr></thead>";
        echo "<tbody>";

        while ($exemplaire = $exemplaires_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($exemplaire['id_ex']) . "</td>";
            echo "<td>" . htmlspecialchars($exemplaire['nom_document']) . "</td>";
            echo "<td>" . htmlspecialchars($exemplaire['statut']) . "</td>";
            echo "<td>" . htmlspecialchars($exemplaire['etat']) . "</td>";
            echo "<td><a href='exemplaires.php?user_id=$user_id&borrow=" . $exemplaire['id_ex'] . "' class='btn'>Emprunter</a></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Aucun exemplaire disponible ou correspondant à la recherche.</p>";
    }

    ?>

</div>

</body>
</html>

<?php
// Fermer la connexion à la base de données après toutes les opérations
$conn->close();
?>
