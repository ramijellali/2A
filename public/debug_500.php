<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Erreur 500</h1>";

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
    
    echo "1. Session utilisateur créée ✅<br>";
    
    // Chargement du bootstrap complet
    require_once __DIR__ . '/../config/bootstrap.php';
    echo "2. Bootstrap chargé ✅<br>";
    
    // Simulation de l'URL pour Client controller
    $_GET['controller'] = 'Client';
    $_GET['action'] = 'dashboard';
    
    echo "3. Paramètres GET définis ✅<br>";
    
    // Création de l'application
    echo "4. Création de l'application...<br>";
    $app = new Application();
    echo "5. Application créée ✅<br>";
    
} catch (ParseError $e) {
    echo "❌ <strong>Erreur de syntaxe :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "❌ <strong>Erreur fatale :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Exception $e) {
    echo "❌ <strong>Exception :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Throwable $e) {
    echo "❌ <strong>Erreur :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
