<?php
/**
 * Contrôleur d'authentification
 * Gère la connexion, déconnexion et inscription
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Utilisateur.php';

class AuthController extends BaseController {
    private $utilisateurModel;
    
    public function __construct() {
        $this->utilisateurModel = new Utilisateur();
        // Pas de vérification d'auth pour ce contrôleur
    }
    
    /**
     * Affiche la page de connexion
     */
    public function login(): void {
        if (isset($_SESSION['user'])) {
            $this->redirectToDashboard();
            return;
        }
        
        $csrfToken = $this->generateCSRF();
        $this->view('auth/login', [
            'csrf_token' => $csrfToken,
            'error' => $_SESSION['login_error'] ?? null
        ]);
        unset($_SESSION['login_error']);
    }
    
    /**
     * Traite la connexion
     */
    public function authenticate(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=Auth&action=login');
            return;
        }
        
        if (!$this->validateCSRF()) {
            $_SESSION['login_error'] = 'Token de sécurité invalide';
            $this->redirect('?controller=Auth&action=login');
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email et mot de passe requis';
            $this->redirect('?controller=Auth&action=login');
            return;
        }
        
        $user = $this->utilisateurModel->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user'] = $user;
            $this->redirectToDashboard();
        } else {
            $_SESSION['login_error'] = 'Email ou mot de passe incorrect';
            $this->redirect('?controller=Auth&action=login');
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout(): void {
        session_destroy();
        $this->redirect('?controller=Auth&action=login');
    }
    
    /**
     * Affiche la page d'inscription (pour les clients)
     */
    public function register(): void {
        if (isset($_SESSION['user'])) {
            $this->redirectToDashboard();
            return;
        }
        
        $csrfToken = $this->generateCSRF();
        $this->view('auth/register', [
            'csrf_token' => $csrfToken,
            'errors' => $_SESSION['register_errors'] ?? [],
            'old_data' => $_SESSION['old_data'] ?? []
        ]);
        unset($_SESSION['register_errors'], $_SESSION['old_data']);
    }
    
    /**
     * Traite l'inscription
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=Auth&action=register');
            return;
        }
        
        if (!$this->validateCSRF()) {
            $_SESSION['register_errors'] = ['csrf' => 'Token de sécurité invalide'];
            $this->redirect('?controller=Auth&action=register');
            return;
        }
        
        $data = $this->sanitize($_POST);
        $errors = $this->validateRegistration($data);
        
        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('?controller=Auth&action=register');
            return;
        }
        
        try {
            $userId = $this->utilisateurModel->createUser([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'mot_de_passe' => $data['password'],
                'role' => 'client'
            ]);
            
            $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            $this->redirect('?controller=Auth&action=login');
        } catch (Exception $e) {
            $_SESSION['register_errors'] = ['general' => 'Erreur lors de l\'inscription'];
            $this->redirect('?controller=Auth&action=register');
        }
    }
    
    /**
     * Valide les données d'inscription
     */
    private function validateRegistration(array $data): array {
        $errors = [];
        
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est obligatoire';
        }
        
        if (empty($data['prenom'])) {
            $errors['prenom'] = 'Le prénom est obligatoire';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est obligatoire';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format d\'email invalide';
        } elseif ($this->utilisateurModel->emailExists($data['email'])) {
            $errors['email'] = 'Cet email est déjà utilisé';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Le mot de passe est obligatoire';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
        }
        
        if ($data['password'] !== ($data['password_confirmation'] ?? '')) {
            $errors['password_confirmation'] = 'La confirmation du mot de passe ne correspond pas';
        }
        
        return $errors;
    }
    
    /**
     * Redirige vers le tableau de bord selon le rôle
     */
    private function redirectToDashboard(): void {
        $role = $_SESSION['user']['role'];
        
        switch ($role) {
            case 'admin':
                $this->redirect('?controller=Admin&action=dashboard');
                break;
            case 'agent':
                $this->redirect('?controller=Agent&action=dashboard');
                break;
            case 'client':
                $this->redirect('?controller=Client&action=dashboard');
                break;
            default:
                $this->redirect('?controller=Auth&action=login');
        }
    }
}
