<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test avec affichage des erreurs</h1>";

try {
    echo "<p>Chargement du bootstrap...</p>";
    require_once __DIR__ . '/../config/bootstrap.php';
    echo "<p>Bootstrap chargé.</p>";
    
    echo "<p>Création de l'application...</p>";
    $app = new Application();
    echo "<p>Application créée.</p>";
    
    echo "<p>Démarrage de l'application...</p>";
    Application::run();
    
} catch (Throwable $e) {
    echo "<p><strong>Erreur :</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
