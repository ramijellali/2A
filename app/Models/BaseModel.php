<?php
/**
 * Classe de base pour tous les modèles
 * Fournit les fonctionnalités CRUD de base
 */

require_once __DIR__ . '/../../config/database.php';

abstract class BaseModel {
    protected $pdo;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    /**
     * Trouve tous les enregistrements
     */
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }
    
    /**
     * Trouve un enregistrement par ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Crée un nouvel enregistrement
     */
    public function create(array $data): int {
        try {
            $fields = implode(',', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($data);
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Erreur SQL : " . implode(' - ', $errorInfo));
            }
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur base de données : " . $e->getMessage());
        }
    }
    
    /**
     * Met à jour un enregistrement
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "{$field} = :{$field}";
        }
        $fieldsString = implode(', ', $fields);
        
        $sql = "UPDATE {$this->table} SET {$fieldsString} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    /**
     * Supprime un enregistrement
     */
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Trouve des enregistrements avec une condition WHERE
     */
    public function findWhere(string $column, $value): array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
    
    /**
     * Compte les enregistrements
     */
    public function count(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }
}
