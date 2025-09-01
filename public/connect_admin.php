<?php
// Connexion automatique comme admin
session_start();

// Simuler un utilisateur admin connecté (récupéré de la DB)
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ? LIMIT 1");
    $stmt->execute(['admin@enquetes.com']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Utiliser les vraies données de l'admin
        unset($admin['mot_de_passe']); // Ne pas stocker le mot de passe en session
        $_SESSION['user'] = $admin;
        
        echo "<h1>✅ Connexion Admin Réussie</h1>";
        echo "<p>Vous êtes maintenant connecté comme : <strong>" . htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']) . "</strong></p>";
        echo "<p>Rôle : <span class='badge bg-danger'>" . ucfirst($admin['role']) . "</span></p>";
    } else {
        // Fallback si l'admin n'existe pas
        $_SESSION['user'] = [
            'id' => 1,
            'nom' => 'Admin',
            'prenom' => 'Super',
            'email' => 'admin@enquetes.com',
            'role' => 'admin',
            'statut' => 'actif',
            'date_creation' => date('Y-m-d H:i:s'),
            'date_modification' => date('Y-m-d H:i:s')
        ];
        echo "<h1>⚠️ Connexion Admin (Fallback)</h1>";
        echo "<p>Utilisateur admin par défaut créé.</p>";
    }
    
} catch (Exception $e) {
    // En cas d'erreur de DB, utiliser un admin par défaut
    $_SESSION['user'] = [
        'id' => 1,
        'nom' => 'Admin',
        'prenom' => 'Super',
        'email' => 'admin@enquetes.com',
        'role' => 'admin',
        'statut' => 'actif',
        'date_creation' => date('Y-m-d H:i:s'),
        'date_modification' => date('Y-m-d H:i:s')
    ];
    echo "<h1>⚠️ Connexion Admin (Erreur DB)</h1>";
    echo "<p>Erreur : " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<div style='margin: 20px 0;'>";
echo "<h3>🎯 Navigation rapide :</h3>";
echo "<p><a href='/?controller=Admin&action=dashboard' class='btn btn-primary'>📊 Tableau de bord admin</a></p>";
echo "<p><a href='/?controller=Admin&action=users' class='btn btn-success'>👥 Gérer les utilisateurs</a></p>";
echo "<p><a href='/?controller=Admin&action=campagnes' class='btn btn-warning'>📢 Gérer les campagnes</a></p>";
echo "<p><a href='/?controller=Admin&action=createUser' class='btn btn-info'>➕ Créer un utilisateur</a></p>";
echo "</div>";
?>
