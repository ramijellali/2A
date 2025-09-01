<?php
/**
 * Test de connexion à la base de données
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo "<h1>✅ Connexion à la base de données réussie !</h1>";
    
    // Test simple de comptage des utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM utilisateurs");
    $result = $stmt->fetch();
    echo "<p>Nombre d'utilisateurs dans la base : " . $result['count'] . "</p>";
    
    // Affichage des comptes de test
    echo "<h2>Comptes de démonstration :</h2>";
    echo "<ul>";
    echo "<li><strong>Admin :</strong> admin@enquetes.com / password</li>";
    echo "<li><strong>Agent :</strong> marie.dupont@enquetes.com / password</li>";
    echo "<li><strong>Client :</strong> sophie.moreau@client.com / password</li>";
    echo "</ul>";
    
    echo "<p><a href='/'>⬅️ Retour à l'application</a></p>";
    
} catch (Exception $e) {
    echo "<h1>❌ Erreur de connexion à la base de données</h1>";
    echo "<p>Erreur : " . $e->getMessage() . "</p>";
}
?>
