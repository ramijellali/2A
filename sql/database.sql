-- Schéma de base de données pour le système d'enquêtes de satisfaction
-- ====================================================================

-- Table des utilisateurs (admin, agents, clients)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'agent', 'client') NOT NULL DEFAULT 'client',
    statut ENUM('actif', 'inactif') NOT NULL DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des campagnes
CREATE TABLE IF NOT EXISTS campagnes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut ENUM('en_preparation', 'active', 'terminee', 'suspendue') NOT NULL DEFAULT 'en_preparation',
    created_by INT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_statut (statut),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des enquêtes
CREATE TABLE IF NOT EXISTS enquetes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    campagne_id INT NOT NULL,
    created_by INT NOT NULL,
    statut ENUM('brouillon', 'active', 'fermee') NOT NULL DEFAULT 'brouillon',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (campagne_id) REFERENCES campagnes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_campagne (campagne_id),
    INDEX idx_statut (statut),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des questions
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enquete_id INT NOT NULL,
    texte TEXT NOT NULL,
    type_question ENUM('texte_libre', 'choix_multiple', 'notation', 'oui_non') NOT NULL,
    options_json TEXT, -- Pour stocker les options des QCM en JSON
    obligatoire BOOLEAN NOT NULL DEFAULT FALSE,
    ordre_affichage INT NOT NULL DEFAULT 1,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enquete_id) REFERENCES enquetes(id) ON DELETE CASCADE,
    INDEX idx_enquete (enquete_id),
    INDEX idx_type (type_question),
    INDEX idx_ordre (ordre_affichage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réponses
CREATE TABLE IF NOT EXISTS reponses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    reponse_texte TEXT,
    reponse_numerique INT,
    date_reponse TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_question (question_id),
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_date (date_reponse),
    UNIQUE KEY unique_user_question (utilisateur_id, question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de liaison enquêtes-clients (pour l'assignation d'enquêtes)
CREATE TABLE IF NOT EXISTS enquete_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enquete_id INT NOT NULL,
    client_id INT NOT NULL,
    statut ENUM('envoye', 'en_cours', 'complete', 'expire') NOT NULL DEFAULT 'envoye',
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_debut TIMESTAMP NULL,
    date_fin TIMESTAMP NULL,
    FOREIGN KEY (enquete_id) REFERENCES enquetes(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_enquete (enquete_id),
    INDEX idx_client (client_id),
    INDEX idx_statut (statut),
    INDEX idx_date_envoi (date_envoi),
    UNIQUE KEY unique_enquete_client (enquete_id, client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des données de démonstration
-- ====================================

-- Utilisateurs de démonstration
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, statut) VALUES
('Admin', 'Système', 'admin@enquetes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'actif'),
('Dupont', 'Marie', 'marie.dupont@enquetes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', 'actif'),
('Moreau', 'Sophie', 'sophie.moreau@client.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'actif'),
('Martin', 'Pierre', 'pierre.martin@client.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'actif'),
('Bernard', 'Julie', 'julie.bernard@enquetes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', 'actif');

-- Campagnes de démonstration
INSERT INTO campagnes (nom, description, date_debut, date_fin, statut, created_by) VALUES
('Satisfaction Service Client 2025', 'Évaluation de la qualité du service client pour le premier trimestre 2025', '2025-01-01', '2025-03-31', 'active', 1),
('Enquête Produits', 'Retours clients sur nos nouveaux produits', '2025-02-01', '2025-04-30', 'active', 1),
('Support Technique', 'Évaluation du support technique', '2025-01-15', '2025-02-15', 'terminee', 2);

-- Enquêtes de démonstration
INSERT INTO enquetes (titre, description, campagne_id, created_by, statut) VALUES
('Satisfaction Accueil Client', 'Évaluation de la qualité de l\'accueil téléphonique', 1, 2, 'active'),
('Temps de Réponse', 'Évaluation des délais de traitement des demandes', 1, 2, 'active'),
('Qualité Produit X', 'Retours sur le nouveau produit X', 2, 2, 'active');

-- Questions de démonstration
INSERT INTO questions (enquete_id, texte, type_question, options_json, obligatoire, ordre_affichage) VALUES
(1, 'Comment évaluez-vous la qualité de l\'accueil téléphonique ?', 'notation', NULL, TRUE, 1),
(1, 'Le conseiller était-il disponible et à l\'écoute ?', 'oui_non', NULL, TRUE, 2),
(1, 'Avez-vous des suggestions d\'amélioration ?', 'texte_libre', NULL, FALSE, 3),
(2, 'Le délai de traitement de votre demande était-il satisfaisant ?', 'choix_multiple', '["Très satisfaisant", "Satisfaisant", "Moyennement satisfaisant", "Peu satisfaisant", "Pas du tout satisfaisant"]', TRUE, 1),
(3, 'Notez la qualité générale du produit', 'notation', NULL, TRUE, 1),
(3, 'Recommanderiez-vous ce produit ?', 'oui_non', NULL, TRUE, 2);

-- Quelques réponses de démonstration
INSERT INTO reponses (question_id, utilisateur_id, reponse_numerique, reponse_texte) VALUES
(1, 3, 4, NULL),
(2, 3, 1, NULL),
(3, 3, NULL, 'Le service était très professionnel'),
(4, 3, NULL, 'Satisfaisant'),
(1, 4, 5, NULL),
(2, 4, 1, NULL),
(5, 4, 4, NULL),
(6, 4, 1, NULL);

-- Assignations d'enquêtes aux clients
INSERT INTO enquete_clients (enquete_id, client_id, statut, date_debut, date_fin) VALUES
(1, 3, 'complete', '2025-01-15 10:00:00', '2025-01-15 10:30:00'),
(2, 3, 'en_cours', '2025-01-20 14:00:00', NULL),
(3, 3, 'envoye', NULL, NULL),
(1, 4, 'complete', '2025-01-16 09:00:00', '2025-01-16 09:45:00'),
(2, 4, 'envoye', NULL, NULL),
(3, 4, 'envoye', NULL, NULL);