<?php
/**
 * Bootstrap de l'application
 * Initialise les composants nécessaires
 */

// Chargement de la configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/utils.php';

/**
 * Classe principale de l'application
 */
class Application {
    private $controller;
    private $action;
    private $params;
    
    public function __construct() {
        $this->parseUrl();
        $this->initSession();
        $this->loadController();
    }
    
    /**
     * Parse l'URL pour extraire contrôleur et action
     */
    private function parseUrl(): void {
        $this->controller = $_GET['controller'] ?? DEFAULT_CONTROLLER;
        $this->action = $_GET['action'] ?? DEFAULT_ACTION;
        $this->params = array_diff_key($_GET, array_flip(['controller', 'action']));
        
        // Validation des noms de contrôleur et action
        if (!ctype_alpha($this->controller) || !ctype_alpha($this->action)) {
            $this->controller = DEFAULT_CONTROLLER;
            $this->action = DEFAULT_ACTION;
        }
    }
    
    /**
     * Initialise la session
     */
    private function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Régénérer l'ID de session périodiquement
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    /**
     * Charge et exécute le contrôleur
     */
    private function loadController(): void {
        $controllerName = ucfirst($this->controller) . 'Controller';
        $controllerFile = APP_PATH . '/Controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            $this->handleError(404, 'Contrôleur non trouvé');
            return;
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            $this->handleError(500, 'Classe du contrôleur non trouvée');
            return;
        }
        
        try {
            $controller = new $controllerName();
            
            if (!method_exists($controller, $this->action)) {
                $this->handleError(404, 'Action non trouvée');
                return;
            }
            
            // Exécuter l'action
            call_user_func_array([$controller, $this->action], []);
            
        } catch (Exception $e) {
            Utils::log('Erreur dans le contrôleur: ' . $e->getMessage(), 'ERROR');
            $this->handleError(500, 'Erreur interne du serveur');
        }
    }
    
    /**
     * Gère les erreurs de l'application
     */
    private function handleError(int $code, string $message): void {
        http_response_code($code);
        
        if (APP_DEBUG) {
            echo "<h1>Erreur {$code}</h1><p>{$message}</p>";
            if (isset($e)) {
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
        } else {
            // En production, afficher une page d'erreur personnalisée
            include APP_PATH . '/Views/errors/' . $code . '.php';
        }
        exit;
    }
    
    /**
     * Point d'entrée principal
     */
    public static function run(): void {
        new self();
    }
}

// Autoloader pour les classes
spl_autoload_register(function ($className) {
    // Chercher dans les modèles
    $modelFile = APP_PATH . '/Models/' . $className . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }
    
    // Chercher dans les contrôleurs
    $controllerFile = APP_PATH . '/Controllers/' . $className . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return;
    }
    
    // Chercher dans la configuration
    $configFile = CONFIG_PATH . '/' . $className . '.php';
    if (file_exists($configFile)) {
        require_once $configFile;
        return;
    }
});

// Gestion des erreurs globales
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    Utils::log("Erreur PHP: {$message} dans {$file}:{$line}", 'ERROR');
    
    if (APP_DEBUG) {
        echo "<b>Erreur:</b> {$message} dans <b>{$file}</b> ligne <b>{$line}</b><br>";
    }
    
    return true;
});

set_exception_handler(function ($exception) {
    Utils::log('Exception non capturée: ' . $exception->getMessage(), 'ERROR');
    
    if (APP_DEBUG) {
        echo "<h1>Exception non capturée</h1>";
        echo "<p><b>Message:</b> " . $exception->getMessage() . "</p>";
        echo "<p><b>Fichier:</b> " . $exception->getFile() . "</p>";
        echo "<p><b>Ligne:</b> " . $exception->getLine() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        include APP_PATH . '/Views/errors/500.php';
    }
    exit;
});

// Fonction de fermeture propre
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        Utils::log("Erreur fatale: {$error['message']} dans {$error['file']}:{$error['line']}", 'ERROR');
        
        if (!APP_DEBUG) {
            http_response_code(500);
            include APP_PATH . '/Views/errors/500.php';
        }
    }
});
