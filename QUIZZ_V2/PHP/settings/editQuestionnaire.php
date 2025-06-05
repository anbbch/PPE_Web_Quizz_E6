<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config.php';
include '../menu.php';

// Vérifier si l'utilisateur est authentifié et administrateur
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['status'] !== 'Administrator') {
    header("Location: " . URL . 'login.php');
    exit();
}

// Connexion à la base de données
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les questionnaires
$sql = "SELECT id, name FROM theme"; // La table contenant les thèmes des questionnaires
$result = $conn->query($sql);

$questionnaires = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questionnaires[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer les Questionnaires</title>
    <link rel="stylesheet" href="../CSS/quizz.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1>Gestion des Questionnaires</h1>
        <a href="createQuestionnaire.php" class="btn">Créer un nouveau questionnaire</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom du questionnaire</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questionnaires as $q): ?>
                    <tr>
                        <td><?= htmlspecialchars($q['id']) ?></td>
                        <td><?= htmlspecialchars($q['name']) ?></td>
                        <td>
                            <a href="editOneQuestionnaire.php?id=<?= $q['id'] ?>" class="btn-edit">Éditer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>