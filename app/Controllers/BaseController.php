<?php
/**
 * Contrôleur de base
 * Fournit les fonctionnalités communes à tous les contrôleurs
 */

abstract class BaseController {
    protected $currentUser;
    
    public function __construct() {
        $this->checkAuth();
        $this->currentUser = $_SESSION['user'] ?? null;
    }
    
    /**
     * Vérifie l'authentification (peut être surchargée)
     */
    protected function checkAuth(): void {
        // Par défaut, pas de vérification
    }
    
    /**
     * Redirige vers une URL
     */
    protected function redirect(string $url): void {
        header("Location: " . $url);
        exit;
    }
    
    /**
     * Charge une vue
     */
    protected function view(string $viewPath, array $data = []): void {
        // Ajouter currentUser aux données disponibles dans toutes les vues
        $data['currentUser'] = $this->currentUser;
        
        // Extraction des données pour les rendre disponibles dans la vue
        extract($data);
        
        // Détermine le template selon le rôle de l'utilisateur
        $template = $this->getTemplate();
        
        // Charge la vue dans le template
        $content = $this->loadView($viewPath, $data);
        
        include __DIR__ . "/../Views/{$template}/layout.php";
    }
    
    /**
     * Charge une vue sans template
     */
    protected function loadView(string $viewPath, array $data = []): string {
        // Ajouter currentUser aux données disponibles
        $data['currentUser'] = $this->currentUser;
        extract($data);
        ob_start();
        include __DIR__ . "/../Views/{$viewPath}.php";
        return ob_get_clean();
    }
    
    /**
     * Détermine le template à utiliser
     */
    protected function getTemplate(): string {
        if ($this->currentUser) {
            return ($this->currentUser['role'] === 'client') ? 'frontoffice' : 'backoffice';
        }
        return 'frontoffice';
    }
    
    /**
     * Retourne une réponse JSON
     */
    protected function json(array $data, int $statusCode = 200): void {
        // Nettoyer le buffer de sortie pour éviter les caractères BOM
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Valide les données CSRF
     */
    protected function validateCSRF(): bool {
        return isset($_POST['csrf_token']) && 
               isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
    
    /**
     * Génère un token CSRF
     */
    protected function generateCSRF(): string {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    /**
     * Nettoie les données d'entrée
     */
    protected function sanitize(array $data): array {
        $clean = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Seulement trim, pas d'encodage HTML pour les données de base
                $clean[$key] = trim($value);
            } else {
                $clean[$key] = $value;
            }
        }
        return $clean;
    }
    
    /**
     * Ajoute un message flash
     */
    protected function flash(string $type, string $message): void {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }
    
    /**
     * Récupère et efface les messages flash
     */
    protected function getFlashMessages(): array {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
    
    /**
     * Vérifie les permissions selon le rôle
     */
    protected function hasPermission(array $allowedRoles): bool {
        return $this->currentUser && in_array($this->currentUser['role'], $allowedRoles);
    }
    
    /**
     * Retourne une erreur 403 si pas d'autorisation
     */
    protected function requirePermission(array $allowedRoles): void {
        if (!$this->hasPermission($allowedRoles)) {
            http_response_code(403);
            $this->view('errors/403');
            exit;
        }
    }
}
