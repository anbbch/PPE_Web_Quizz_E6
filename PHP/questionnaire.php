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

if (!isset($_GET['theme'])) {
    die("Thème non sélectionné.");
}

$theme_id = intval($_GET['theme']);

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les questionnaires du thème
$sql = "SELECT id, libelle FROM questionnaire WHERE theme_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $theme_id);
$stmt->execute();
$result = $stmt->get_result();
$questionnaires = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaires</title>
    <link rel="stylesheet" href="../CSS/quizz.css">
</head>

<body>

    <div id="quiz-container">
        <h1>Questionnaires</h1>
        <h2>Sélectionnez un questionnaire :</h2>
        <div id="questionnaire-selection">
            <?php foreach ($questionnaires as $q): ?>
                <button class="questionnaire-button" onclick="window.location.href='quizz.php?questionnaire=<?= $q['id'] ?>'">
                    <?= htmlspecialchars($q['libelle']) ?>
                </button>
                <button onclick="window.location.href='home.php'" class="new-questionnaire-button">Nouveau Questionnaire</button>

            <?php endforeach; ?>
        </div>

    </div>

    <div class="back-home">
        <a href="<?= $is_admin ? 'homeAdmin.php' : 'homeUser.php' ?>" class="btn btn-secondary">Menu</a>
    </div>

</body>

</html>