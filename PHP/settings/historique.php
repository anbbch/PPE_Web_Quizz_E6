<?php
require_once '../../config.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../login.php");
    exit();
}

$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';

if (isset($_SESSION['id'])) {
    $user_id = intval($_SESSION['id']);
} else {
    die("Erreur : utilisateur non authentifié.");
}

// Connexion à la base de données
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Si admin, récupérer la liste des utilisateurs ayant un historique
$users = [];
if ($is_admin) {
    $sql_users = "SELECT DISTINCT u.id, u.username, u.name 
                  FROM users u
                  JOIN historiqueUtilisateur h ON u.id = h.user_id";
    $result_users = $conn->query($sql_users);
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
}

// Déterminer l'utilisateur sélectionné (par défaut, l'utilisateur connecté)
$selected_user_id = $user_id;
if ($is_admin && isset($_POST['selected_user'])) {
    $selected_user_id = intval($_POST['selected_user']);
}

// Récupérer les résultats de l'utilisateur dans la table historiqueutilisateur
$sql = "
    SELECT h.id, h.questionnaire_id, h.date_reponse, q.libelle AS questionnaire_nom, 
           h.question, h.bonne_reponse, h.reponse,  h.score
    FROM historiqueutilisateur h
    JOIN questionnaire q ON h.questionnaire_id = q.id
    WHERE h.user_id = ?
    ORDER BY h.date_reponse DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $selected_user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Réponses</title>
    <link rel="stylesheet" href="../../CSS/historique.css">
</head>

<body>
    <div id="quiz-container">
        <h2>Historique des réponses</h2>

        <?php if ($is_admin): ?>
            <div class="selection-container">
                <form method="POST">
                    <label for="selected_user">Sélectionner un utilisateur :</label>
                    <select name="selected_user" id="selected_user" onchange="this.form.submit()">
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id']; ?>" <?= ($selected_user_id == $user['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($user['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        <?php endif; ?>

        <?php
        $previous_date = '';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $current_date = date("d/m/Y H:i:s", strtotime($row['date_reponse']));
                if ($current_date !== $previous_date) {
                    if ($previous_date !== '') {
                        echo "<div class='score-total'>Total Score: " . $totalScore . "</div>";
                        echo "</tbody></table>";
                    }
                    echo "<div class='section'>
                            <div class='questionnaire-info'>
                                <span>" . htmlspecialchars($row['questionnaire_nom']) . "</span>
                                <span class='date'>Date: " . $current_date . "</span>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Questions</th>
                                        <th>Réponse Utilisateur</th>
                                        <th>Bonne Réponse</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>
                                <tbody>";
                    $totalScore = 0;
                }

                echo "<tr>
                        <td>" . htmlspecialchars($row['question']) . "</td>
                        <td>" . htmlspecialchars($row['reponse']) . "</td>
                        <td>" . htmlspecialchars($row['bonne_reponse']) . "</td>
                        <td>" . htmlspecialchars($row['score']) . "</td>
                      </tr>";

                $totalScore += $row['score'];
                $previous_date = $current_date;
            }
            echo "<div class='score-total'>Total Score: " . $totalScore . "</div>";
            echo "</tbody></table>";
        } else {
            echo "<p>Aucun historique trouvé pour cet utilisateur.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
    <div class="back-home">
        <a href="<?= $is_admin ? '../homeAdmin.php' : '../homeUser.php' ?>" class="btn btn-secondary">Menu</a>
    </div>
</body>

</html>
