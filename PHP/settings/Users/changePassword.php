<?php
// changemdp.php

require_once '../../../config.php';


// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../login.php");
    exit();
}

$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Validate new password
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{10,}$/', $new_password)) {
        $errorMessage = "The password does not meet the constraints.";
    } else {
        $username = $_COOKIE['username'];

        $conn = getDbConnection();

        // Get the user from the database
        $stmt = $conn->prepare("SELECT password, salt FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashed_password, $salt);

        if ($stmt->num_rows == 1) {
            $stmt->fetch();

            // Hash the provided old password with the stored salt
            $hashed_old_password = hash('sha256', $salt . $old_password);

            // Check if the provided old password matches the stored hashed password
            if ($hashed_old_password === $hashed_password) {
                // Generate a new salt and hash the new password
                $new_salt = bin2hex(random_bytes(32));
                $hashed_new_password = hash('sha256', $new_salt . $new_password);

                // Update the user's password and salt in the database
                $stmt = $conn->prepare("UPDATE users SET password = ?, salt = ? WHERE username = ?");
                $stmt->bind_param("sss", $hashed_new_password, $new_salt, $username);

                if ($stmt->execute()) {
                    $successMessage = "Password changed successfully.";
                } else {
                    $errorMessage = "Error changing password.";
                }
            } else {
                $errorMessage = "Old password is incorrect.";
            }
        } else {
            $errorMessage = "User not found.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="../../../CSS/AuthPage.css" />
    <link rel="icon" type="image/x-icon" href="../Media/sodexo.ico">
</head>

<body>
    <!--<div class="conteneur">
        <a href="https://fr.sodexo.com/contactez-sodexo/formulaire-de-contact-generique.html">
            <img src="../Media/sodexoLogo.png" alt="Logo Sodexo" />
        </a>
    </div>-->
    <div class="form-container">
        <div id="configForm">
            <br>
            <div>
                <p id="en-tete">Change Password</p>
            </div><br>
            <?php if (isset($successMessage)) {
                echo "<p>$successMessage</p>";
            } ?>

            <form method="post" action="">
                <input type="password" name="old_password" class="form-input" placeholder="Old Password" required>
                <input type="password" name="new_password" class="form-input" placeholder="New Password" required><br>

                <small id="small">Password must have 1 number, 1 special character, 1 uppercase </small><br><small>letter, and be at least 10 characters long.</small>
                <?php if (isset($errorMessage)) {
                    echo "<p>$errorMessage</p>";
                } ?>
                <button type="submit" class="form-button">Change Password</button>
            </form>
        </div>
    </div>
    <div class="back-home">
        <a href="<?= $is_admin ? '../../homeAdmin.php' : '../../homeUser.php' ?>" class="btn btn-secondary">Menu</a>
    </div>
</body>

</html>