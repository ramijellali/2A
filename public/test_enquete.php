<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test minimal ClientController</h1>";

try {
    // Session
    session_start();
    $_SESSION['user'] = [
        'id' => 3,
        'nom' => 'Moreau',
        'prenom' => 'Sophie', 
        'email' => 'sophie.moreau@client.com',
        'role' => 'client'
    ];
    echo "Session OK ✅<br>";
    
    // Chargement minimal des dépendances
    require_once __DIR__ . '/../config/config.php';
    echo "Config OK ✅<br>";
    
    require_once __DIR__ . '/../config/database.php';
    echo "Database OK ✅<br>";
    
    // Test connexion DB
    $db = Database::getInstance();
    echo "Connexion DB OK ✅<br>";
    
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    echo "BaseModel OK ✅<br>";
    
    require_once __DIR__ . '/../app/Models/Enquete.php';
    echo "Enquete model OK ✅<br>";
    
    // Test du modèle Enquete
    $enqueteModel = new Enquete();
    echo "Enquete instancié OK ✅<br>";
    
    // Test de la méthode getForClient
    $enquetes = $enqueteModel->getForClient(3);
    echo "getForClient(3) OK ✅ - Nombre d'enquêtes: " . count($enquetes) . "<br>";
    
    echo "<p><strong>Test réussi ! Le problème n'est pas dans Enquete.</strong></p>";
    
} catch (Throwable $e) {
    echo "❌ <strong>Erreur :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
