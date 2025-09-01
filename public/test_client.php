<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test ClientController</h1>";

try {
    // Chargement des dépendances
    require_once __DIR__ . '/../config/bootstrap.php';
    
    echo "Bootstrap chargé ✅<br>";
    
    // Test de création du contrôleur Client
    $clientController = new ClientController();
    echo "ClientController créé ✅<br>";
    
    // Simulation d'une session utilisateur client
    $_SESSION['user'] = [
        'id' => 3,
        'nom' => 'Moreau',
        'prenom' => 'Sophie',
        'email' => 'sophie.moreau@client.com',
        'role' => 'client'
    ];
    
    echo "Session utilisateur simulée ✅<br>";
    
    // Test de la méthode dashboard
    echo "Tentative d'appel de la méthode dashboard...<br>";
    ob_start();
    $clientController->dashboard();
    $output = ob_get_clean();
    
    echo "Méthode dashboard exécutée avec succès ✅<br>";
    echo "<hr>";
    echo "<h2>Sortie de la méthode dashboard :</h2>";
    echo $output;
    
} catch (Throwable $e) {
    echo "❌ <strong>Erreur :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
