<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config.php';

// Vérifier si l'utilisateur est authentifié et Administrateur
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['status'] !== 'Administrator') {
    header("Location: ../../login.php");
    exit();
}

$is_admin = isset($_SESSION['status']) && $_SESSION['status'] === 'Administrator';

$conn = getDbConnection();
// Traitement du changement de statut
if (isset($_GET['change_status'])) {
    $id = intval($_GET['change_status']);

    // Vérifier le statut actuel
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    $new_status = ($status === 'Administrator') ? 'User' : 'Administrator';

    // Mise à jour du statut
    $update_stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $id);
    $update_stmt->execute();
    $update_stmt->close();

    header("Location: manageUsers.php");
    exit();
}
// Traitement de la suppression d'utilisateur
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);

    // Vérifier l'email de l'utilisateur
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if ($email === $_SESSION['username']) {
        die("Vous ne pouvez pas vous auto-supprimer.");
    }

    // Suppression de l'utilisateur
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    $delete_stmt->execute();
    $delete_stmt->close();

    header("Location: manageUsers.php");
    exit();
}

// Mettre à jour le groupe d'un utilisateur
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_group'])) {
    $user_id = intval($_POST['user_id']);
    $groupe_id = intval($_POST['groupes_id']);

    $stmt = $conn->prepare("UPDATE users SET groupes_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $groupe_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manageUsers.php");
    exit();
}

// Récupérer les groupes disponibles
$query_groupes = "SELECT id, name FROM groupes";
$result_groupes = $conn->query($query_groupes);
$groupes = [];
if ($result_groupes && $result_groupes->num_rows > 0) {
    while ($row = $result_groupes->fetch_assoc()) {
        $groupes[] = $row;
    }
}

// Récupérer la liste des utilisateurs avec leur groupe
$query = "SELECT users.id, users.name, users.email, users.status, users.groupes_id, groupes.name AS groupe_nom 
          FROM users 
          LEFT JOIN groupes ON users.groupes_id = groupes.id
          ORDER BY status DESC, name ASC";
$result = $conn->query($query);
$users = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="../../CSS/manageAdmin.css">
</head>

<body>
    <h2>Liste des Utilisateurs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Groupe</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['status']) ?></td>
                    <td>
                        <form method="POST" action="manageUsers.php">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="groupes_id" onchange="this.form.submit()">
                                <option value="">Aucun</option>
                                <?php foreach ($groupes as $groupe): ?>
                                    <option value="<?= $groupe['id'] ?>" <?= ($user['groupes_id'] == $groupe['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($groupe['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="update_group" value="1">
                        </form>
                    </td>
                    <td>
                        <?php if ($user['email'] !== $_SESSION['username']): ?>
                            <a href="manageUsers.php?change_status=<?= $user['id'] ?>" class="btn">Changer Statut</a>
                            <a href="manageUsers.php?delete_user=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                        <?php else: ?>
                            <span>(Vous)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <div class="back-home">
        <a href="<?= $is_admin ? '../homeAdmin.php' : '../homeUser.php' ?>" class="btn btn-secondary">Menu</a>
    </div>

</body>

</html>