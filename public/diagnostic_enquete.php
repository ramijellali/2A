<?php
/**
 * Page de diagnostic pour tester l'enquête client
 */

// Test de connexion à la base de données
try {
    require_once '../config/database.php';
    require_once '../app/Models/Enquete.php';
    require_once '../app/Models/Question.php';
    require_once '../app/Models/Reponse.php';
    
    echo "<h1>Diagnostic - Enquête Client</h1>";
    
    // Test de l'enquête
    $enqueteModel = new Enquete();
    echo "<h2>Test modèle Enquête</h2>";
    
    // Simuler l'ID client 3 (Sophie Moreau)
    $clientId = 3;
    $enqueteId = 1;
    
    echo "<h3>Enquêtes pour client ID $clientId :</h3>";
    $enquetes = $enqueteModel->getForClient($clientId);
    echo "<pre>";
    var_dump($enquetes);
    echo "</pre>";
    
    if (!empty($enquetes)) {
        $enquete = $enquetes[0];
        echo "<h3>Première enquête trouvée :</h3>";
        echo "<pre>";
        var_dump($enquete);
        echo "</pre>";
        
        // Test des questions
        $questionModel = new Question();
        echo "<h3>Questions pour enquête ID $enqueteId :</h3>";
        $questions = $questionModel->getByEnquete($enqueteId);
        echo "<pre>";
        var_dump($questions);
        echo "</pre>";
        
        // Test des réponses
        $reponseModel = new Reponse();
        echo "<h3>Réponses pour client ID $clientId et enquête ID $enqueteId :</h3>";
        $reponses = $reponseModel->getByClientAndEnquete($clientId, $enqueteId);
        echo "<pre>";
        var_dump($reponses);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>Aucune enquête trouvée pour ce client</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Erreur :</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
