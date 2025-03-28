<?php
// login.php

// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// Check if the login form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Get the user from the database
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT id, password, salt, status FROM users WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password, $salt, $status);

    if ($stmt->num_rows == 1) {
        $stmt->fetch();

        // Hash the provided password with the stored salt
        $hashed_password_input = hash('sha256', $salt . $password);

        // Check if the provided password matches the stored hashed password
        if ($hashed_password_input === $hashed_password) {
            // Authentication successful, start the session
            session_start();
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $username; // Stocker le nom d'utilisateur
            $_SESSION['status'] = $status; // Stocker le statut
            $_SESSION['id'] = $user_id; // Stocker l'ID de l'utilisateur


            var_dump($_COOKIE);

            // Redirect to the home page
            header("Location: PHP/homeUser.php");
            exit();
        } else {
            $errorMessage = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } else {
        $errorMessage = "Nom d'utilisateur ou mot de passe incorrect.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Login Page</title>
    <link rel="stylesheet" href="./CSS/AuthPage.css" />
    <link rel="icon" type="image/x-icon" href="./Media/sodexo.ico">
</head>

<body>
    <!-- <div class="conteneur">
        <a href="https://fr.sodexo.com/contactez-sodexo/formulaire-de-contact-generique.html">
            <img src="./Media/sodexoLogo.png" alt="Logo Sodexo" />
        </a> -->
    </div>
    <div class="form-container">
        <div id="configForm">
            <br>
            <div>
                <p id="en-tete">Login Form</p>
            </div><br>
            <div>
                <form method="post" action="">
                    <input type="text" id="username" name="username" class="form-input" placeholder="Username" required>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Password" required>
                    <button type="submit" id="submitFormLogin" class="form-button">Submit</button>
                </form>
            </div>
            <?php if (isset($errorMessage)) { ?>
                <br>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php } ?>
            <br><br>
            <div class="link_Subsciption">
                <a href="PHP/settings/createUser.php">Subscribe</a>
            </div>
            <div class="link_Subsciption">
                <a href="PHP/settings/forgetPassword.php">Forgot Password</a>
            </div>
        </div>
    </div>
</body>

</html>