<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Vérifier si l'utilisateur est authentifié
require_once '../config.php';
if (!isset($_SESSION['authenticated']) || $_COOKIE['authenticated'] !== 'true') {
  header("Location: ../login.php");
  exit();
}
$is_admin = isset($_COOKIE['status']) && $_COOKIE['status'] === 'Administrator';

// Vérifier si le formulaire de déconnexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
  // Déconnecter l'utilisateur
  session_unset(); // Supprimer toutes les variables de session
  session_destroy(); // Détruire la session
  // Rediriger vers la page de connexion après la déconnexion
  header("Location: ../login.php");
  exit();
}

// Connexion à la base de données
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
  die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les thèmes
$sql = "SELECT id, name FROM thème";
$result = $conn->query($sql);

$themes = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $themes[] = $row;
  }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Quiz Interactif</title>
  <link rel="stylesheet" href="../CSS/quizz.css" />
</head>

<body>
  <div class="menu-container">
    <button class="menu-button">☰ Menu</button>
    <div class="menu-dropdown">
      <div class="logout-container">
        <form method="post">
          <button type="submit" class="disconnect-btn" name="logout">Logout</button>
        </form>
      </div>
      <a href="settings/changePassword.php">Changer de mot de passe</a>
      <?php if ($is_admin): ?>
        <a href="settings/createAdmin.php">Créer un utilisateur</a>
        <a href="settings/editQuestionnaire.php">Modifier un questionnaire</a>
        <a href="settings/manageUsers.php">Manage</a>
      <?php endif; ?>
    </div>
  </div>
  <div id="Global">
    <p id="timer-global"></p>
  </div>
  <div id="quiz-container">
    <p id="timer"></p>
    <h1>Quiz Interactif</h1>
    <div id="subject-selection">
      <h2>Choisissez un thème :</h2>
      <?php foreach ($themes as $theme): ?>
        <button class="subject-button" data-subject="<?= htmlspecialchars($theme['id']) ?>">
          <?= htmlspecialchars($theme['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!--<div class="logout-container">
    <form method="post">
      <button type="submit" class="disconnect-btn" name="logout">Logout</button>
    </form>
  </div>-->

  <script>
    document.querySelectorAll(".subject-button").forEach(button => {
      button.addEventListener("click", () => {
        const themeId = button.dataset.subject;
        window.location.href = `Quizz.php?theme=${themeId}`;
      });
    });
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

  <script src="../JS/question.js"></script>
  <script src="../JS/quizz.js"></script>


</body>

</html>