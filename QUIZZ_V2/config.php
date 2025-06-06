<?php
session_start();

// //Définir les paramètres de configuration locale:
// define('DB_SERVER', 'localhost');
// define('DB_USERNAME', 'anya');
// define('DB_PASSWORD', '');
// define('DB_NAME', 'quizz');


//Connexion distance :

//MySQLWorkbench CFA:

// define('DB_SERVER', '104.40.137.99:22260');
// define('DB_USERNAME', 'developer');
// define('DB_PASSWORD', 'cerfal1313');
// define('DB_NAME', 'belletable_anya');

//PHPMyAdmin Anya:

// define('DB_SERVER', 'gtc.boubchir.eu');
// define('DB_USERNAME', 'juryBTS');
// define('DB_PASSWORD', '!5Y;H4w*j4rHv4');
// define('DB_NAME', 'quizz_anya');

//PHPMyAdmin Alexis:

define('DB_SERVER', 'db-fde-02.sparkedhost.us:3306');
define('DB_USERNAME', 'u79805_x7JaJKQqb9');
define('DB_PASSWORD', 'rmHi03H0=BaOL96Y.XRmPNkY');
define('DB_NAME', 's79805_BTSSIO_ANYA');

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
