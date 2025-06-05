<?php
session_start();

// //Définir les paramètres de configuration locale:
// define('DB_SERVER', 'localhost');
// define('DB_USERNAME', 'anya');
// define('DB_PASSWORD', '');
// define('DB_NAME', 'quizz');


//Connexion distance :

// define('DB_SERVER', '104.40.137.99:22260');
// define('DB_USERNAME', 'developer');
// define('DB_PASSWORD', 'cerfal1313');
// define('DB_NAME', 'belletable_anya');

define('DB_SERVER', 'gtc.boubchir.eu');
define('DB_USERNAME', 'juryBTS');
define('DB_PASSWORD', '!5Y;H4w*j4rHv4');
define('DB_NAME', 'quizz_anya');

define('URL', '/QUIZZ_V2/');

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
