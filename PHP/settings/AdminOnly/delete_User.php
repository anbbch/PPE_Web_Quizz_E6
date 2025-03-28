<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../config.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['status'] !== 'Administrator') {
    header("Location: ../login.php");
    exit();
}


if (!isset($_GET['id'])) {
    die("ID utilisateur manquant.");
}

$id = intval($_GET['id']);
$conn = getDbConnection();

// Vérifier l'email de l'utilisateur
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

if ($email === $_COOKIE['username']) {
    header("Location: manageUsers.php?error=self-delete");
    exit();
}

// Suppression de l'utilisateur
$delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete_stmt->bind_param("i", $id);
$delete_stmt->execute();
$delete_stmt->close();
$conn->close();

// Redirection après suppression
header("Location: manageUsers.php");
exit();
