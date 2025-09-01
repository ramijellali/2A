<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    echo "Chargement config...<br>";
    require_once __DIR__ . '/../config/config.php';
    
    echo "Chargement utils...<br>";
    require_once __DIR__ . '/../config/utils.php';
    
    echo "Chargement database...<br>";
    require_once __DIR__ . '/../config/database.php';
    
    echo "Chargement BaseController...<br>";
    require_once __DIR__ . '/../app/Controllers/BaseController.php';
    
    echo "Chargement AuthController...<br>";
    require_once __DIR__ . '/../app/Controllers/AuthController.php';
    
    echo "Tous les fichiers chargés avec succès !<br>";
    
} catch (Throwable $e) {
    echo "<strong>Erreur :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
