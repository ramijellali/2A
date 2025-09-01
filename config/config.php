<?php
/**
 * Configuration générale de l'application
 */

// Configuration de l'environnement
define('APP_ENV', 'development'); // development, production
define('APP_DEBUG', true);
define('APP_NAME', 'Système de Gestion d\'Enquêtes');
define('APP_VERSION', '1.0.0');

// Configuration des chemins
define('BASE_PATH', __DIR__ . '/..');
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Configuration de sécurité
define('SESSION_LIFETIME', 7200); // 2 heures
define('CSRF_TOKEN_LIFETIME', 3600); // 1 heure
define('PASSWORD_MIN_LENGTH', 6);

// Configuration de l'application
define('DEFAULT_CONTROLLER', 'Auth');
define('DEFAULT_ACTION', 'login');

// Configuration des emails (pour les notifications)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', 'noreply@enquetes.local');
define('SMTP_FROM_NAME', 'Système d\'Enquêtes');

// Configuration de pagination
define('ITEMS_PER_PAGE', 20);

// Configuration des fichiers uploads (si nécessaire)
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Configuration des logs
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_FILE', BASE_PATH . '/logs/app.log');

// Timezone
date_default_timezone_set('Europe/Paris');

// Configuration de session
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Mettre à 1 en HTTPS
ini_set('session.use_strict_mode', 1);

// Configuration d'erreur selon l'environnement
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/php_errors.log');
}

// Configuration des headers de sécurité
if (!headers_sent()) {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
