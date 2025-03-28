<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../config.php';
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../login.php");
    exit();
}

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

// Vérification des données reçues
if (!isset($data['user_id'], $data['questionnaire_id'], $data['responses'])) {
    echo json_encode(["status" => "error", "message" => "Données invalides"]);
    exit();
}

$user_id = intval($data['user_id']);
$questionnaire_id = intval($data['questionnaire_id']); // Utilisation de l'ID du questionnaire
$responses = $data['responses'];

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Échec de la connexion à la base de données"]));
}

// Préparation des valeurs pour l'INSERT multiple
$values = [];
foreach ($responses as $response) {
    $question_id = intval($response['question_id']);
    $question_text = $conn->real_escape_string($response['question_text']);
    $user_answer = $conn->real_escape_string($response['user_answer']);
    $correct_answer = $conn->real_escape_string($response['correct_answer']);
    $score = intval($response['score']);

    // Insertion des réponses dans l'historique
    $values[] = "($questionnaire_id, $user_id, '$question_text', '$user_answer', '$correct_answer', $score)";
}

$sql = "INSERT INTO historiqueUtilisateur (questionnaire_id, user_id, question, reponse, bonne_reponse, score) VALUES " . implode(", ", $values);

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Résultats enregistrés"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur SQL: " . $conn->error]);
}

$conn->close();
