<?php
require_once '../../../config.php';

$conn = getDbConnection();

$id = $_GET['id'] ?? null;
$token = $_GET['token'] ?? null;

if (!$id || !$token) {
    die("Lien invalide.");
}

// Vérifier si le token existe et est valide
$stmt = $conn->prepare("SELECT token_created_at FROM users WHERE id = ? AND token = ?");
$stmt->bind_param("is", $id, $token);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($token_created_at);

if ($stmt->num_rows == 1) {
    $stmt->fetch();
    $current_time = time();
    $expiration_time = 1800; // 30 minutes de validité

    if (($current_time - $token_created_at) > $expiration_time) {
        die("Lien expiré. Veuillez refaire une demande.");
    }
} else {
    die("Lien invalide.");
}

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $errorMessage = "Les mots de passe ne correspondent pas.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{10,}$/', $new_password)) {
        $errorMessage = "Le mot de passe doit contenir au moins 10 caractères, une majuscule, un chiffre et un caractère spécial.";
    } else {
        // Générer un nouveau salt et hasher le mot de passe
        $new_salt = bin2hex(random_bytes(16));
        $hashed_new_password = hash('sha256', $new_salt . $new_password);

        // Mettre à jour le mot de passe et supprimer le token
        $update_stmt = $conn->prepare("UPDATE users SET password = ?, salt = ?, token = NULL, token_created_at = NULL WHERE id = ?");
        $update_stmt->bind_param("ssi", $hashed_new_password, $new_salt, $id);

        if ($update_stmt->execute()) {
            header("Location: ../../../login.php");
            exit();
        } else {
            $errorMessage = "Erreur lors du changement de mot de passe.";
        }
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="../../../CSS/AuthPage.css" />
</head>
<body>
    <div class="form-container">
        <p>Réinitialisation du mot de passe</p>

        <?php if (isset($errorMessage)) { echo "<p>$errorMessage</p>"; } ?>

        <form method="post">
            <input type="password" name="new_password" class="form-input" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" class="form-input" placeholder="Confirmez le mot de passe" required><br><br>
            <small id="small">Doit contenir 1 majuscule, 1 chiffre, 1 caractère spécial et au moins 10 caractères.</small>
            <button type="submit" class="form-button">Changer le mot de passe</button>
        </form>
    </div>
</body>
</html>
