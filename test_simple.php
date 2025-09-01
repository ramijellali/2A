<?php
/**
 * Test simple de création d'utilisateur
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Models/Utilisateur.php';

// Test de création d'utilisateur avec des données nettoyées
$data = [
    'nom' => 'Louay',
    'prenom' => 'Test',
    'email' => 'louay.simple@example.com',
    'mot_de_passe' => 'password123',
    'role' => 'client',
    'statut' => 'actif'
];

echo "=== TEST CREATION SIMPLE ===\n";
echo "Données à insérer :\n";
print_r($data);

try {
    $utilisateurModel = new Utilisateur();
    
    echo "\nTest createUser...\n";
    $userId = $utilisateurModel->createUser($data);
    echo "Utilisateur créé avec l'ID : " . $userId . "\n";
    
    // Vérification
    echo "\nVérification en base :\n";
    $user = $utilisateurModel->findById($userId);
    if ($user) {
        echo "Utilisateur trouvé : " . $user['nom'] . " " . $user['prenom'] . "\n";
        echo "Email : " . $user['email'] . "\n";
        echo "Rôle : " . $user['role'] . "\n";
    } else {
        echo "Erreur : utilisateur non trouvé après création\n";
    }
    
} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
