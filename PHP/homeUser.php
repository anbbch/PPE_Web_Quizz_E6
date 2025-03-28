<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header("Location: ../login.php");
  exit();
}

$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';
if ($is_admin) {
  header("Location: homeAdmin.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
  session_unset();
  session_destroy();
  header("Location: ../login.php");
  exit();
}

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$sql = "SELECT id, name FROM thème";
$result = $conn->query($sql);
$themes = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Interactif - Utilisateur</title>
    <link rel="stylesheet" href="../CSS/quizz.css">
    <script>
        function openPopup() {
            document.getElementById("theme-popup").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }
        function closePopup() {
            document.getElementById("theme-popup").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }
        function confirmDelete() {
            return confirm("Voulez-vous vraiment supprimer ce thème ?");
        }
    </script>
</head>
<body>
    <div class="menu-container">
        <button class="menu-button">☰ Menu</button>
        <div class="menu-dropdown">
            <form method="post">
                <button type="submit" class="disconnect-btn" name="logout">Logout</button>
            </form>
            <a href="settings/Users/changePassword.php">Changer de mot de passe</a>
            <a href="settings/Users/changeInfo.php">Changer ses infos</a>
            <a href="settings/historique.php">Historique</a>
        </div>
    </div>

    <div id="quiz-container">
        <h1>Quiz Interactif</h1>
        <div id="subject-selection">
            <h2>Choisissez un thème :</h2>
            <?php foreach ($themes as $theme): ?>
                <div class="theme-item">
                    <button class="subject-button" onclick="window.location.href='questionnaire.php?theme=<?= $theme['id'] ?>'">
                        <?= htmlspecialchars($theme['name']) ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
      const menuButton = document.querySelector(".menu-button");
      const menuDropdown = document.querySelector(".menu-dropdown");

      menuButton.addEventListener("click", function(event) {
        event.stopPropagation(); // Empêche la propagation pour éviter de fermer immédiatement
        menuDropdown.style.display = menuDropdown.style.display === "block" ? "none" : "block";
      });

      document.addEventListener("click", function(event) {
        if (!menuButton.contains(event.target) && !menuDropdown.contains(event.target)) {
          menuDropdown.style.display = "none";
        }
      });
    });
  </script>

</body>
</html>
