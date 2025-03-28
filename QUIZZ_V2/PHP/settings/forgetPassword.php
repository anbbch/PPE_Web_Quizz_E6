<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    $conn = getDbConnection();
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Vérifier si l'email existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    if (!$stmt) {
        die("Erreur SQL : " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        // Générer un token sécurisé et timestamp d'expiration
        $token = bin2hex(random_bytes(32));
        $timestamp = time() + 1800; // 30 minutes de validité

        // Mettre à jour le token et le timestamp dans la base
        $update_stmt = $conn->prepare("UPDATE users SET token = ?, token_created_at = ? WHERE email = ?");
        if (!$update_stmt) {
            die("Erreur SQL : " . $conn->error);
        }

        $update_stmt->bind_param("sis", $token, $timestamp, $email);
        $update_stmt->execute();

        // Générer le lien de réinitialisation
        $reset_url = "http://localhost/QUIZZ_V2/PHP/settings/changeforgotPassword.php?id=$user_id&token=$token";

        // Rediriger vers la page `email.php` avec les paramètres id et token
        header("Location: email.php?id=$user_id&token=$token");
        exit();
    } else {
        $errorMessage = "Aucun compte trouvé avec cet email.";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="../../CSS/AuthPage.css" />
</head>

<body>
    <div class="form-container">
        <div id="configForm">
            <br>
            <div>
                <p id="en-tete">Réinitialiser le mot de passe</p>
            </div><br>
            <?php if (isset($message)) {
                echo "<p>$message</p>";
            } ?>
            <form method="post" action="">
                <input type="email" name="email" class="form-input" placeholder="Votre adresse e-mail" required>
                <?php if (isset($errorMessage)) {
                    echo "<p>$errorMessage</p>";
                } ?>
                <button type="submit" class="form-button">Réinitialiser</button>
            </form>
        </div>
    </div>
    <div class="back-home">
        <a href="../../login.php" class="btn btn-secondary">Retour</a>
    </div>
</body>

</html>