<?php
/**
 * Script de diagnostic pour la création d'utilisateur
 */

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/app/Models/Utilisateur.php';

// Données de test
$data = [
    'nom' => 'Test',
    'prenom' => 'User',
    'email' => 'test.user@example.com',
    'mot_de_passe' => 'password123',
    'role' => 'client',
    'statut' => 'actif'
];

try {
    $utilisateurModel = new Utilisateur();
    
    echo "=== DIAGNOSTIC CRÉATION UTILISATEUR ===\n";
    echo "Données à insérer :\n";
    print_r($data);
    
    // Test de validation email
    echo "\nTest emailExists : ";
    $emailExists = $utilisateurModel->emailExists($data['email']);
    echo $emailExists ? "EXISTE DÉJÀ" : "DISPONIBLE";
    echo "\n";
    
    // Test de création
    echo "\nTentative de création...\n";
    $userId = $utilisateurModel->createUser($data);
    echo "Utilisateur créé avec l'ID : " . $userId . "\n";
    
    // Vérification
    $user = $utilisateurModel->findById($userId);
    echo "Utilisateur récupéré :\n";
    print_r($user);
    
} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
