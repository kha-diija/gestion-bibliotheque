<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajout d'une amende</title>
  <link rel="stylesheet" href="styleinsc.css">
  <script>
    // Validation du formulaire
    function validateFineForm(event) {
      const montant = document.getElementById("montant").value.trim();
      const raison = document.getElementById("raison").value;

      if (!montant || !raison) {
        alert("Tous les champs doivent être remplis.");
        event.preventDefault();
        return false;
      }

      // Vérification du format du montant
      if (!/^\d+(\.\d{1,2})?$/.test(montant)) {
        alert("Le montant doit être un nombre valide (ex: 100 ou 100.50).");
        event.preventDefault();
        return false;
      }

      return true;
    }
  </script>
</head>
<body>

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

$successMessage = ""; // Variable pour le message de succès

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $conn->real_escape_string($_POST['id_user']);
    $id_ex = $conn->real_escape_string($_POST['id_ex']);
    $montant = $conn->real_escape_string($_POST['montant']);
    $raison = $conn->real_escape_string($_POST['raison']);

    // Vérification si l'ID utilisateur existe dans la table user
    $checkUserQuery = "SELECT * FROM user WHERE id_user = '$id_user'";
    $userResult = $conn->query($checkUserQuery);

    // Vérification si l'ID exemplaire existe dans la table exemplaire
    $checkExQuery = "SELECT * FROM exemp WHERE id_ex = '$id_ex'";
    $exempResult = $conn->query($checkExQuery);

    if ($userResult->num_rows == 0) {
        echo "<script>alert('L\'ID utilisateur n\'existe pas.');</script>";
    } elseif ($exempResult->num_rows == 0) {
        echo "<script>alert('L\'ID exemplaire n\'existe pas.');</script>";
    } else {
        // Vérification si l'entrée existe déjà dans la table amende
        $checkExistQuery = "SELECT * FROM amende WHERE id_user = '$id_user' AND id_ex = '$id_ex'";
        $existResult = $conn->query($checkExistQuery);

        if ($existResult->num_rows > 0) {
            echo "<script>alert('Une amende pour cet utilisateur et cet exemplaire existe déjà.');</script>";
        } else {
            // Insérer les données dans la table amende
            $sql = "INSERT INTO amende (id_user, id_ex, montant, raison)
                    VALUES ('$id_user', '$id_ex', '$montant', '$raison')";

            if ($conn->query($sql) === TRUE) {
                // Mise à jour de la table `exemplaire` avec la raison et le statut
                $updateExQuery = "
                    UPDATE exemp
                    SET etat = '$raison', statut = 'en travaux'
                    WHERE id_ex = '$id_ex'
                ";

                if ($conn->query($updateExQuery) === TRUE) {
                    $successMessage = "Amende ajoutée avec succès et mise à jour de l'état de l'exemplaire effectuée !";
                } else {
                    echo "<script>alert('Erreur lors de la mise à jour de l\'exemplaire : " . $conn->error . "');</script>";
                }
            } else {
                echo "Erreur : " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
?>

<div class="login-container">
  <h1>Ajout d'une amende</h1>

  <?php if ($successMessage): ?>
    <div class="success-message"><?php echo $successMessage; ?></div>
  <?php endif; ?>

  <form id="fineForm" method="post" onsubmit="return validateFineForm(event)">
    <div class="input-group">
      <label for="id_user">ID Utilisateur</label>
      <input type="text" id="id_user" name="id_user" placeholder="Entrez l'ID de l'utilisateur" required>
    </div>
    <div class="input-group">
      <label for="id_ex">ID Exemplaire</label>
      <input type="text" id="id_ex" name="id_ex" placeholder="Entrez l'ID de l'exemplaire" required>
    </div>
    <div class="input-group">
      <label for="raison">Raison</label>
      <select id="raison" name="raison" required>
        <option value="" disabled selected>Choisissez une raison</option>
        <option value="usagé">Usagé</option>
        <option value="endommage">Endommagé</option>
      </select>
    </div>
    <div class="input-group">
      <label for="montant">Montant</label>
      <input type="text" id="montant" name="montant" placeholder="Entrez le montant de l'amende" required>
    </div>
    <button type="submit">Ajouter l'amende</button>
  </form>
</div>

</body>
</html>
