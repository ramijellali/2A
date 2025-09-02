SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Corriger les noms d'utilisateurs avec des problèmes d'encodage
UPDATE utilisateurs SET 
    nom = 'Système',
    prenom = 'Admin'
WHERE id = 1;

UPDATE utilisateurs SET 
    nom = 'Dupont',
    prenom = 'Marie'
WHERE id = 2;

UPDATE utilisateurs SET 
    nom = 'Moreau',
    prenom = 'Sophie'
WHERE id = 3;

UPDATE utilisateurs SET 
    nom = 'Martin',
    prenom = 'Pierre'
WHERE id = 4;

UPDATE utilisateurs SET 
    nom = 'Bernard',
    prenom = 'Julie'
WHERE id = 5;
