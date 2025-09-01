<?php
// Test complet avec les includes
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    echo "Chargement du config...<br>";
    require_once __DIR__ . '/../config/config.php';
    echo "Config chargé OK<br>";
    
    echo "Chargement du bootstrap...<br>";
    require_once __DIR__ . '/../config/bootstrap.php';
    echo "Bootstrap chargé OK<br>";
    
    echo "Création de l'application...<br>";
    $app = new Application();
    echo "Application créée OK<br>";
    
} catch (Throwable $e) {
    echo "<strong>Erreur :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
