<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';


if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header("Location: " . URL . 'login.php');
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

$sql = "SELECT id, name FROM `theme`";
if (!$result = $conn->query($sql)) {
  die("Erreur SQL : " . $conn->error);
}

$result = $conn->query($sql);
$themes = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../CSS/quizz.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Quiz Interactif - Utilisateur</title>
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
  <!-- Navbar en haut -->
  <div class="navbar">
    <?php include 'menu.php'; ?>
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

</body>

</html>