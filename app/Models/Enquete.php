<?php
/**
 * Modèle Enquête
 * Gère les enquêtes et leurs relations
 */

require_once 'BaseModel.php';

class Enquete extends BaseModel {
    protected $table = 'enquetes';
    
    /**
     * Obtient toutes les enquêtes avec les informations de campagne et agent
     */
    public function getAllWithDetails(): array {
        $stmt = $this->pdo->query("
            SELECT e.*, 
                   c.nom as campagne_nom,
                   c.statut as campagne_statut,
                   u.nom as agent_nom, u.prenom as agent_prenom
            FROM {$this->table} e
            LEFT JOIN campagnes c ON e.campagne_id = c.id
            LEFT JOIN utilisateurs u ON e.created_by = u.id
            ORDER BY e.date_creation DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtient une enquête avec tous ses détails
     */
    public function getWithDetails(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT e.*, 
                   c.nom as campagne_nom,
                   c.statut as campagne_statut,
                   u.nom as agent_nom, u.prenom as agent_prenom
            FROM {$this->table} e
            LEFT JOIN campagnes c ON e.campagne_id = c.id
            LEFT JOIN utilisateurs u ON e.created_by = u.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Obtient les enquêtes d'un agent
     */
    public function getByAgent(int $agentId): array {
        $stmt = $this->pdo->prepare("
            SELECT e.*, c.nom as campagne_nom, c.statut as campagne_statut
            FROM {$this->table} e
            LEFT JOIN campagnes c ON e.campagne_id = c.id
            WHERE e.created_by = ?
            ORDER BY e.date_creation DESC
        ");
        $stmt->execute([$agentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtient les enquêtes d'une campagne
     */
    public function getByCampagne(int $campagneId): array {
        $stmt = $this->pdo->prepare("
            SELECT e.*, u.nom as agent_nom, u.prenom as agent_prenom
            FROM {$this->table} e
            LEFT JOIN utilisateurs u ON e.created_by = u.id
            WHERE e.campagne_id = ?
            ORDER BY e.date_creation DESC
        ");
        $stmt->execute([$campagneId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtient les enquêtes disponibles pour un client
     */
    public function getForClient(int $clientId): array {
        $stmt = $this->pdo->prepare("
            SELECT e.*, 
                   c.nom as campagne_nom, 
                   ec.statut as participation_statut,
                   ec.date_envoi,
                   ec.date_reponse
            FROM {$this->table} e
            JOIN enquete_clients ec ON e.id = ec.enquete_id
            LEFT JOIN campagnes c ON e.campagne_id = c.id
            WHERE ec.client_id = ? AND e.statut = 'active'
            ORDER BY ec.date_envoi DESC
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Assigne des clients à une enquête
     */
    public function assignClients(int $enqueteId, array $clientIds): bool {
        try {
            $this->pdo->beginTransaction();
            
            // Supprime les anciennes assignations
            $stmt = $this->pdo->prepare("DELETE FROM enquete_clients WHERE enquete_id = ?");
            $stmt->execute([$enqueteId]);
            
            // Ajoute les nouvelles assignations
            $stmt = $this->pdo->prepare("INSERT INTO enquete_clients (enquete_id, client_id) VALUES (?, ?)");
            foreach ($clientIds as $clientId) {
                $stmt->execute([$enqueteId, $clientId]);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    /**
     * Obtient les clients assignés à une enquête
     */
    public function getAssignedClients(int $enqueteId): array {
        $stmt = $this->pdo->prepare("
            SELECT u.*, ec.statut as participation_statut, ec.date_envoi, ec.date_reponse
            FROM utilisateurs u
            JOIN enquete_clients ec ON u.id = ec.client_id
            WHERE ec.enquete_id = ?
            ORDER BY ec.date_envoi DESC
        ");
        $stmt->execute([$enqueteId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Met à jour le statut d'une enquête
     */
    public function updateStatut(int $id, string $statut): bool {
        $allowedStatuts = ['brouillon', 'active', 'terminee', 'archivee'];
        if (!in_array($statut, $allowedStatuts)) {
            return false;
        }
        
        return $this->update($id, ['statut' => $statut]);
    }
    
    /**
     * Obtient les enquêtes d'un agent avec filtres
     */
    public function getByAgentWithFilters(int $agentId, array $filters = [], int $limit = 10, int $offset = 0): array {
        $where = ["e.created_by = ?"];
        $params = [$agentId];
        
        // Filtre par statut
        if (!empty($filters['statut'])) {
            $where[] = "e.statut = ?";
            $params[] = $filters['statut'];
        }
        
        // Filtre par date début
        if (!empty($filters['date_debut'])) {
            $where[] = "DATE(e.date_creation) >= ?";
            $params[] = $filters['date_debut'];
        }
        
        // Filtre par date fin
        if (!empty($filters['date_fin'])) {
            $where[] = "DATE(e.date_creation) <= ?";
            $params[] = $filters['date_fin'];
        }
        
        // Filtre par recherche
        if (!empty($filters['search'])) {
            $where[] = "(e.titre LIKE ? OR e.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $stmt = $this->pdo->prepare("
            SELECT e.*, 
                   c.nom as campagne_titre,
                   (SELECT COUNT(*) FROM questions q WHERE q.enquete_id = e.id) as nb_questions,
                   (SELECT COUNT(*) FROM reponses r 
                    JOIN questions q ON r.question_id = q.id 
                    WHERE q.enquete_id = e.id) as nb_reponses
            FROM {$this->table} e
            LEFT JOIN campagnes c ON e.campagne_id = c.id
            WHERE {$whereClause}
            ORDER BY e.date_modification DESC, e.date_creation DESC
            LIMIT ? OFFSET ?
        ");
        
        $params[] = $limit;
        $params[] = $offset;
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Compte les enquêtes d'un agent avec filtres
     */
    public function countByAgentWithFilters(int $agentId, array $filters = []): int {
        $where = ["created_by = ?"];
        $params = [$agentId];
        
        // Filtre par statut
        if (!empty($filters['statut'])) {
            $where[] = "statut = ?";
            $params[] = $filters['statut'];
        }
        
        // Filtre par date début
        if (!empty($filters['date_debut'])) {
            $where[] = "DATE(date_creation) >= ?";
            $params[] = $filters['date_debut'];
        }
        
        // Filtre par date fin
        if (!empty($filters['date_fin'])) {
            $where[] = "DATE(date_creation) <= ?";
            $params[] = $filters['date_fin'];
        }
        
        // Filtre par recherche
        if (!empty($filters['search'])) {
            $where[] = "(titre LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$whereClause}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
