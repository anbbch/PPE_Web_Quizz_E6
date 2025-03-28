<?php
// Vérifier si les paramètres id et token sont bien transmis
if (!isset($_GET['id']) || !isset($_GET['token'])) {
    die("Paramètres manquants.");
}

$id = htmlspecialchars($_GET['id']);
$token = htmlspecialchars($_GET['token']);

// Générer l'URL de réinitialisation
$reset_url = "http://localhost/QUIZZ_V2/PHP/settings/Users/changeforgotPassword.php?id=$id&token=$token";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Réinitialisation de mot de passe</title>
    <!--<link rel="stylesheet" href="../../CSS/AuthPage.css">-->
</head>
<body>
    <div class="form-container">
        <h2>Réinitialisation de votre mot de passe</h2>
        <p>Un lien de réinitialisation de mot de passe a été généré pour vous :</p>
        <a href="<?= $reset_url ?>" class="form-button">Cliquez ici pour réinitialiser votre mot de passe</a>
        <p>Si vous n'avez pas fait cette demande, ignorez simplement cet email.</p>
    </div>
</body>
</html>
