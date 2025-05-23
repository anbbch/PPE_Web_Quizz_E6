<?php
require_once '../../../config.php';

include '../../menu.php';

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../../../../login.php");
    exit();
}
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
//     session_unset();
//     session_destroy();
//     header("Location: ../../../../login.php");
//     exit();
// }
$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';

$conn = getDbConnection();
$username = $_SESSION['username'];

// Récupérer les informations de l'utilisateur, y compris son groupe
$query = "SELECT u.username, u.email, g.name AS groupe_nom 
          FROM users u 
          LEFT JOIN groupes g ON u.groupes_id = g.id 
          WHERE u.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($username, $email, $groupe_nom);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Change Information</title>
    <link rel="stylesheet" href="../../../CSS/AuthPage.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function toggleFields() {
            document.getElementById("new_username").disabled = !document.getElementById("change_username").checked;
            document.getElementById("new_email").disabled = !document.getElementById("change_email").checked;
        }
    </script>
</head>

<body>
    <div class="form-container">
        <div id="configForm">
            <div>
                <p id="en-tete">Change Information</p>
            </div><br>
            <!--<p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($username) ?></p>-->
            <p><strong>Email :</strong> <?= htmlspecialchars($email) ?>, <strong>Groupe :</strong> <?= htmlspecialchars($groupe_nom ?: 'Aucun groupe') ?></p><br><br>

            <form method="post" action="">
                <label><input type="checkbox" id="change_username" onclick="toggleFields()"> Changer le nom d'utilisateur</label><br>
                <input type="text" name="new_username" id="new_username" class="form-input" placeholder="Nouveau nom d'utilisateur" disabled><br>

                <label><input type="checkbox" id="change_email" onclick="toggleFields()"> Changer l'email</label><br>
                <input type="email" name="new_email" id="new_email" class="form-input" placeholder="Nouvel email" disabled><br>

                <button type="submit" class="form-button">Mettre à jour</button>
            </form>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

</html>