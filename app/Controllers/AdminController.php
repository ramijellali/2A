<?php
/**
 * Contrôleur Admin
 * Gère les fonctionnalités d'administration
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Utilisateur.php';
require_once __DIR__ . '/../Models/Campagne.php';
require_once __DIR__ . '/../Models/Enquete.php';
require_once __DIR__ . '/../Models/Question.php';
require_once __DIR__ . '/../Models/Reponse.php';

class AdminController extends BaseController {
    private $utilisateurModel;
    private $campagneModel;
    private $enqueteModel;
    private $questionModel;
    private $reponseModel;
    
    public function __construct() {
        parent::__construct();
        $this->requirePermission(['admin']);
        $this->utilisateurModel = new Utilisateur();
        $this->campagneModel = new Campagne();
        $this->enqueteModel = new Enquete();
        $this->questionModel = new Question();
        $this->reponseModel = new Reponse();
    }
    
    /**
     * Tableau de bord admin
     */
    public function dashboard(): void {
        $stats = [
            'users' => $this->utilisateurModel->getStatsByRole(),
            'campagnes' => [
                'total' => $this->campagneModel->count(),
                'active' => count($this->campagneModel->findWhere('statut', 'active'))
            ],
            'enquetes' => [
                'total' => $this->enqueteModel->count(),
                'active' => count($this->enqueteModel->findWhere('statut', 'active'))
            ]
        ];
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Liste des utilisateurs
     */
    public function users(): void {
        $users = $this->utilisateurModel->findAll();
        $this->view('admin/users/index', [
            'users' => $users,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Créer un utilisateur
     */
    public function createUser(): void {
        $csrfToken = $this->generateCSRF();
        $this->view('admin/users/create', [
            'csrf_token' => $csrfToken,
            'errors' => $_SESSION['user_errors'] ?? [],
            'old_data' => $_SESSION['old_data'] ?? []
        ]);
        unset($_SESSION['user_errors'], $_SESSION['old_data']);
    }
    
    /**
     * Sauvegarder un utilisateur
     */
    public function storeUser(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Admin&action=createUser');
            return;
        }
        
        $data = $this->sanitize($_POST);
        
        // Supprimer le token CSRF des données à insérer
        unset($data['csrf_token']);
        
        $errors = $this->validateUser($data);
        
        if (!empty($errors)) {
            $_SESSION['user_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('?controller=Admin&action=createUser');
            return;
        }
        
        try {
            $userId = $this->utilisateurModel->createUser($data);
            if ($userId) {
                $this->flash('success', 'Utilisateur créé avec succès');
                $this->redirect('?controller=Admin&action=users');
            } else {
                $this->flash('error', 'Erreur lors de la création - ID non retourné');
                $this->redirect('?controller=Admin&action=createUser');
            }
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la création: ' . $e->getMessage());
            $this->redirect('?controller=Admin&action=createUser');
        }
    }
    
    /**
     * Éditer un utilisateur
     */
    public function editUser(): void {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->utilisateurModel->findById($id);
        
        if (!$user) {
            $this->flash('error', 'Utilisateur non trouvé');
            $this->redirect('?controller=Admin&action=users');
            return;
        }
        
        $csrfToken = $this->generateCSRF();
        $this->view('admin/users/edit', [
            'user' => $user,
            'csrf_token' => $csrfToken,
            'errors' => $_SESSION['user_errors'] ?? [],
            'old_data' => $_SESSION['old_data'] ?? []
        ]);
        unset($_SESSION['user_errors'], $_SESSION['old_data']);
    }
    
    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(): void {
        $id = (int)($_POST['id'] ?? 0);
        
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Admin&action=editUser&id=' . $id);
            return;
        }
        
        $data = $this->sanitize($_POST);
        
        // Supprimer les champs non-DB des données à mettre à jour
        unset($data['csrf_token'], $data['id']);
        
        $errors = $this->validateUser($data, $id);
        
        if (!empty($errors)) {
            $_SESSION['user_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('?controller=Admin&action=editUser&id=' . $id);
            return;
        }
        
        try {
            $this->utilisateurModel->updateUser($id, $data);
            $this->flash('success', 'Utilisateur mis à jour avec succès');
            $this->redirect('?controller=Admin&action=users');
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la mise à jour');
            $this->redirect('?controller=Admin&action=editUser&id=' . $id);
        }
    }
    
    /**
     * Supprimer un utilisateur
     */
    public function deleteUser(): void {
        $id = (int)($_POST['id'] ?? 0);
        
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Admin&action=users');
            return;
        }
        
        if ($id === $this->currentUser['id']) {
            $this->flash('error', 'Vous ne pouvez pas supprimer votre propre compte');
            $this->redirect('?controller=Admin&action=users');
            return;
        }
        
        try {
            $this->utilisateurModel->delete($id);
            $this->flash('success', 'Utilisateur supprimé avec succès');
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la suppression');
        }
        
        $this->redirect('?controller=Admin&action=users');
    }
    
    /**
     * Liste des campagnes
     */
    public function campagnes(): void {
        $campagnes = $this->campagneModel->getAllWithCreator();
        $this->view('admin/campagnes/index', [
            'campagnes' => $campagnes,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Créer une campagne
     */
    public function createCampagne(): void {
        $csrfToken = $this->generateCSRF();
        $this->view('admin/campagnes/create', [
            'csrf_token' => $csrfToken,
            'errors' => $_SESSION['campagne_errors'] ?? [],
            'old_data' => $_SESSION['old_data'] ?? []
        ]);
        unset($_SESSION['campagne_errors'], $_SESSION['old_data']);
    }
    
    /**
     * Sauvegarder une nouvelle campagne
     */
    public function storeCampagne(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Admin&action=createCampagne');
            return;
        }
        
        $data = $this->sanitize($_POST);
        
        // Supprimer le token CSRF des données avant validation et stockage
        unset($data['csrf_token']);
        
        $errors = $this->validateCampagne($data);
        
        if (!empty($errors)) {
            $_SESSION['campagne_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('?controller=Admin&action=createCampagne');
            return;
        }
        
        try {
            // Ajouter l'ID du créateur
            $data['created_by'] = $this->currentUser['id'];
            
            $campagneId = $this->campagneModel->create($data);
            $this->flash('success', 'Campagne créée avec succès');
            $this->redirect('?controller=Admin&action=viewCampagne&id=' . $campagneId);
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la création : ' . $e->getMessage());
            $this->redirect('?controller=Admin&action=createCampagne');
        }
    }
    
    /**
     * Voir une campagne
     */
    public function viewCampagne(): void {
        $id = (int)($_GET['id'] ?? 0);
        $campagne = $this->campagneModel->getWithCreator($id);
        
        if (!$campagne) {
            $this->flash('error', 'Campagne introuvable');
            $this->redirect('?controller=Admin&action=campagnes');
            return;
        }
        
        $stats = $this->campagneModel->getStats($id);
        $enquetes = $this->enqueteModel->getByCampagne($id);
        
        $this->view('admin/campagnes/view', [
            'campagne' => $campagne,
            'stats' => $stats,
            'enquetes' => $enquetes,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Éditer une campagne
     */
    public function editCampagne(): void {
        $id = (int)($_GET['id'] ?? 0);
        $campagne = $this->campagneModel->findById($id);
        
        if (!$campagne) {
            $this->flash('error', 'Campagne introuvable');
            $this->redirect('?controller=Admin&action=campagnes');
            return;
        }
        
        $csrfToken = $this->generateCSRF();
        $this->view('admin/campagnes/edit', [
            'campagne' => $campagne,
            'csrf_token' => $csrfToken,
            'errors' => $_SESSION['campagne_errors'] ?? [],
            'old_data' => $_SESSION['old_data'] ?? []
        ]);
        unset($_SESSION['campagne_errors'], $_SESSION['old_data']);
    }
    
    /**
     * Mettre à jour une campagne
     */
    public function updateCampagne(): void {
        $id = (int)($_POST['id'] ?? 0);
        
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Admin&action=editCampagne&id=' . $id);
            return;
        }
        
        $data = $this->sanitize($_POST);
        unset($data['csrf_token'], $data['id']);
        
        $errors = $this->validateCampagne($data, $id);
        
        if (!empty($errors)) {
            $_SESSION['campagne_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('?controller=Admin&action=editCampagne&id=' . $id);
            return;
        }
        
        try {
            $this->campagneModel->update($id, $data);
            $this->flash('success', 'Campagne mise à jour avec succès');
            $this->redirect('?controller=Admin&action=viewCampagne&id=' . $id);
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
            $this->redirect('?controller=Admin&action=editCampagne&id=' . $id);
        }
    }
    
    /**
     * Supprimer une campagne
     */
    public function deleteCampagne(): void {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $this->flash('error', 'ID de campagne invalide');
            $this->redirect('?controller=Admin&action=campagnes');
            return;
        }
        
        try {
            // Vérifier si la campagne a des enquêtes actives
            $enquetes = $this->enqueteModel->getByCampagne($id);
            $enquetesActives = array_filter($enquetes, function($e) {
                return $e['statut'] === 'active';
            });
            
            if (count($enquetesActives) > 0) {
                $this->flash('error', 'Impossible de supprimer une campagne avec des enquêtes actives');
                $this->redirect('?controller=Admin&action=campagnes');
                return;
            }
            
            $this->campagneModel->delete($id);
            $this->flash('success', 'Campagne supprimée avec succès');
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
        
        $this->redirect('?controller=Admin&action=campagnes');
    }
    
    /**
     * Valide les données d'une campagne
     */
    private function validateCampagne(array $data, int $excludeId = null): array {
        $errors = [];
        
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est obligatoire';
        } elseif (strlen($data['nom']) < 3) {
            $errors['nom'] = 'Le nom doit contenir au moins 3 caractères';
        }
        
        if (empty($data['description'])) {
            $errors['description'] = 'La description est obligatoire';
        }
        
        if (empty($data['date_debut'])) {
            $errors['date_debut'] = 'La date de début est obligatoire';
        }
        
        if (empty($data['date_fin'])) {
            $errors['date_fin'] = 'La date de fin est obligatoire';
        }
        
        if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
            if (!$this->campagneModel->validateDates($data['date_debut'], $data['date_fin'])) {
                $errors['date_fin'] = 'La date de fin doit être postérieure à la date de début';
            }
        }
        
        if (!in_array($data['statut'] ?? '', ['en_preparation', 'active', 'terminee', 'suspendue'])) {
            $errors['statut'] = 'Statut invalide';
        }
        
        return $errors;
    }
    
    /**
     * Liste des enquêtes (pour l'admin)
     */
    public function enquetes(): void {
        // Récupérer toutes les enquêtes avec leurs informations complètes
        $enquetes = $this->enqueteModel->getAllWithDetails();
        
        $this->view('admin/enquetes/index', [
            'enquetes' => $enquetes,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Voir les détails d'une enquête (pour l'admin)
     */
    public function viewEnquete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $enquete = $this->enqueteModel->getWithDetails($id);
        
        if (!$enquete) {
            $this->flash('error', 'Enquête introuvable');
            $this->redirect('?controller=Admin&action=enquetes');
            return;
        }
        
        $questions = $this->questionModel->getByEnquete($id);
        $stats = $this->reponseModel->getStatsForEnquete($id);
        $assignedClients = $this->enqueteModel->getAssignedClients($id);
        
        $this->view('admin/enquetes/view', [
            'enquete' => $enquete,
            'questions' => $questions,
            'stats' => $stats,
            'assigned_clients' => $assignedClients,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }

    /**
     * Valide les données d'un utilisateur
     */
    private function validateUser(array $data, int $excludeId = null): array {
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
        } elseif ($this->utilisateurModel->emailExists($data['email'], $excludeId)) {
            $errors['email'] = 'Cet email est déjà utilisé';
        }
        
        if (!in_array($data['role'], ['admin', 'agent', 'client'])) {
            $errors['role'] = 'Rôle invalide';
        }
        
        if (!empty($data['mot_de_passe']) && strlen($data['mot_de_passe']) < 6) {
            $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères';
        }
        
        return $errors;
    }

    /**
     * Afficher toutes les questions du système
     */
    public function questions(): void {
        try {
            // Récupérer toutes les questions
            $questions = $this->questionModel->findAll();
            
            // Enrichir avec les informations d'enquête
            foreach ($questions as &$question) {
                $enquete = $this->enqueteModel->findById($question['enquete_id']);
                $question['enquete_titre'] = $enquete ? $enquete['titre'] : 'Enquête supprimée';
                $question['enquete_statut'] = $enquete ? $enquete['statut'] : 'inconnu';
                
                // Campagne
                if ($enquete && $enquete['campagne_id']) {
                    $campagne = $this->campagneModel->findById($enquete['campagne_id']);
                    $question['campagne_nom'] = $campagne ? $campagne['nom'] : 'Campagne supprimée';
                }
            }
            
            // Statistiques
            $stats = [
                'total' => count($questions),
                'par_type' => []
            ];
            
            foreach ($questions as $question) {
                $type = $question['type_question'];
                if (!isset($stats['par_type'][$type])) {
                    $stats['par_type'][$type] = 0;
                }
                $stats['par_type'][$type]++;
            }
            
            $this->view('admin/questions/index', [
                'questions' => $questions,
                'stats' => $stats,
                'flash_messages' => $this->getFlashMessages()
            ]);
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors du chargement des questions : ' . $e->getMessage());
            $this->redirect('?controller=Admin&action=dashboard');
        }
    }

    /**
     * Voir le détail d'une question
     */
    public function viewQuestion(): void {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $this->flash('error', 'ID de question invalide');
            $this->redirect('?controller=Admin&action=questions');
            return;
        }
        
        try {
            $question = $this->questionModel->findById($id);
            
            if (!$question) {
                $this->flash('error', 'Question non trouvée');
                $this->redirect('?controller=Admin&action=questions');
                return;
            }
            
            // Récupérer l'enquête associée
            $enquete = $this->enqueteModel->findById($question['enquete_id']);
            $question['enquete_titre'] = $enquete ? $enquete['titre'] : 'Enquête supprimée';
            
            // Récupérer les réponses pour cette question
            $reponses = $this->reponseModel->getByQuestion($id);
            
            // Enrichir les réponses avec les informations client et valeur consolidée
            foreach ($reponses as &$reponse) {
                // Consolider la valeur de réponse
                if (!empty($reponse['reponse_numerique'])) {
                    $reponse['valeur'] = (string)$reponse['reponse_numerique'];
                } else {
                    $reponse['valeur'] = $reponse['reponse_texte'] ?? '';
                }
                
                // Client (utiliser utilisateur_id)
                if (isset($reponse['utilisateur_id']) && $reponse['utilisateur_id']) {
                    $client = $this->utilisateurModel->findById($reponse['utilisateur_id']);
                    $reponse['client_nom'] = $client ? $client['prenom'] . ' ' . $client['nom'] : 'Client supprimé';
                    $reponse['client_id'] = $reponse['utilisateur_id']; // Pour compatibilité vue
                } else {
                    $reponse['client_nom'] = 'Client non défini';
                    $reponse['client_id'] = 0;
                }
            }
            
            $this->view('admin/questions/view', [
                'question' => $question,
                'reponses' => $reponses,
                'flash_messages' => $this->getFlashMessages()
            ]);
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors du chargement de la question : ' . $e->getMessage());
            $this->redirect('?controller=Admin&action=questions');
        }
    }

    /**
     * Afficher toutes les réponses du système
     */
    public function reponses(): void {
        try {
            // Récupérer toutes les réponses avec enrichissement
            $reponses = $this->reponseModel->findAll();
            
            // Enrichir avec les informations de question, enquête et client
            foreach ($reponses as &$reponse) {
                // Question
                $question = $this->questionModel->findById($reponse['question_id']);
                $reponse['question_texte'] = $question ? substr($question['texte'], 0, 60) . (strlen($question['texte']) > 60 ? '...' : '') : 'Question supprimée';
                $reponse['question_type'] = $question ? $question['type_question'] : 'inconnu';
                
                // Consolider la valeur de réponse
                if (!empty($reponse['reponse_numerique'])) {
                    $reponse['valeur'] = (string)$reponse['reponse_numerique'];
                } else {
                    $reponse['valeur'] = $reponse['reponse_texte'] ?? '';
                }
                
                // Enquête via question
                if ($question) {
                    $enquete = $this->enqueteModel->findById($question['enquete_id']);
                    $reponse['enquete_titre'] = $enquete ? $enquete['titre'] : 'Enquête supprimée';
                    $reponse['enquete_statut'] = $enquete ? $enquete['statut'] : 'inconnu';
                    
                    // Campagne via enquête
                    if ($enquete && $enquete['campagne_id']) {
                        $campagne = $this->campagneModel->findById($enquete['campagne_id']);
                        $reponse['campagne_nom'] = $campagne ? $campagne['nom'] : 'Campagne supprimée';
                    }
                }
                
                // Client (utiliser utilisateur_id)
                if (isset($reponse['utilisateur_id']) && $reponse['utilisateur_id']) {
                    $client = $this->utilisateurModel->findById($reponse['utilisateur_id']);
                    $reponse['client_nom'] = $client ? $client['prenom'] . ' ' . $client['nom'] : 'Client supprimé';
                } else {
                    $reponse['client_nom'] = 'Client non défini';
                }
            }
            
            // Tri par date décroissante
            usort($reponses, function($a, $b) {
                return strtotime($b['date_reponse']) - strtotime($a['date_reponse']);
            });
            
            // Statistiques
            $stats = [
                'total' => count($reponses),
                'par_type' => [],
                'derniere_semaine' => 0
            ];
            
            $uneSemaineAgo = time() - (7 * 24 * 60 * 60);
            
            foreach ($reponses as $reponse) {
                $type = $reponse['question_type'] ?? 'inconnu';
                if (!isset($stats['par_type'][$type])) {
                    $stats['par_type'][$type] = 0;
                }
                $stats['par_type'][$type]++;
                
                if (strtotime($reponse['date_reponse']) > $uneSemaineAgo) {
                    $stats['derniere_semaine']++;
                }
            }
            
            $this->view('admin/reponses/index', [
                'reponses' => $reponses,
                'stats' => $stats,
                'flash_messages' => $this->getFlashMessages()
            ]);
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors du chargement des réponses : ' . $e->getMessage());
            $this->redirect('?controller=Admin&action=dashboard');
        }
    }

    /**
     * Voir le détail d'une réponse
     */
    public function viewReponse(): void {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $this->flash('error', 'ID de réponse invalide');
            $this->redirect('?controller=Admin&action=reponses');
            return;
        }
        
        try {
            $reponse = $this->reponseModel->findById($id);
            
            if (!$reponse) {
                $this->flash('error', 'Réponse non trouvée');
                $this->redirect('?controller=Admin&action=reponses');
                return;
            }
            
            // Enrichir avec les informations contextuelles
            $question = $this->questionModel->findById($reponse['question_id']);
            $reponse['question_texte'] = $question ? $question['texte'] : 'Question supprimée';
            $reponse['question_type'] = $question ? $question['type_question'] : 'inconnu';
            
            // Consolider la valeur de réponse
            if (!empty($reponse['reponse_numerique'])) {
                $reponse['valeur'] = (string)$reponse['reponse_numerique'];
            } else {
                $reponse['valeur'] = $reponse['reponse_texte'] ?? '';
            }
            
            if ($question) {
                $enquete = $this->enqueteModel->findById($question['enquete_id']);
                $reponse['enquete_titre'] = $enquete ? $enquete['titre'] : 'Enquête supprimée';
                $reponse['enquete_id'] = $question['enquete_id'];
            }
            
            $client = $this->utilisateurModel->findById($reponse['utilisateur_id']);
            $reponse['client_nom'] = $client ? $client['prenom'] . ' ' . $client['nom'] : 'Client supprimé';
            $reponse['client_email'] = $client ? $client['email'] : 'Email inconnu';
            
            $this->view('admin/reponses/view', [
                'reponse' => $reponse,
                'flash_messages' => $this->getFlashMessages()
            ]);
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors du chargement de la réponse : ' . $e->getMessage());
            $this->redirect('?controller=Admin&action=reponses');
        }
    }
}
