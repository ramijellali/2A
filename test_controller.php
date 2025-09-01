<?php
/**
 * Test de création d'utilisateur avec données simulées du formulaire
 */

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/app/Controllers/AdminController.php';

// Simuler une session admin
session_start();
$_SESSION['user'] = [
    'id' => 1,
    'email' => 'admin@enquetes.com',
    'role' => 'admin'
];

// Simuler les données POST du formulaire
$_POST = [
    'csrf_token' => 'test_token_123',
    'nom' => 'Louay',
    'prenom' => 'Test',
    'email' => 'louay.test2@example.com',
    'mot_de_passe' => 'password123',
    'role' => 'client',
    'statut' => 'actif'
];

// Simuler le token CSRF en session
$_SESSION['csrf_token'] = 'test_token_123';

echo "=== TEST CREATION UTILISATEUR VIA CONTROLEUR ===\n";
echo "Données POST :\n";
print_r($_POST);

try {
    $controller = new AdminController();
    
    // Capturer le buffer de sortie pour éviter les redirections
    ob_start();
    $controller->storeUser();
    $output = ob_get_clean();
    
    echo "Sortie du contrôleur : " . $output . "\n";
    
    // Vérifier les messages flash
    if (isset($_SESSION['flash'])) {
        echo "Messages flash :\n";
        print_r($_SESSION['flash']);
    }
    
} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}

// Vérifier si l'utilisateur a été créé
require_once __DIR__ . '/app/Models/Utilisateur.php';
$utilisateurModel = new Utilisateur();
$user = $utilisateurModel->findWhere('email', 'louay.test2@example.com');
if (!empty($user)) {
    echo "Utilisateur créé avec succès :\n";
    print_r($user[0]);
} else {
    echo "Utilisateur non trouvé dans la base de données\n";
}
