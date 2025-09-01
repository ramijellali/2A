<?php
/**
 * Fonctions utilitaires pour l'application
 */

class Utils {
    
    /**
     * Génère un token CSRF sécurisé
     */
    public static function generateCSRFToken(): string {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || 
            time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_LIFETIME) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valide un token CSRF
     */
    public static function validateCSRFToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && 
               isset($_SESSION['csrf_token_time']) &&
               time() - $_SESSION['csrf_token_time'] <= CSRF_TOKEN_LIFETIME &&
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Nettoie et valide une adresse email
     */
    public static function sanitizeEmail(string $email): ?string {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }
    
    /**
     * Génère un mot de passe aléatoire
     */
    public static function generatePassword(int $length = 12): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle($chars), 0, $length);
    }
    
    /**
     * Hash un mot de passe de manière sécurisée
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Vérifie un mot de passe contre son hash
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Échappe les caractères HTML
     */
    public static function escape(string $text): string {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Formate une date pour l'affichage
     */
    public static function formatDate(string $date, string $format = 'd/m/Y H:i'): string {
        return date($format, strtotime($date));
    }
    
    /**
     * Calcule le temps écoulé depuis une date
     */
    public static function timeAgo(string $datetime): string {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'À l\'instant';
        if ($time < 3600) return floor($time/60) . ' min';
        if ($time < 86400) return floor($time/3600) . ' h';
        if ($time < 2592000) return floor($time/86400) . ' j';
        if ($time < 31104000) return floor($time/2592000) . ' mois';
        
        return floor($time/31104000) . ' an' . (floor($time/31104000) > 1 ? 's' : '');
    }
    
    /**
     * Valide un numéro de téléphone français
     */
    public static function validatePhone(string $phone): bool {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return preg_match('/^0[1-9][0-9]{8}$/', $phone);
    }
    
    /**
     * Formate un numéro de téléphone
     */
    public static function formatPhone(string $phone): string {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) {
            return substr($phone, 0, 2) . ' ' . 
                   substr($phone, 2, 2) . ' ' . 
                   substr($phone, 4, 2) . ' ' . 
                   substr($phone, 6, 2) . ' ' . 
                   substr($phone, 8, 2);
        }
        return $phone;
    }
    
    /**
     * Génère une URL sécurisée pour les liens
     */
    public static function url(string $controller, string $action = 'index', array $params = []): string {
        $url = "?controller={$controller}&action={$action}";
        foreach ($params as $key => $value) {
            $url .= "&{$key}=" . urlencode($value);
        }
        return $url;
    }
    
    /**
     * Redirige vers une URL
     */
    public static function redirect(string $url): void {
        header("Location: " . $url);
        exit;
    }
    
    /**
     * Valide un fichier uploadé
     */
    public static function validateUpload(array $file): array {
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Erreur lors de l\'upload';
            return $errors;
        }
        
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $errors[] = 'Fichier trop volumineux';
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_FILE_TYPES)) {
            $errors[] = 'Type de fichier non autorisé';
        }
        
        return $errors;
    }
    
    /**
     * Génère un nom de fichier unique
     */
    public static function generateFileName(string $originalName): string {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    /**
     * Convertit les tailles en format lisible
     */
    public static function formatBytes(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Valide une date
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Génère une couleur aléatoire en hexadécimal
     */
    public static function randomColor(): string {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Vérifie si une chaîne contient seulement des lettres et espaces
     */
    public static function isValidName(string $name): bool {
        return preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/', $name);
    }
    
    /**
     * Nettoie une chaîne pour les URLs (slug)
     */
    public static function slugify(string $text): string {
        $text = preg_replace('/[^\\pL\d]+/u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('/[^-\w]+/', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('/-+/', '-', $text);
        return strtolower($text);
    }
    
    /**
     * Log une message selon le niveau
     */
    public static function log(string $message, string $level = 'INFO'): void {
        if (!defined('LOG_FILE')) return;
        
        $logLevels = ['DEBUG', 'INFO', 'WARNING', 'ERROR'];
        $currentLevel = array_search(LOG_LEVEL, $logLevels);
        $messageLevel = array_search($level, $logLevels);
        
        if ($messageLevel >= $currentLevel) {
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;
            
            $logDir = dirname(LOG_FILE);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
        }
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }
    
    /**
     * Obtient l'utilisateur connecté
     */
    public static function getCurrentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public static function hasRole(string $role): bool {
        $user = self::getCurrentUser();
        return $user && $user['role'] === $role;
    }
    
    /**
     * Calcule la pagination
     */
    public static function calculatePagination(int $totalItems, int $currentPage = 1, int $itemsPerPage = ITEMS_PER_PAGE): array {
        $totalPages = ceil($totalItems / $itemsPerPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        return [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'items_per_page' => $itemsPerPage,
            'offset' => $offset,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages
        ];
    }
}
