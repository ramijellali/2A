<?php
/**
 * Contrôleur Agent
 * Gère les fonctionnalités des agents
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Utilisateur.php';
require_once __DIR__ . '/../Models/Campagne.php';
require_once __DIR__ . '/../Models/Enquete.php';
require_once __DIR__ . '/../Models/Question.php';
require_once __DIR__ . '/../Models/Reponse.php';

class AgentController extends BaseController {
    private $utilisateurModel;
    private $campagneModel;
    private $enqueteModel;
    private $questionModel;
    private $reponseModel;
    
    public function __construct() {
        parent::__construct();
        $this->requirePermission(['agent', 'admin']);
        $this->utilisateurModel = new Utilisateur();
        $this->campagneModel = new Campagne();
        $this->enqueteModel = new Enquete();
        $this->questionModel = new Question();
        $this->reponseModel = new Reponse();
    }
    
    /**
     * Tableau de bord agent
     */
    public function dashboard(): void {
        $agentId = $this->currentUser['id'];
        
        $stats = [
            'mes_enquetes' => count($this->enqueteModel->getByAgent($agentId)),
            'enquetes_actives' => count(array_filter($this->enqueteModel->getByAgent($agentId), 
                function($e) { return $e['statut'] === 'active'; })),
            'campagnes_actives' => count($this->campagneModel->getActiveCampaigns()),
            'clients_total' => count($this->utilisateurModel->findByRole('client'))
        ];
        
        $recentEnquetes = array_slice($this->enqueteModel->getByAgent($agentId), 0, 5);
        
        $this->view('agent/dashboard', [
            'stats' => $stats,
            'recent_enquetes' => $recentEnquetes,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Liste des enquêtes de l'agent
     */
    public function enquetes(): void {
        $agentId = $this->currentUser['id'];
        
        // Gestion des filtres
        $filters = [
            'statut' => $_GET['statut'] ?? '',
            'date_debut' => $_GET['date_debut'] ?? '',
            'date_fin' => $_GET['date_fin'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        // Pagination
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $enquetes = $this->enqueteModel->getByAgentWithFilters($agentId, $filters, $limit, $offset);
        $totalEnquetes = $this->enqueteModel->countByAgentWithFilters($agentId, $filters);
        $totalPages = ceil($totalEnquetes / $limit);
        
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalEnquetes
        ];
        
        $this->view('agent/enquetes/index', [
            'enquetes' => $enquetes,
            'filters' => $filters,
            'pagination' => $pagination,
            'csrf_token' => $this->generateCSRF(),
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Créer une nouvelle enquête
     */
    public function newEnquete(): void {
        $campagnes = $this->campagneModel->getActiveCampaigns();
        $csrfToken = $this->generateCSRF();
        
        $this->view('agent/enquetes/create', [
            'campagnes' => $campagnes,
            'csrf_token' => $csrfToken,
            'errors' => $_SESSION['enquete_errors'] ?? [],
            'old_data' => $_SESSION['old_data'] ?? []
        ]);
        unset($_SESSION['enquete_errors'], $_SESSION['old_data']);
    }
    
    /**
     * Sauvegarder une nouvelle enquête
     */
    public function storeEnquete(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Agent&action=createEnquete');
            return;
        }
        
        $data = $this->sanitize($_POST);
        $errors = $this->validateEnquete($data);
        
        if (!empty($errors)) {
            $_SESSION['enquete_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('?controller=Agent&action=createEnquete');
            return;
        }
        
        try {
            $enqueteId = $this->enqueteModel->create([
                'titre' => $data['titre'],
                'description' => $data['description'],
                'campagne_id' => $data['campagne_id'],
                'created_by' => $this->currentUser['id'],
                'statut' => 'brouillon'
            ]);
            
            $this->flash('success', 'Enquête créée avec succès');
            $this->redirect('?controller=Agent&action=editEnquete&id=' . $enqueteId);
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la création');
            $this->redirect('?controller=Agent&action=newEnquete');
        }
    }
    
    /**
     * Éditer une enquête
     */
    public function editEnquete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $enquete = $this->enqueteModel->findById($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $questions = $this->questionModel->getByEnquete($id);
        $clients = $this->utilisateurModel->findByRole('client');
        $assignedClients = $this->enqueteModel->getAssignedClients($id);
        $csrfToken = $this->generateCSRF();
        
        $this->view('agent/enquetes/edit', [
            'enquete' => $enquete,
            'questions' => $questions,
            'clients' => $clients,
            'assigned_clients' => $assignedClients,
            'csrf_token' => $csrfToken,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Ajouter une question à une enquête
     */
    public function addQuestion(): void {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Token invalide'], 400);
            return;
        }
        
        $data = $this->sanitize($_POST);
        $errors = $this->questionModel->validate($data);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        try {
            // Vérifier que l'enquête appartient à l'agent
            $enquete = $this->enqueteModel->findById($data['enquete_id']);
            if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
                $this->json(['success' => false, 'message' => 'Non autorisé'], 403);
                return;
            }
            
            $data['ordre_affichage'] = $this->questionModel->getNextOrdre($data['enquete_id']);
            $questionId = $this->questionModel->createQuestion($data);
            
            $this->json(['success' => true, 'question_id' => $questionId]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
    
    /**
     * Éditer une question
     */
    public function editQuestion(): void {
        $id = (int)($_GET['id'] ?? 0);
        $question = $this->questionModel->findById($id);
        
        if (!$question) {
            $this->json(['success' => false, 'message' => 'Question non trouvée'], 404);
            return;
        }
        
        // Vérifier que l'enquête appartient à l'agent
        $enquete = $this->enqueteModel->findById($question['enquete_id']);
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->json(['success' => false, 'message' => 'Non autorisé'], 403);
            return;
        }
        
        // Décoder les options si présentes
        if (!empty($question['options_json'])) {
            $question['options'] = json_decode($question['options_json'], true);
        }
        
        $this->json(['success' => true, 'question' => $question]);
    }
    
    /**
     * Mettre à jour une question
     */
    public function updateQuestion(): void {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Token invalide'], 400);
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $question = $this->questionModel->findById($id);
        
        if (!$question) {
            $this->json(['success' => false, 'message' => 'Question non trouvée'], 404);
            return;
        }
        
        // Vérifier que l'enquête appartient à l'agent
        $enquete = $this->enqueteModel->findById($question['enquete_id']);
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->json(['success' => false, 'message' => 'Non autorisé'], 403);
            return;
        }
        
        $data = $this->sanitize($_POST);
        unset($data['id'], $data['csrf_token']);
        
        $errors = $this->questionModel->validate($data);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        try {
            $this->questionModel->updateQuestion($id, $data);
            $this->json(['success' => true, 'message' => 'Question mise à jour']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
    
    /**
     * Supprimer une question
     */
    public function deleteQuestion(): void {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Token invalide'], 400);
            return;
        }
        
        $id = (int)($_GET['id'] ?? 0);
        $question = $this->questionModel->findById($id);
        
        if (!$question) {
            $this->json(['success' => false, 'message' => 'Question non trouvée'], 404);
            return;
        }
        
        // Vérifier que l'enquête appartient à l'agent
        $enquete = $this->enqueteModel->findById($question['enquete_id']);
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->json(['success' => false, 'message' => 'Non autorisé'], 403);
            return;
        }
        
        // Vérifier que l'enquête est en brouillon
        if ($enquete['statut'] !== 'brouillon') {
            $this->json(['success' => false, 'message' => 'Impossible de supprimer une question d\'une enquête active'], 400);
            return;
        }
        
        try {
            $this->questionModel->delete($id);
            $this->json(['success' => true, 'message' => 'Question supprimée']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
    
    /**
     * Assigner des clients à une enquête
     */
    public function assignClients(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $enqueteId = (int)($_POST['enquete_id'] ?? 0);
        $clientIds = $_POST['client_ids'] ?? [];
        
        // Vérifier que l'enquête appartient à l'agent
        $enquete = $this->enqueteModel->findById($enqueteId);
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        try {
            $this->enqueteModel->assignClients($enqueteId, $clientIds);
            $this->flash('success', 'Clients assignés avec succès');
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de l\'assignation');
        }
        
        $this->redirect('?controller=Agent&action=editEnquete&id=' . $enqueteId);
    }
    
    /**
     * Activer une enquête
     */
    public function activateEnquete(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $enquete = $this->enqueteModel->findById($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        // Vérifier qu'il y a au moins une question
        $questions = $this->questionModel->getByEnquete($id);
        if (empty($questions)) {
            $this->flash('error', 'Impossible d\'activer une enquête sans questions');
            $this->redirect('?controller=Agent&action=editEnquete&id=' . $id);
            return;
        }
        
        try {
            $this->enqueteModel->updateStatut($id, 'active');
            $this->flash('success', 'Enquête activée avec succès');
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de l\'activation');
        }
        
        $this->redirect('?controller=Agent&action=enquetes');
    }
    
    /**
     * Voir les statistiques d'une enquête
     */
    public function statsEnquete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $enquete = $this->enqueteModel->getWithDetails($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $stats = $this->reponseModel->getStatsForEnquete($id);
        $assignedClients = $this->enqueteModel->getAssignedClients($id);
        
        $this->view('agent/enquetes/stats', [
            'enquete' => $enquete,
            'stats' => $stats,
            'assigned_clients' => $assignedClients
        ]);
    }
    
    /**
     * Valider les données d'une enquête
     */
    private function validateEnquete(array $data): array {
        $errors = [];
        
        if (empty($data['titre'])) {
            $errors['titre'] = 'Le titre est obligatoire';
        }
        
        if (empty($data['campagne_id'])) {
            $errors['campagne_id'] = 'La campagne est obligatoire';
        } else {
            // Vérifier que la campagne existe et est active
            $campagne = $this->campagneModel->findById($data['campagne_id']);
            if (!$campagne || $campagne['statut'] !== 'active') {
                $errors['campagne_id'] = 'Campagne invalide ou inactive';
            }
        }
        
        return $errors;
    }
    
    /**
     * Voir les détails d'une enquête
     */
    public function viewEnquete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $enquete = $this->enqueteModel->getWithDetails($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $questions = $this->questionModel->getByEnquete($id);
        $assignedClients = $this->enqueteModel->getAssignedClients($id);
        $stats = $this->reponseModel->getStatsForEnquete($id);
        
        $this->view('agent/enquetes/view', [
            'enquete' => $enquete,
            'questions' => $questions,
            'assigned_clients' => $assignedClients,
            'stats' => $stats,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Mettre à jour une enquête
     */
    public function updateEnquete(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $enquete = $this->enqueteModel->findById($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $data = $this->sanitize($_POST);
        unset($data['csrf_token'], $data['id']);
        $errors = $this->validateEnquete($data);
        
        if (!empty($errors)) {
            $_SESSION['enquete_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('?controller=Agent&action=editEnquete&id=' . $id);
            return;
        }
        
        try {
            $data['date_modification'] = date('Y-m-d H:i:s');
            $this->enqueteModel->update($id, $data);
            $this->flash('success', 'Enquête mise à jour avec succès');
            $this->redirect('?controller=Agent&action=editEnquete&id=' . $id);
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la mise à jour');
            $this->redirect('?controller=Agent&action=editEnquete&id=' . $id);
        }
    }
    
    /**
     * Supprimer une enquête (brouillon uniquement)
     */
    public function deleteEnquete(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $enquete = $this->enqueteModel->findById($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        if ($enquete['statut'] !== 'brouillon') {
            $this->flash('error', 'Seules les enquêtes en brouillon peuvent être supprimées');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        try {
            // Supprimer les questions associées
            $this->questionModel->deleteByEnquete($id);
            // Supprimer l'enquête
            $this->enqueteModel->delete($id);
            $this->flash('success', 'Enquête supprimée avec succès');
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de la suppression');
        }
        
        $this->redirect('?controller=Agent&action=enquetes');
    }
    
    /**
     * Archiver une enquête
     */
    public function archiveEnquete(): void {
        if (!$this->validateCSRF()) {
            $this->flash('error', 'Token de sécurité invalide');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $enquete = $this->enqueteModel->findById($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        try {
            $this->enqueteModel->updateStatut($id, 'archivee');
            $this->flash('success', 'Enquête archivée avec succès');
        } catch (Exception $e) {
            $this->flash('error', 'Erreur lors de l\'archivage');
        }
        
        $this->redirect('?controller=Agent&action=enquetes');
    }
    
    /**
     * Gestion des questions d'une enquête
     */
    public function questions(): void {
        $enqueteId = (int)($_GET['enquete_id'] ?? 0);
        $enquete = $this->enqueteModel->findById($enqueteId);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $questions = $this->questionModel->getByEnquete($enqueteId);
        $csrfToken = $this->generateCSRF();
        
        $this->view('agent/enquetes/questions', [
            'enquete' => $enquete,
            'questions' => $questions,
            'csrf_token' => $csrfToken,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Voir les résultats d'une enquête
     */
    public function results(): void {
        $id = (int)($_GET['id'] ?? 0);
        $enquete = $this->enqueteModel->getWithDetails($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $questions = $this->questionModel->getByEnquete($id);
        $stats = $this->reponseModel->getStatsForEnquete($id);
        $reponses = $this->reponseModel->getByEnquete($id);
        
        $this->view('agent/enquetes/results', [
            'enquete' => $enquete,
            'questions' => $questions,
            'stats' => $stats,
            'reponses' => $reponses,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Exporter les résultats d'une enquête
     */
    public function exportResults(): void {
        $id = (int)($_GET['id'] ?? 0);
        $enquete = $this->enqueteModel->findById($id);
        
        if (!$enquete || $enquete['created_by'] != $this->currentUser['id']) {
            $this->flash('error', 'Enquête non trouvée');
            $this->redirect('?controller=Agent&action=enquetes');
            return;
        }
        
        $questions = $this->questionModel->getByEnquete($id);
        $reponses = $this->reponseModel->getByEnquete($id);
        
        // Générer le CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="enquete_' . $id . '_resultats.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Headers UTF-8 BOM pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // En-têtes
        $headers = ['Client', 'Date de réponse'];
        foreach ($questions as $question) {
            $headers[] = $question['texte'];
        }
        fputcsv($output, $headers, ';');
        
        // Données
        $reponsesGrouped = [];
        foreach ($reponses as $reponse) {
            $reponsesGrouped[$reponse['client_id']][$reponse['question_id']] = $reponse;
        }
        
        foreach ($reponsesGrouped as $clientId => $clientReponses) {
            $row = [];
            $firstReponse = reset($clientReponses);
            $row[] = $firstReponse['client_nom'] . ' ' . $firstReponse['client_prenom'];
            $row[] = date('d/m/Y H:i', strtotime($firstReponse['date_reponse']));
            
            foreach ($questions as $question) {
                $reponse = $clientReponses[$question['id']] ?? null;
                $row[] = $reponse ? $reponse['valeur'] : '';
            }
            
            fputcsv($output, $row, ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Modèles d'enquêtes
     */
    public function templates(): void {
        // Pour l'instant, juste rediriger vers la création
        $this->redirect('?controller=Agent&action=newEnquete');
    }
    
    /**
     * Créer une enquête (alias pour newEnquete)
     */
    public function createEnquete(): void {
        $this->newEnquete();
    }
}