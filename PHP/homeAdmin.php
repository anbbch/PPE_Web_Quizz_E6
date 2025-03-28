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

if (!$is_admin) {
    header("Location: homeUser.php");
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_theme'])) {
    $theme_name = trim($_POST['theme_name']);
    if (!empty($theme_name)) {
        $stmt = $conn->prepare("INSERT INTO thème (name) VALUES (?)");
        $stmt->bind_param("s", $theme_name);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Thème ajouté avec succès.'); window.location.href = 'homeAdmin.php';</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_theme'])) {
    $theme_id = intval($_POST['theme_id']);
    $stmt = $conn->prepare("DELETE FROM thème WHERE id = ?");
    $stmt->bind_param("i", $theme_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Thème supprimé.'); window.location.href = 'homeAdmin.php';</script>";
}

$sql = "SELECT id, name FROM thème";
$result = $conn->query($sql);
$themes = $result->fetch_all(MYSQLI_ASSOC);



// Récupération des scores cumulés par groupe et date
global $conn;
$scoreParGroupe = [];
$sql = "SELECT g.name AS group_name, DATE(h.date_reponse) as date_reponse, SUM(h.score) AS total_score 
        FROM historiqueUtilisateur h
        JOIN users u ON h.user_id = u.id
        JOIN groupes g ON u.groupes_id = g.id
        WHERE DATE(h.date_reponse) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        GROUP BY g.name, DATE(h.date_reponse)
        ORDER BY DATE(h.date_reponse) ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $scoreParGroupe[$row['group_name']][] = [
        'date_reponse' => $row['date_reponse'],
        'score' => $row['total_score']
    ];
}

// Récupération des questionnaires avec le meilleur taux de réussite
$topQuestionnaires = [];
$sql = "SELECT q.libelle, g.name AS group_name, AVG(h.score) AS avg_score
        FROM historiqueUtilisateur h
        JOIN users u ON h.user_id = u.id
        JOIN groupes g ON u.groupes_id = g.id
        JOIN questionnaire q ON h.questionnaire_id = q.id
        GROUP BY q.libelle, g.name
        ORDER BY avg_score DESC LIMIT 5";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $topQuestionnaires[] = $row;
}

// Récupération des 3 meilleurs utilisateurs
$topUsers = [];
$sql = "SELECT u.username, SUM(h.score) AS total_score
        FROM historiqueUtilisateur h
        JOIN users u ON h.user_id = u.id
        GROUP BY u.id
        ORDER BY total_score DESC LIMIT 3";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $topUsers[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/quizz.css">
    <title>Dashboard Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="settings/createAdmin.php">Créer un utilisateur</a>
            <a href="settings/manageUsers.php">Manage</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>Tableau de bord</h1>
        <div class="charts-container">
            <h2>Progression des Scores par Groupe</h2>
            <canvas id="scoreChart"></canvas>

            <h2>Top Questionnaires par Groupe</h2>
            <canvas id="quizChart"></canvas>
        </div>
        <div class="top-users">
            <h2>Top 3 Utilisateurs</h2>
            <ul>
                <?php foreach ($topUsers as $user) { ?>
                    <li><?= htmlspecialchars($user['username']) ?> - Score: <?= $user['total_score'] ?></li>
                <?php } ?>
            </ul>
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
                    <form method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer ce thème ?');">
                        <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                        <button type="submit" name="delete_theme" class="delete-button">Supprimer</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <button onclick="document.getElementById('theme-popup').style.display='block'">Créer un nouveau thème</button>
        <div id="theme-popup">
            <h2>Créer un nouveau thème</h2>
            <form method="post">
                <input type="text" name="theme_name" placeholder="Nom du thème" required>
                <button type="submit" name="add_theme">Créer</button>
                <button type="button" onclick="document.getElementById('theme-popup').style.display='none'">Annuler</button>
            </form>
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

        document.addEventListener("DOMContentLoaded", function() {
            let scoreData = <?php echo json_encode($scoreParGroupe); ?>;
            let labels = [...new Set(Object.values(scoreData).flat().map(item => item.date))];
            let datasets = Object.keys(scoreData).map(group => ({
                label: group,
                data: labels.map(date => {
                    let found = scoreData[group].find(entry => entry.date === date);
                    return found ? found.score : 0;
                }),
                borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
                fill: false
            }));

            new Chart(document.getElementById("scoreChart"), {
                type: 'line',
                data: {
                    labels,
                    datasets
                },
                options: {
                    responsive: true
                }
            });

            let quizData = <?php echo json_encode($topQuestionnaires); ?>;
            new Chart(document.getElementById("quizChart"), {
                type: 'bar',
                data: {
                    labels: quizData.map(q => q.libelle + ' (' + q.group_name + ')'),
                    datasets: [{
                        label: 'Score Moyen',
                        data: quizData.map(q => q.avg_score),
                        backgroundColor: 'blue'
                    }]
                }
            });
        });
    </script>

</body>

</html>