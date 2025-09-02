SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

UPDATE enquetes SET 
    titre = 'Temps de Réponse', 
    description = 'Évaluation des délais de traitement des demandes' 
WHERE id = 2;

UPDATE questions SET 
    texte = 'Le délai de traitement de votre demande était-il satisfaisant ?',
    options_json = '["Très satisfaisant", "Satisfaisant", "Moyennement satisfaisant", "Peu satisfaisant", "Pas du tout satisfaisant"]'
WHERE id = 4;
