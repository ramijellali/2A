<?php
/**
 * Modèle Utilisateur
 * Gère les opérations CRUD pour les utilisateurs (admin, agent, client)
 */

require_once 'BaseModel.php';

class Utilisateur extends BaseModel {
    protected $table = 'utilisateurs';
    
    /**
     * Authentifie un utilisateur
     */
    public function authenticate(string $email, string $password): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = ? AND statut = 'actif'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Ne pas retourner le mot de passe
            unset($user['mot_de_passe']);
            return $user;
        }
        
        return null;
    }
    
    /**
     * Crée un nouvel utilisateur avec hash du mot de passe
     */
    public function createUser(array $data): int {
        if (isset($data['mot_de_passe'])) {
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        }
        
        return $this->create($data);
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function updateUser(int $id, array $data): bool {
        if (isset($data['mot_de_passe']) && !empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        } else {
            unset($data['mot_de_passe']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Trouve les utilisateurs par rôle
     */
    public function findByRole(string $role): array {
        return $this->findWhere('role', $role);
    }
    
    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists(string $email, int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtient les statistiques des utilisateurs par rôle
     */
    public function getStatsByRole(): array {
        $stmt = $this->pdo->query("
            SELECT role, COUNT(*) as total, 
                   SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as actifs
            FROM {$this->table} 
            GROUP BY role
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Recherche d'utilisateurs
     */
    public function search(string $term): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ?
        ");
        $searchTerm = "%{$term}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
}
