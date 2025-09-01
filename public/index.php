<?php
/**
 * Point d'entrée principal de l'application
 * Système de gestion d'enquêtes de satisfaction
 */

// Activer l'affichage des erreurs en mode développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Chargement du bootstrap
    require_once __DIR__ . '/../config/bootstrap.php';

    // Démarrage de l'application
    Application::run();
} catch (Throwable $e) {
    echo "<h1>Erreur de l'application</h1>";
    echo "<p><strong>Message :</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
