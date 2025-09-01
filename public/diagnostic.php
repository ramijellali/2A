<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic approfondi</h1>";

try {
    echo "1. Test config... ";
    require_once __DIR__ . '/../config/config.php';
    echo "✅<br>";
    
    echo "2. Test utils... ";
    require_once __DIR__ . '/../config/utils.php';
    echo "✅<br>";
    
    echo "3. Test database... ";
    require_once __DIR__ . '/../config/database.php';
    echo "✅<br>";
    
    echo "4. Test BaseModel... ";
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    echo "✅<br>";
    
    echo "5. Test Utilisateur... ";
    require_once __DIR__ . '/../app/Models/Utilisateur.php';
    echo "✅<br>";
    
    echo "6. Test Question... ";
    require_once __DIR__ . '/../app/Models/Question.php';
    echo "✅<br>";
    
    echo "7. Test BaseController... ";
    require_once __DIR__ . '/../app/Controllers/BaseController.php';
    echo "✅<br>";
    
    echo "8. Test AuthController... ";
    require_once __DIR__ . '/../app/Controllers/AuthController.php';
    echo "✅<br>";
    
    echo "9. Test ClientController... ";
    require_once __DIR__ . '/../app/Controllers/ClientController.php';
    echo "✅<br>";
    
    echo "<p><strong>Tous les tests réussis !</strong></p>";
    
} catch (Throwable $e) {
    echo "❌<br><strong>Erreur :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
