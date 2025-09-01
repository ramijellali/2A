<?php
/**
 * Index de debug pour identifier l'erreur 500
 */

// Activer tous les types d'erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Buffer d'output pour capturer les erreurs
ob_start();

echo "<h1>Debug Application - Index</h1>";

try {
    // Afficher les paramètres de la requête
    echo "<h2>Paramètres de la requête :</h2>";
    echo "Controller: " . ($_GET['controller'] ?? 'non défini') . "<br>";
    echo "Action: " . ($_GET['action'] ?? 'non défini') . "<br>";
    
    // Démarrage de session si pas encore fait
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    echo "<h2>Informations de session :</h2>";
    if (isset($_SESSION['user'])) {
        echo "Utilisateur connecté: " . $_SESSION['user']['email'] . " (rôle: " . $_SESSION['user']['role'] . ")<br>";
    } else {
        echo "Aucun utilisateur connecté<br>";
    }
    
    // Test de chargement du bootstrap
    echo "<h2>Chargement du bootstrap :</h2>";
    echo "Tentative de chargement...<br>";
    
    require_once __DIR__ . '/../config/bootstrap.php';
    echo "Bootstrap chargé avec succès ✅<br>";
    
    // Test de création de l'application
    echo "<h2>Création de l'application :</h2>";
    echo "Tentative de création...<br>";
    
    $app = new Application();
    echo "Application créée avec succès ✅<br>";
    
    echo "<h2>L'application a été créée sans erreur !</h2>";
    
} catch (ParseError $e) {
    echo "<h2>❌ Erreur de syntaxe PHP :</h2>";
    echo "<strong>Message :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . "<br>";
    echo "<strong>Ligne :</strong> " . $e->getLine() . "<br>";
    echo "<pre>Trace :\n" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<h2>❌ Erreur fatale PHP :</h2>";
    echo "<strong>Message :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . "<br>";
    echo "<strong>Ligne :</strong> " . $e->getLine() . "<br>";
    echo "<pre>Trace :\n" . $e->getTraceAsString() . "</pre>";
} catch (Exception $e) {
    echo "<h2>❌ Exception :</h2>";
    echo "<strong>Message :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . "<br>";
    echo "<strong>Ligne :</strong> " . $e->getLine() . "<br>";
    echo "<pre>Trace :\n" . $e->getTraceAsString() . "</pre>";
} catch (Throwable $e) {
    echo "<h2>❌ Erreur générale :</h2>";
    echo "<strong>Message :</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Type :</strong> " . get_class($e) . "<br>";
    echo "<strong>Fichier :</strong> " . $e->getFile() . "<br>";
    echo "<strong>Ligne :</strong> " . $e->getLine() . "<br>";
    echo "<pre>Trace :\n" . $e->getTraceAsString() . "</pre>";
}

// Afficher le contenu du buffer
$output = ob_get_clean();
echo $output;
?>
