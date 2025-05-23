<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config.php';
include '../menu.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['id']; // Assurez-vous que l'ID de l'utilisateur est stocké dans la session
$username = $_SESSION['username']; // Assurez-vous que le nom d'utilisateur est dans la session
$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';

if (!$is_admin) {
    header("Location: homeUser.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../../login.php");
    exit();
}

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Exemple pour récupérer les scores cumulés par groupe et date
$sql = "SELECT g.name AS group_name, DATE(h.date_reponse) as date_reponse, SUM(h.score) AS total_score
        FROM historiqueUtilisateur h
        JOIN users u ON h.user_id = u.id
        JOIN groupes g ON u.groupes_id = g.id
        WHERE h.user_id = ? AND DATE(h.date_reponse) >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY g.name, DATE(h.date_reponse)
        ORDER BY DATE(h.date_reponse) ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Assurez-vous que user_id est un entier
$stmt->execute();
$result = $stmt->get_result();

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
        WHERE u.id = $user_id
        GROUP BY q.libelle, g.name
        ORDER BY avg_score DESC LIMIT 5";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $topQuestionnaires[] = $row;
}
// Récupérer les 3 meilleurs utilisateurs du même groupe
$topUsers = [];
$sql = "SELECT u.username, SUM(h.score) AS total_score
        FROM historiqueUtilisateur h
        JOIN users u ON h.user_id = u.id
        WHERE u.groupes_id = (SELECT groupes_id FROM users WHERE id = $user_id)
        GROUP BY u.id
        ORDER BY total_score DESC LIMIT 5";
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../CSS/homeAdmin.css">
    <title>Tableau de bord Admin</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Bonjour, <?= htmlspecialchars($username) ?> !</h1>

        <div class="row">
            <!-- Progression des Scores  de l'utilisateur par rapport a son groupe -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-brown text-white">
                        <h4>Progression des Scores de <?= htmlspecialchars($username) ?> par Groupe</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="scoreChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Questionnaires de l'utilisateur-->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-brown text-white">
                        <h4>Top Questionnaires pour <?= htmlspecialchars($username) ?> par Groupe</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="quizChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top 5 Utilisateurs de son groupe-->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-brown text-white">
                        <h4>Top 3 Utilisateurs de votre Groupe</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($topUsers as $user) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($user['username']) ?>
                                    <span class="badge badge-primary badge-pill"><?= $user['total_score'] ?></span>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let scoreData = <?php echo json_encode($scoreParGroupe); ?>;
        let labels = [...new Set(Object.values(scoreData).flat().map(item => item.date_reponse))];
        let datasets = Object.keys(scoreData).map(group => ({
            label: group,
            data: labels.map(date => {
                let found = scoreData[group].find(entry => entry.date_reponse === date);
                return found ? found.score : 0;
            }),
            borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
            fill: false
        }));

        // Initialisation du graphique
        new Chart(document.getElementById("scoreChart"), {
            type: 'line',
            data: {
                labels: labels, // Utilisation des dates comme labels
                datasets: datasets // Les datasets contenant les scores par groupe
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'category', // Les dates sont des catégories ici
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Score'
                        }
                    }
                }
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

    window.addEventListener('resize', function() {
        const chart = document.getElementById('quizChart');
        if (chart) {
            chart.style.width = `${window.innerWidth * 0.8}px`;
            chart.style.height = `${window.innerHeight * 0.5}px`;
        }
    });
</script>

</html>