<?php
session_start();

// Simuler une connexion client
$_SESSION['user'] = [
    'id' => 3,
    'nom' => 'Moreau',
    'prenom' => 'Sophie',
    'email' => 'sophie.moreau@client.com',
    'role' => 'client'
];

echo "<h1>Session client créée</h1>";
echo "<p>Utilisateur connecté : " . $_SESSION['user']['prenom'] . " " . $_SESSION['user']['nom'] . "</p>";
echo "<p>Rôle : " . $_SESSION['user']['role'] . "</p>";
echo "<p><a href='/client_dashboard.php'>Accéder au dashboard client (version directe)</a></p>";
echo "<p><a href='/?controller=Client&action=dashboard'>Accéder au dashboard client (via contrôleur)</a></p>";
?>
