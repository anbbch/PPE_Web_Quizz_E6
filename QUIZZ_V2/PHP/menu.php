<?php

// Vérifie si l'utilisateur est authentifié
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: " . URL . 'login.php');
    exit();
}

$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= URL . ($is_admin ? 'PHP/homeAdmin.php' : 'PHP/homeUser.php') ?>">Home</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL . ('PHP/settings/Users/changePassword.php') ?>">Changer de mot de passe</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL . ('PHP/settings/Users/changeInfo.php') ?>">Changer ses infos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL . ('PHP/settings/historique.php') ?>">Historique</a>
                </li>

                <?php if ($is_admin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . ('PHP/settings/createAdmin.php') ?>">Créer un utilisateur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . ('PHP/settings/manageUsers.php') ?>">Gérer les utilisateurs</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Statistiques
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="<?= URL . ('PHP/stats/stats_groupe.php') ?>">Statistiques 1</a></li>
                            <li><a class="dropdown-item" href="stats2.php">Statistiques 2</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <form method="post" style="display: inline;">
                        <button type="submit" name="logout" class="btn btn-link nav-link" style="padding: 8px; border: none; background: none; ">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>