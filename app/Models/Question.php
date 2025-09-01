<?php
/**
 * Modèle Question
 * Gère les questions des enquêtes
 */

require_once 'BaseModel.php';

class Question extends BaseModel {
    protected $table = 'questions';
    
    /**
     * Obtient toutes les questions d'une enquête
     */
    public function getByEnquete(int $enqueteId): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE enquete_id = ? 
            ORDER BY ordre_affichage ASC
        ");
        $stmt->execute([$enqueteId]);
        $questions = $stmt->fetchAll();
        
        // Décoder les options JSON
        foreach ($questions as &$question) {
            if (!empty($question['options_json'])) {
                $question['options'] = json_decode($question['options_json'], true);
            }
        }
        
        return $questions;
    }
    
    /**
     * Crée une question avec encodage JSON des options
     */
    public function createQuestion(array $data): int {
        // Supprimer le token CSRF s'il existe
        unset($data['csrf_token']);
        
        // Gérer les options pour choix multiples
        if ($data['type_question'] === 'choix_multiple' && isset($data['options']) && is_array($data['options'])) {
            $data['options_json'] = json_encode(array_filter($data['options']));
            unset($data['options']);
        }
        
        // Gérer les options pour notation
        if ($data['type_question'] === 'notation' && isset($data['rating_max'])) {
            $data['options_json'] = json_encode(['max' => (int)$data['rating_max']]);
            unset($data['rating_max']);
        }
        
        // Conversion des champs
        if (isset($data['ordre'])) {
            $data['ordre_affichage'] = $data['ordre'];
            unset($data['ordre']);
        }
        
        return $this->create($data);
    }
    
    /**
     * Met à jour une question
     */
    public function updateQuestion(int $id, array $data): bool {
        // Supprimer le token CSRF s'il existe
        unset($data['csrf_token']);
        
        // Gérer les options pour choix multiples
        if ($data['type_question'] === 'choix_multiple' && isset($data['options']) && is_array($data['options'])) {
            $data['options_json'] = json_encode(array_filter($data['options']));
            unset($data['options']);
        }
        
        // Gérer les options pour notation
        if ($data['type_question'] === 'notation' && isset($data['rating_max'])) {
            $data['options_json'] = json_encode(['max' => (int)$data['rating_max']]);
            unset($data['rating_max']);
        }
        
        // Conversion des champs
        if (isset($data['ordre'])) {
            $data['ordre_affichage'] = $data['ordre'];
            unset($data['ordre']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Obtient la question suivante dans l'ordre
     */
    public function getNextOrdre(int $enqueteId): int {
        $stmt = $this->pdo->prepare("
            SELECT MAX(ordre_affichage) FROM {$this->table} WHERE enquete_id = ?
        ");
        $stmt->execute([$enqueteId]);
        $maxOrdre = $stmt->fetchColumn();
        
        return ($maxOrdre ?? 0) + 1;
    }
    
    /**
     * Réorganise l'ordre des questions
     */
    public function reorder(int $enqueteId, array $questionIds): bool {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET ordre = ? WHERE id = ? AND enquete_id = ?");
            
            foreach ($questionIds as $ordre => $questionId) {
                $stmt->execute([$ordre + 1, $questionId, $enqueteId]);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    /**
     * Valide les données d'une question
     */
    public function validate(array $data): array {
        $errors = [];
        
        if (empty($data['texte'])) {
            $errors['texte'] = 'Le texte de la question est obligatoire';
        } elseif (strlen($data['texte']) < 5) {
            $errors['texte'] = 'Le texte doit contenir au moins 5 caractères';
        }
        
        if (empty($data['type_question'])) {
            $errors['type_question'] = 'Le type est obligatoire';
        } elseif (!in_array($data['type_question'], ['texte_libre', 'choix_multiple', 'notation', 'oui_non'])) {
            $errors['type_question'] = 'Type de question invalide';
        }
        
        if ($data['type_question'] === 'choix_multiple') {
            $options = $data['options'] ?? [];
            if (!is_array($options) || count(array_filter($options)) < 2) {
                $errors['options'] = 'Au moins 2 options sont nécessaires pour une question à choix multiples';
            }
        }
        
        if ($data['type_question'] === 'notation') {
            $maxRating = (int)($data['rating_max'] ?? 0);
            if ($maxRating < 2 || $maxRating > 10) {
                $errors['rating_max'] = 'L\'échelle de notation doit être entre 2 et 10';
            }
        }
        
        return $errors;
    }
    
    /**
     * Duplique les questions d'une enquête vers une autre
     */
    public function duplicateFromEnquete(int $sourceEnqueteId, int $targetEnqueteId): bool {
        try {
            $questions = $this->getByEnquete($sourceEnqueteId);
            
            foreach ($questions as $question) {
                unset($question['id']);
                $question['enquete_id'] = $targetEnqueteId;
                $this->createQuestion($question);
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Supprime toutes les questions d'une enquête
     */
    public function deleteByEnquete(int $enqueteId): bool {
        try {
            // D'abord supprimer les réponses liées aux questions
            $stmt = $this->pdo->prepare("
                DELETE r FROM reponses r 
                JOIN questions q ON r.question_id = q.id 
                WHERE q.enquete_id = ?
            ");
            $stmt->execute([$enqueteId]);
            
            // Puis supprimer les questions
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE enquete_id = ?");
            $stmt->execute([$enqueteId]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
