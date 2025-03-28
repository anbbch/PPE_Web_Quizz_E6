<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';


// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header("Location: ../login.php");
  exit();
}

// Vérifier si l'utilisateur est un administrateur
$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';

// Vérifier si le formulaire de déconnexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
  // Déconnecter l'utilisateur
  session_unset(); // Supprimer toutes les variables de session
  session_destroy(); // Détruire la session
  // Rediriger vers la page de connexion après la déconnexion
  header("Location: ../login.php");
  exit();
}

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Ajout d'un thème
if ($is_admin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_theme'])) {
    $theme_name = trim($_POST['theme_name']);
    if (!empty($theme_name)) {
        $stmt = $conn->prepare("INSERT INTO thème (name) VALUES (?)");
        $stmt->bind_param("s", $theme_name);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Thème ajouté avec succès.'); window.location.href = 'home.php';</script>";
    } else {
        echo "<script>alert('Veuillez entrer un nom de thème.');</script>";
    }
}

// Suppression d'un thème
if ($is_admin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_theme'])) {
    $theme_id = intval($_POST['theme_id']);
    $stmt = $conn->prepare("DELETE FROM thème WHERE id = ?");
    $stmt->bind_param("i", $theme_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Thème supprimé.'); window.location.href = 'home.php';</script>";
}

// Récupération des thèmes
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
    <title>Quiz Interactif</title>
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
        <div class="logout-container">
          <form method="post">
            <button type="submit" class="disconnect-btn" name="logout">Logout</button>
          </form>
        </div>
        <a href="settings/Users/changePassword.php">Changer de mot de passe</a>
        <a href="settings/Users/changeInfo.php">Changer ses infos</a>
        <a href="settings/historique.php">Historique</a>
        <?php if ($is_admin): ?>
          <a href="settings/createAdmin.php">Créer un utilisateur</a>
          <!--<a href="settings/editQuestionnaire.php">Modifier un questionnaire</a>-->
          <a href="settings/manageUsers.php">Manage</a>
        <?php endif; ?>
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
                    <?php if ($is_admin): ?>
                        <form method="post" onsubmit="return confirmDelete()">
                            <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                            <button type="submit" name="delete_theme" class="delete-button">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($is_admin): ?>
            <button onclick="openPopup()">Créer un nouveau thème</button>
            <div id="overlay" onclick="closePopup()"></div>
            <div id="theme-popup">
                <h2>Créer un nouveau thème</h2>
                <form method="post">
                    <input type="text" name="theme_name" placeholder="Nom du thème" required>
                    <button type="submit" name="add_theme">Créer</button>
                    <button type="button" onclick="closePopup()">Annuler</button>
                </form>
            </div>
        <?php endif; ?>
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

  <script src="../JS/question.js"></script>
  <script src="../JS/quizz.js"></script>
</body>
</html>