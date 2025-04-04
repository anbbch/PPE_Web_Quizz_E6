<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// create_user.php
require_once '../../config.php';
include '../menu.php';


// Check if user is authenticated and is admin
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['status'] !== 'Administrator') {
    header("Location: ../login.php");
    exit();
}

$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';
if (!$is_admin) {
    header("Location: ../homeUser.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $status = $_POST['status']; // Récupère le rôle sélectionné

    // Generate a salt
    $salt = bin2hex(random_bytes(32));
    // Hash the password with the salt
    $hashed_password = hash('sha256', $salt . $password);

    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, salt, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $login, $email, $hashed_password, $salt, $status);

    if ($stmt->execute()) {
        $successMessage = "User created successfully.";
    } else {
        $errorMessage = "Error creating user.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Create User</title>
    <link rel="stylesheet" href="../../CSS/AuthPage.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <p id="en-tete">Create User</p>
            </div><br>
            <?php if (!empty($errorMessage)) { ?>
                <script>
                    alert("<?php echo $errorMessage; ?>");
                </script>
            <?php } ?>
            <?php if (!empty($successMessage)) { ?>
                <script>
                    alert("<?php echo $successMessage; ?>");
                </script>
            <?php } ?>
            <form method="post" action="">
                <input type="text" name="name" class="form-input" placeholder="Name" required>
                <input type="text" name="login" class="form-input" placeholder="Login" required>
                <input type="text" name="email" class="form-input" placeholder="Email" required>
                <input type="password" name="password" class="form-input" placeholder="Password" required><br>
                <label>
                    Administrator<input type="radio" name="status" value="Administrator" required>
                </label>
                <label>
                    User<input type="radio" name="status" value="User" required>
                </label>
                <button type="submit" class="form-button">Create User</button>
            </form>
        </div>
    </div>
</body>

</html>