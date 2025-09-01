<?php
/**
 * Modèle Réponse
 * Gère les réponses des clients aux questions
 */

require_once 'BaseModel.php';

class Reponse extends BaseModel {
    protected $table = 'reponses';
    
    /**
     * Obtient toutes les réponses d'un client pour une enquête
     */
    public function getByClientAndEnquete(int $clientId, int $enqueteId): array {
        $stmt = $this->pdo->prepare("
            SELECT r.*, 
                   q.texte as question_texte, 
                   q.type_question as question_type,
                   COALESCE(r.reponse_texte, CAST(r.reponse_numerique AS CHAR)) as valeur
            FROM {$this->table} r
            JOIN questions q ON r.question_id = q.id
            WHERE r.utilisateur_id = ? AND q.enquete_id = ?
            ORDER BY q.ordre_affichage ASC
        ");
        $stmt->execute([$clientId, $enqueteId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtient toutes les réponses d'une question
     */
    public function getByQuestion(int $questionId): array {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.nom, u.prenom, u.email
            FROM {$this->table} r
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            WHERE r.question_id = ?
            ORDER BY r.date_reponse DESC
        ");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Sauvegarde une réponse (créer ou mettre à jour)
     */
    public function saveReponse(int $questionId, int $clientId, string $valeur): bool {
        try {
            // Vérifier si la réponse existe déjà
            $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE question_id = ? AND utilisateur_id = ?");
            $stmt->execute([$questionId, $clientId]);
            $existing = $stmt->fetch();
            
            // Déterminer si c'est numérique ou texte
            $isNumeric = is_numeric($valeur);
            $data = [
                'question_id' => $questionId,
                'utilisateur_id' => $clientId,
                'reponse_texte' => $isNumeric ? null : $valeur,
                'reponse_numerique' => $isNumeric ? (int)$valeur : null,
                'date_reponse' => date('Y-m-d H:i:s')
            ];
            
            if ($existing) {
                // Mettre à jour
                return $this->update($existing['id'], $data);
            } else {
                // Créer
                $result = $this->create($data);
                return $result !== false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Sauvegarde multiple réponses pour une enquête
     */
    public function saveMultiple(int $enqueteId, int $clientId, array $reponses): bool {
        try {
            $this->pdo->beginTransaction();
            
            foreach ($reponses as $questionId => $valeur) {
                if (!empty($valeur)) {
                    $this->saveReponse($questionId, $clientId, $valeur);
                }
            }
            
            // Mettre à jour le statut de participation
            $stmt = $this->pdo->prepare("
                UPDATE enquete_clients 
                SET statut = 'complete', date_reponse = NOW() 
                WHERE enquete_id = ? AND client_id = ?
            ");
            $stmt->execute([$enqueteId, $clientId]);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    /**
     * Obtient les statistiques complètes de réponses pour une enquête
     */
    public function getStatsForEnquete(int $enqueteId): array {
        // Statistiques globales
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(DISTINCT r.utilisateur_id) as clients_ayant_repondu,
                COUNT(DISTINCT q.id) as questions_total,
                COUNT(r.id) as reponses_total,
                AVG(CASE WHEN q.type_question = 'rating' THEN CAST(COALESCE(r.reponse_texte, r.reponse_numerique) AS DECIMAL(3,2)) END) as note_moyenne
            FROM questions q
            LEFT JOIN reponses r ON q.id = r.question_id
            WHERE q.enquete_id = ?
        ");
        $stmt->execute([$enqueteId]);
        $stats = $stmt->fetch();
        
        // Obtenir le nombre de clients assignés
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as clients_assignes
            FROM enquete_clients ec
            WHERE ec.enquete_id = ?
        ");
        $stmt->execute([$enqueteId]);
        $assignedStats = $stmt->fetch();
        
        // Statistiques par question
        $stmt = $this->pdo->prepare("
            SELECT 
                q.id,
                q.texte as libelle,
                q.type_question as type,
                COUNT(r.id) as total_reponses,
                COUNT(DISTINCT r.utilisateur_id) as clients_ayant_repondu
            FROM questions q
            LEFT JOIN reponses r ON q.id = r.question_id
            WHERE q.enquete_id = ?
            GROUP BY q.id, q.texte, q.type_question
            ORDER BY q.ordre_affichage ASC
        ");
        $stmt->execute([$enqueteId]);
        $questionStats = $stmt->fetchAll();
        
        return array_merge(
            $stats ?: [], 
            $assignedStats ?: [], 
            ['questions' => $questionStats]
        );
    }
    
    /**
     * Obtient l'analyse des réponses pour une question de type choix multiple
     */
    public function getChoiceAnalysis(int $questionId): array {
        $stmt = $this->pdo->prepare("
            SELECT valeur, COUNT(*) as count
            FROM {$this->table}
            WHERE question_id = ?
            GROUP BY valeur
            ORDER BY count DESC
        ");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtient l'analyse des réponses pour une question de type notation
     */
    public function getRatingAnalysis(int $questionId): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                AVG(CAST(valeur AS DECIMAL(3,2))) as moyenne,
                MIN(CAST(valeur AS DECIMAL(3,2))) as minimum,
                MAX(CAST(valeur AS DECIMAL(3,2))) as maximum,
                COUNT(*) as total
            FROM {$this->table}
            WHERE question_id = ? AND valeur REGEXP '^[0-9]+(\.[0-9]+)?$'
        ");
        $stmt->execute([$questionId]);
        return $stmt->fetch();
    }
    
    /**
     * Vérifie si un client a répondu à une enquête
     */
    public function hasClientResponded(int $enqueteId, int $clientId): bool {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM {$this->table} r
            JOIN questions q ON r.question_id = q.id
            WHERE q.enquete_id = ? AND r.utilisateur_id = ?
        ");
        $stmt->execute([$enqueteId, $clientId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtient toutes les réponses d'une enquête avec détails client
     */
    public function getByEnquete(int $enqueteId): array {
        $stmt = $this->pdo->prepare("
            SELECT r.*, 
                   q.texte as question_texte,
                   q.type_question as question_type,
                   u.nom as client_nom,
                   u.prenom as client_prenom,
                   u.email as client_email,
                   COALESCE(r.reponse_texte, CAST(r.reponse_numerique AS CHAR)) as valeur
            FROM {$this->table} r
            JOIN questions q ON r.question_id = q.id
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            WHERE q.enquete_id = ?
            ORDER BY u.nom, u.prenom, q.ordre_affichage
        ");
        $stmt->execute([$enqueteId]);
        return $stmt->fetchAll();
    }
}
