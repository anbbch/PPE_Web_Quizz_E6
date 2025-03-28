<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../../config.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['status'] !== 'Administrator') {
    header("Location: ../login.php");
    exit();
}


if (!isset($_GET['id'])) {
    die("ID utilisateur manquant.");
}

$conn = getDbConnection();
$id = intval($_GET['id']);

// Vérifier le statut actuel
$stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

// Sécuriser le changement de statut
$new_status = (strcasecmp($status, 'Administrator') == 0) ? 'User' : 'Administrator';

// Mise à jour du statut
$update_stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
$update_stmt->bind_param("si", $new_status, $id);
$update_stmt->execute();
$update_stmt->close();
$conn->close();

// Redirection après mise à jour
header("Location: ../manageUsers.php");
exit();
?>
