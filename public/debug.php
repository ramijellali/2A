<?php
echo "<h1>Debug de l'application</h1>";

// Vérifier si le fichier bootstrap existe
if (file_exists(__DIR__ . '/../config/bootstrap.php')) {
    echo "<p>✅ Le fichier bootstrap.php existe</p>";
    
    try {
        require_once __DIR__ . '/../config/bootstrap.php';
        echo "<p>✅ Bootstrap chargé avec succès</p>";
        
        if (class_exists('Application')) {
            echo "<p>✅ La classe Application existe</p>";
            
            // Tenter de créer une instance
            $app = new Application();
            echo "<p>✅ Instance Application créée</p>";
            
        } else {
            echo "<p>❌ La classe Application n'existe pas</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Erreur lors du chargement du bootstrap : " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p>❌ Le fichier bootstrap.php n'existe pas</p>";
}

echo "<p><a href='/'>⬅️ Retour à l'application</a></p>";
?>
