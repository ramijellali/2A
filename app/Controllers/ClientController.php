<?php
/**
 * Contrôleur Client
 * Gère les fonctionnalités des clients
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Enquete.php';
require_once __DIR__ . '/../Models/Question.php';
require_once __DIR__ . '/../Models/Reponse.php';

class ClientController extends BaseController {
    private $enqueteModel;
    private $questionModel;
    private $reponseModel;
    
    public function __construct() {
        parent::__construct();
        $this->requirePermission(['client']);
        $this->enqueteModel = new Enquete();
        $this->questionModel = new Question();
        $this->reponseModel = new Reponse();
    }
    
    /**
     * Tableau de bord client
     */
    public function dashboard(): void {
        $clientId = $this->currentUser['id'];
        $enquetes = $this->enqueteModel->getForClient($clientId);
        
        $stats = [
            'enquetes_recues' => count($enquetes),
            'enquetes_completees' => count(array_filter($enquetes, 
                function($e) { return $e['participation_statut'] === 'complete'; })),
            'enquetes_en_cours' => count(array_filter($enquetes, 
                function($e) { return $e['participation_statut'] === 'envoye'; }))
        ];
        
        $this->view('client/dashboard', [
            'enquetes' => $enquetes,
            'stats' => $stats,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Alias pour viewEnquete - pour simplifier l'URL
     */
    public function enquete(): void {
        $this->viewEnquete();
    }
    
    /**
     * Voir une enquête spécifique
     */
    public function viewEnquete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $clientId = $this->currentUser['id'];
        
        // Vérifier que le client a accès à cette enquête
        $enquetes = $this->enqueteModel->getForClient($clientId);
        $enquete = null;
        foreach ($enquetes as $e) {
            if ($e['id'] == $id) {
                $enquete = $e;
                break;
            }
        }
        
        if (!$enquete) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Client&action=dashboard');
            return;
        }
        
        $questions = $this->questionModel->getByEnquete($id);
        $reponses = $this->reponseModel->getByClientAndEnquete($clientId, $id);
        
        // Convertir les réponses en tableau indexé par question_id
        $reponsesMap = [];
        foreach ($reponses as $reponse) {
            $reponsesMap[$reponse['question_id']] = $reponse['valeur'];
        }
        
        // Marquer l'enquête comme vue si ce n'est pas déjà fait
        if ($enquete['participation_statut'] === 'envoye') {
            $this->markAsViewed($id, $clientId);
        }
        
        $csrfToken = $this->generateCSRF();
        
        $this->view('client/enquete/view', [
            'enquete' => $enquete,
            'questions' => $questions,
            'reponses' => $reponsesMap,
            'csrf_token' => $csrfToken,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Sauvegarder les réponses à une enquête
     */
    public function saveReponses(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Client&action=dashboard');
            return;
        }
        
        $enqueteId = (int)($_POST['enquete_id'] ?? 0);
        $clientId = $this->currentUser['id'];
        $reponses = $_POST['reponses'] ?? [];
        
        // Vérifier l'accès à l'enquête
        $enquetes = $this->enqueteModel->getForClient($clientId);
        $hasAccess = false;
        foreach ($enquetes as $e) {
            if ($e['id'] == $enqueteId) {
                $hasAccess = true;
                break;
            }
        }
        
        if (!$hasAccess) {
            $this->flash('error', 'Accès non autorisé');
            $this->redirect('?controller=Client&action=dashboard');
            return;
        }
        
        // Valider les réponses
        $questions = $this->questionModel->getByEnquete($enqueteId);
        $errors = $this->validateReponses($questions, $reponses);
        
        if (!empty($errors)) {
            $_SESSION['reponse_errors'] = $errors;
            $this->redirect('?controller=Client&action=viewEnquete&id=' . $enqueteId);
            return;
        }
        
        try {
            // Sauvegarder les réponses
            $this->reponseModel->saveMultiple($enqueteId, $clientId, $reponses);
            $this->flash('success', 'Vos réponses ont été enregistrées avec succès');
            $this->redirect('?controller=Client&action=dashboard');
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la sauvegarde');
            $this->redirect('?controller=Client&action=viewEnquete&id=' . $enqueteId);
        }
    }
    
    /**
     * Sauvegarde automatique (AJAX)
     */
    public function autoSave(): void {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Token invalide'], 400);
            return;
        }
        
        $enqueteId = (int)($_POST['enquete_id'] ?? 0);
        $clientId = $this->currentUser['id'];
        $reponses = $_POST['reponses'] ?? [];
        
        try {
            // Sauvegarder les réponses sans marquer comme complète
            foreach ($reponses as $questionId => $valeur) {
                if (!empty($valeur)) {
                    $this->reponseModel->saveReponse($questionId, $clientId, $valeur);
                }
            }
            
            $this->json(['success' => true, 'message' => 'Sauvegarde automatique réussie']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erreur de sauvegarde'], 500);
        }
    }
    
    /**
     * Voir l'historique des enquêtes
     */
    public function historique(): void {
        $clientId = $this->currentUser['id'];
        $enquetes = $this->enqueteModel->getForClient($clientId);
        
        // Trier par date de participation
        usort($enquetes, function($a, $b) {
            return strtotime($b['date_envoi']) - strtotime($a['date_envoi']);
        });
        
        $this->view('client/historique', [
            'enquetes' => $enquetes,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Voir les statistiques personnelles
     */
    public function mesStats(): void {
        $clientId = $this->currentUser['id'];
        $enquetes = $this->enqueteModel->getForClient($clientId);
        
        $stats = [
            'total_enquetes' => count($enquetes),
            'enquetes_completees' => 0,
            'taux_participation' => 0,
            'moyenne_temps_reponse' => 0,
            'participation_par_mois' => []
        ];
        
        foreach ($enquetes as $enquete) {
            if ($enquete['participation_statut'] === 'complete') {
                $stats['enquetes_completees']++;
            }
            
            // Grouper par mois pour le graphique
            $mois = date('Y-m', strtotime($enquete['date_envoi']));
            if (!isset($stats['participation_par_mois'][$mois])) {
                $stats['participation_par_mois'][$mois] = 0;
            }
            $stats['participation_par_mois'][$mois]++;
        }
        
        if ($stats['total_enquetes'] > 0) {
            $stats['taux_participation'] = round(($stats['enquetes_completees'] / $stats['total_enquetes']) * 100, 1);
        }
        
        $this->view('client/stats', [
            'stats' => $stats,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Marquer une enquête comme vue
     */
    private function markAsViewed(int $enqueteId, int $clientId): void {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("
                UPDATE enquete_clients 
                SET statut = 'vu' 
                WHERE enquete_id = ? AND client_id = ? AND statut = 'envoye'
            ");
            $stmt->execute([$enqueteId, $clientId]);
        } catch (Exception $e) {
            // Log l'erreur mais ne pas interrompre le flux
            error_log('Erreur lors du marquage comme vu: ' . $e->getMessage());
        }
    }
    
    /**
     * Valider les réponses du client
     */
    private function validateReponses(array $questions, array $reponses): array {
        $errors = [];
        
        foreach ($questions as $question) {
            $questionId = $question['id'];
            $reponse = $reponses[$questionId] ?? '';
            
            // Vérifier les questions obligatoires
            if ($question['obligatoire'] && empty($reponse)) {
                $errors[$questionId] = 'Cette question est obligatoire';
                continue;
            }
            
            // Validation selon le type de question
            switch ($question['type']) {
                case 'rating':
                    if (!empty($reponse)) {
                        $value = (int)$reponse;
                        $options = json_decode($question['options'], true);
                        $max = $options['max'] ?? 5;
                        if ($value < 1 || $value > $max) {
                            $errors[$questionId] = "La note doit être entre 1 et {$max}";
                        }
                    }
                    break;
                    
                case 'multiple_choice':
                    if (!empty($reponse)) {
                        $options = json_decode($question['options'], true);
                        if (!in_array($reponse, $options)) {
                            $errors[$questionId] = 'Option invalide';
                        }
                    }
                    break;
                    
                case 'yes_no':
                    if (!empty($reponse) && !in_array($reponse, ['oui', 'non'])) {
                        $errors[$questionId] = 'Réponse invalide';
                    }
                    break;
                    
                case 'text':
                    if (!empty($reponse) && strlen($reponse) > 1000) {
                        $errors[$questionId] = 'Réponse trop longue (maximum 1000 caractères)';
                    }
                    break;
            }
        }
        
        return $errors;
    }
}
