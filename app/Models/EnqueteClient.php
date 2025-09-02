<?php
/**
 * Modèle EnqueteClient
 * Gère les assignations d'enquêtes aux clients
 */

require_once 'BaseModel.php';

class EnqueteClient extends BaseModel {
    protected $table = 'enquete_clients';
    
    /**
     * Assigner une enquête à un client
     */
    public function assignerEnquete(int $enqueteId, int $clientId): int {
        $data = [
            'enquete_id' => $enqueteId,
            'client_id' => $clientId,
            'statut' => 'envoye'
        ];
        
        return $this->create($data);
    }
    
    /**
     * Assigner une enquête à plusieurs clients
     */
    public function assignerEnqueteMultiple(int $enqueteId, array $clientIds): array {
        $results = [];
        
        foreach ($clientIds as $clientId) {
            try {
                $results[] = $this->assignerEnquete($enqueteId, $clientId);
            } catch (Exception $e) {
                // Si l'assignation existe déjà, on ignore l'erreur
                error_log("Assignation déjà existante pour enquête $enqueteId et client $clientId");
            }
        }
        
        return $results;
    }
    
    /**
     * Obtenir les enquêtes assignées à un client
     */
    public function getEnquetesClient(int $clientId): array {
        $stmt = $this->pdo->prepare("
            SELECT ec.*, 
                   e.titre as enquete_titre,
                   e.description as enquete_description,
                   e.statut as enquete_statut,
                   c.nom as campagne_nom
            FROM {$this->table} ec
            JOIN enquetes e ON ec.enquete_id = e.id
            JOIN campagnes c ON e.campagne_id = c.id
            WHERE ec.client_id = ?
            ORDER BY ec.date_envoi DESC
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir les clients assignés à une enquête
     */
    public function getClientsEnquete(int $enqueteId): array {
        $stmt = $this->pdo->prepare("
            SELECT ec.*, 
                   u.nom as client_nom,
                   u.prenom as client_prenom,
                   u.email as client_email
            FROM {$this->table} ec
            JOIN utilisateurs u ON ec.client_id = u.id
            WHERE ec.enquete_id = ?
            ORDER BY ec.date_envoi DESC
        ");
        $stmt->execute([$enqueteId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Mettre à jour le statut d'une assignation
     */
    public function updateStatut(int $enqueteId, int $clientId, string $statut): bool {
        $data = ['statut' => $statut];
        
        // Mettre à jour les dates selon le statut
        if ($statut === 'en_cours' && !$this->getAssignation($enqueteId, $clientId)['date_debut']) {
            $data['date_debut'] = date('Y-m-d H:i:s');
        } elseif ($statut === 'complete') {
            $data['date_fin'] = date('Y-m-d H:i:s');
        }
        
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table} 
            SET " . implode(' = ?, ', array_keys($data)) . " = ?
            WHERE enquete_id = ? AND client_id = ?
        ");
        
        return $stmt->execute([...array_values($data), $enqueteId, $clientId]);
    }
    
    /**
     * Obtenir une assignation spécifique
     */
    public function getAssignation(int $enqueteId, int $clientId): ?array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE enquete_id = ? AND client_id = ?
        ");
        $stmt->execute([$enqueteId, $clientId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Vérifier si un client a accès à une enquête
     */
    public function clientHasAccess(int $enqueteId, int $clientId): bool {
        $assignation = $this->getAssignation($enqueteId, $clientId);
        return $assignation && in_array($assignation['statut'], ['envoye', 'en_cours', 'complete']);
    }
    
    /**
     * Supprimer une assignation
     */
    public function retirerEnquete(int $enqueteId, int $clientId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM {$this->table} 
            WHERE enquete_id = ? AND client_id = ?
        ");
        return $stmt->execute([$enqueteId, $clientId]);
    }
    
    /**
     * Obtenir les statistiques d'une enquête
     */
    public function getStatsEnquete(int $enqueteId): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_assigne,
                SUM(CASE WHEN statut = 'envoye' THEN 1 ELSE 0 END) as envoye,
                SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                SUM(CASE WHEN statut = 'complete' THEN 1 ELSE 0 END) as complete,
                SUM(CASE WHEN statut = 'expire' THEN 1 ELSE 0 END) as expire
            FROM {$this->table} 
            WHERE enquete_id = ?
        ");
        $stmt->execute([$enqueteId]);
        return $stmt->fetch() ?: [];
    }
    
    /**
     * Obtenir toutes les assignations avec détails
     */
    public function getAllWithDetails(): array {
        $stmt = $this->pdo->query("
            SELECT ec.*, 
                   e.titre as enquete_titre,
                   c.nom as campagne_nom,
                   u.nom as client_nom,
                   u.prenom as client_prenom,
                   u.email as client_email
            FROM {$this->table} ec
            JOIN enquetes e ON ec.enquete_id = e.id
            JOIN campagnes c ON e.campagne_id = c.id
            JOIN utilisateurs u ON ec.client_id = u.id
            ORDER BY ec.date_envoi DESC
        ");
        return $stmt->fetchAll();
    }
}
