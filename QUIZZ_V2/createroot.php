<?php
// create_root_user.php

require_once 'config.php';

// Le mot de passe que vous voulez utiliser pour l'utilisateur root
$password = 'a';

// Générer un sel aléatoire de 64 caractères
$salt = bin2hex(random_bytes(32));

// Hash le mot de passe avec le sel en utilisant SHA256
$hashed_password = hash('sha256', $salt . $password);

$status = 'Administrator';

// Connexion à la base de données
$conn = getDbConnection();

// Insérer l'utilisateur root avec le mot de passe hashé et le sel
$stmt = $conn->prepare("INSERT INTO users (name, username, password, salt, status) VALUES (?, ?, ?, ?, ?)");
$name = 'Root User';
$login = 'Root';
$stmt->bind_param("sssss", $name, $login, $hashed_password, $salt, $status);

if ($stmt->execute()) {
    echo "Root user created successfully.";
} else {
    echo "Error creating root user.";
}

$stmt->close();
$conn->close();
?>
