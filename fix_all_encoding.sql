SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Corriger les enquêtes
UPDATE enquetes SET 
    titre = 'Satisfaction Accueil Client', 
    description = 'Évaluation de la qualité de l\'accueil téléphonique' 
WHERE id = 1;

UPDATE enquetes SET 
    titre = 'Temps de Réponse', 
    description = 'Évaluation des délais de traitement des demandes' 
WHERE id = 2;

UPDATE enquetes SET 
    titre = 'Qualité Produit X', 
    description = 'Retours sur le nouveau produit X' 
WHERE id = 3;

-- Corriger les questions
UPDATE questions SET 
    texte = 'Comment évaluez-vous la qualité de notre accueil téléphonique ?',
    options_json = '["Excellent", "Très bon", "Bon", "Moyen", "Mauvais"]'
WHERE id = 1;

UPDATE questions SET 
    texte = 'Nos conseillers ont-ils été courtois et professionnels ?',
    options_json = '["Tout à fait d\'accord", "D\'accord", "Neutre", "Pas d\'accord", "Pas du tout d\'accord"]'
WHERE id = 2;

UPDATE questions SET 
    texte = 'Avez-vous des commentaires ou suggestions d\'amélioration ?'
WHERE id = 3;

UPDATE questions SET 
    texte = 'Le délai de traitement de votre demande était-il satisfaisant ?',
    options_json = '["Très satisfaisant", "Satisfaisant", "Moyennement satisfaisant", "Peu satisfaisant", "Pas du tout satisfaisant"]'
WHERE id = 4;
