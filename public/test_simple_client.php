<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test Simple ClientController</h1>";

try {
    // Démarrage de session
    session_start();
    
    // Simulation utilisateur connecté
    $_SESSION['user'] = [
        'id' => 3,
        'nom' => 'Moreau',
        'prenom' => 'Sophie',
        'email' => 'sophie.moreau@client.com',
        'role' => 'client'
    ];
    
    echo "Session créée ✅<br>";
    
    // Chargement minimal des fichiers
    require_once __DIR__ . '/../config/config.php';
    echo "Config chargé ✅<br>";
    
    require_once __DIR__ . '/../config/database.php';
    echo "Database chargé ✅<br>";
    
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    echo "BaseModel chargé ✅<br>";
    
    require_once __DIR__ . '/../app/Controllers/BaseController.php';
    echo "BaseController chargé ✅<br>";
    
    require_once __DIR__ . '/../app/Models/Enquete.php';
    echo "Enquete model chargé ✅<br>";
    
    require_once __DIR__ . '/../app/Controllers/ClientController.php';
    echo "ClientController chargé ✅<br>";
    
    // Test de création
    $client = new ClientController();
    echo "ClientController instancié ✅<br>";
    
} catch (Throwable $e) {
    echo "❌ <strong>Erreur :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
