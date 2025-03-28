<?php
session_start();

// Définir les paramètres de configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'anya');
define('DB_PASSWORD', '');
define('DB_NAME', 'quizz');

function getDbConnection()
{
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Configuration de la durée de la session (30 minutes)
$sessionDuration = 30 * 60;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionDuration)) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();
