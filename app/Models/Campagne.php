<?php
/**
 * Modèle Campagne
 * Gère les campagnes d'enquêtes
 */

require_once 'BaseModel.php';

class Campagne extends BaseModel {
    protected $table = 'campagnes';
    
    /**
     * Obtient toutes les campagnes avec les informations du créateur
     */
    public function getAllWithCreator(): array {
        $stmt = $this->pdo->query("
            SELECT c.*, u.nom as createur_nom, u.prenom as createur_prenom
            FROM {$this->table} c
            LEFT JOIN utilisateurs u ON c.created_by = u.id
            ORDER BY c.date_creation DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtient une campagne avec les informations du créateur
     */
    public function getWithCreator(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.nom as createur_nom, u.prenom as createur_prenom
            FROM {$this->table} c
            LEFT JOIN utilisateurs u ON c.created_by = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Obtient les campagnes actives
     */
    public function getActiveCampaigns(): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE statut = 'active' 
            AND date_debut <= CURDATE() 
            AND date_fin >= CURDATE()
            ORDER BY date_creation DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtient les statistiques d'une campagne
     */
    public function getStats(int $id): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                (SELECT COUNT(*) FROM enquetes WHERE campagne_id = ?) as total_enquetes,
                (SELECT COUNT(*) FROM enquetes WHERE campagne_id = ? AND statut = 'active') as enquetes_actives,
                (SELECT COUNT(DISTINCT ec.client_id) FROM enquete_clients ec 
                 JOIN enquetes e ON ec.enquete_id = e.id WHERE e.campagne_id = ?) as total_clients_cibles,
                (SELECT COUNT(*) FROM enquete_clients ec 
                 JOIN enquetes e ON ec.enquete_id = e.id WHERE e.campagne_id = ? AND ec.statut = 'complete') as reponses_completes
        ");
        $stmt->execute([$id, $id, $id, $id]);
        return $stmt->fetch();
    }
    
    /**
     * Valide les dates de campagne
     */
    public function validateDates(string $dateDebut, string $dateFin): bool {
        return strtotime($dateDebut) <= strtotime($dateFin) && strtotime($dateDebut) >= strtotime(date('Y-m-d'));
    }
    
    /**
     * Met à jour le statut d'une campagne
     */
    public function updateStatut(int $id, string $statut): bool {
        $allowedStatuts = ['en_preparation', 'active', 'terminee', 'suspendue'];
        if (!in_array($statut, $allowedStatuts)) {
            return false;
        }
        
        return $this->update($id, ['statut' => $statut]);
    }
}
