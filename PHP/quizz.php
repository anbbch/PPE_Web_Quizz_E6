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

if (!isset($_GET['questionnaire'])) {
    die("Thème non sélectionné.");
}

$theme_id = intval($_GET['questionnaire']);
if (isset($_SESSION['id'])) {
    $user_id = intval($_SESSION['id']);
} else {
    die("Erreur : utilisateur non authentifié.");
}


$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupération des questions du thème sélectionné
$sql = "SELECT id, conteneu, reponse, goodAnswer FROM questions WHERE questionnaire_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $theme_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

$stmt->close();
$conn->close();

// Mélanger les questions en PHP
shuffle($questions);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="stylesheet" href="../CSS/quizz.css">
</head>

<body>
    <div id="quiz-container">
        <h1>Quiz</h1>
        <p id="score">Score : 0</p>
        <div id="question-container">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question" data-index="<?= $index ?>" data-question-id="<?= $question['id'] ?>" style="<?= $index === 0 ? '' : 'display: none;' ?>">
                    <p><?= htmlspecialchars($question['conteneu']) ?></p>
                    <?php
                    $reponses = explode(";", $question['reponse']);

                    shuffle($reponses);
                    ?>
                    <?php foreach ($reponses as $reponse): ?>
                        <button class="answer-button" data-answer="<?= htmlspecialchars($reponse) ?>"
                            data-correct="<?= ($reponse === $question['goodAnswer']) ? 'true' : 'false' ?>">
                            <?= htmlspecialchars($reponse) ?>
                        </button>
                    <?php endforeach; ?>
                    <p class="correct-answer" style="display: none;">Bonne réponse : <strong><?= htmlspecialchars($question['goodAnswer']) ?></strong></p>
                </div>
            <?php endforeach; ?>
        </div>
        <button id="submit-results" style="display: none;">Enregistrer les résultats</button>
    </div>
    <div class="back-home">
        <a href="<?= $is_admin ? 'homeAdmin.php' : 'homeUser.php' ?>" class="btn btn-secondary">Menu</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let currentQuestionIndex = 0;
            let score = 0;
            let responses = [];
            const user_id = <?= $user_id ?>;
            const questionnaire_id = <?= $theme_id ?>;
            const questions = document.querySelectorAll(".question");
            const scoreDisplay = document.getElementById("score");
            const submitButton = document.getElementById("submit-results");

            function showQuestion(index) {
                questions.forEach((q, i) => {
                    q.style.display = i === index ? "block" : "none";
                });

                document.querySelectorAll(".question").forEach(q => {
                    let buttons = Array.from(q.querySelectorAll(".answer-button"));
                    buttons.sort(() => Math.random() - 0.5);
                    buttons.forEach(btn => q.appendChild(btn));
                });
            }

            function handleAnswerClick(event) {
                const selectedAnswer = event.target;
                const questionContainer = selectedAnswer.closest(".question");
                const correctAnswerElement = questionContainer.querySelector(".correct-answer");
                const answers = questionContainer.querySelectorAll(".answer-button");
                const question_id = questionContainer.dataset.questionId;
                const question_text = questionContainer.querySelector("p").innerText;
                const user_answer = selectedAnswer.innerText;
                const correct_answer = correctAnswerElement.querySelector("strong").innerText;

                answers.forEach(answer => answer.style.pointerEvents = "none");

                let question_score = 0;
                if (selectedAnswer.dataset.correct === "true") {
                    selectedAnswer.style.backgroundColor = "green";
                    score++;
                    question_score = 1;
                } else {
                    selectedAnswer.style.backgroundColor = "red";
                    correctAnswerElement.style.display = "block";
                }

                scoreDisplay.textContent = `Score : ${score}`;

                responses.push({
                    question_id: question_id,
                    question_text: question_text,
                    user_answer: user_answer,
                    correct_answer: correct_answer,
                    score: question_score
                });

                setTimeout(() => {
                    currentQuestionIndex++;
                    if (currentQuestionIndex < questions.length) {
                        showQuestion(currentQuestionIndex);
                    } else {
                        alert(`Quiz terminé ! Votre score : ${score}/${questions.length}`);
                        submitButton.style.display = "block";
                    }
                }, 1000);
            }

            document.querySelectorAll(".answer-button").forEach(button => {
                button.addEventListener("click", handleAnswerClick);
            });

            submitButton.addEventListener("click", function() {
                fetch("settings/enregistrer_reponse.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            user_id: user_id,
                            questionnaire_id: questionnaire_id,
                            responses: responses
                        })
                    })
                    .then(response => {
                        return response.text(); // Utiliser .text() pour vérifier le contenu brut de la réponse
                    })
                    .then(text => {
                        try {
                            const data = JSON.parse(text); // Puis essayer de parser le JSON
                            if (data.status === "success") {
                                alert("Résultats enregistrés avec succès !");
                                window.location.href = "home.php";
                            } else {
                                alert("Erreur lors de l'enregistrement : " + data.message);
                            }
                        } catch (e) {
                            console.error("Erreur de parsing JSON :", e);
                            console.log("Réponse brute du serveur :", text); // Afficher la réponse pour débogage
                        }
                    })
                    .catch(error => console.error("Erreur :", error));
            });

            showQuestion(currentQuestionIndex);
        });
    </script>
</body>

</html>