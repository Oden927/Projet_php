CREATE DATABASE gestion_cours;
USE gestion_cours;

CREATE TABLE Utilisateur (
    utilisateur_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    prenom VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Stocké en hash
    telephone VARCHAR(100),
    role ENUM('eleve', 'professeur') NOT NULL, -- Détermine le type d'utilisateur
    date_naissance DATE,  -- Uniquement pour les élèves
    adresse TEXT,         -- Uniquement pour les élèves
    classe_id INT,        -- Si élève, référence sa classe
    FOREIGN KEY (classe_id) REFERENCES Classe(classe_id)
);

CREATE TABLE Utilisateur (
    utilisateur_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    prenom VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Stocké en hash
    telephone VARCHAR(100),
    role ENUM('eleve', 'professeur') NOT NULL, -- Détermine le type d'utilisateur
    date_naissance DATE,  -- Uniquement pour les élèves
    adresse TEXT,         -- Uniquement pour les élèves
    classe_id INT,        -- Si élève, référence sa classe
    FOREIGN KEY (classe_id) REFERENCES Classe(classe_id)
);

-- Table Classe
CREATE TABLE Classe (
    classe_id INT PRIMARY KEY AUTO_INCREMENT,
    nom_classe VARCHAR(150) NOT NULL,
    nbre_eleve INT DEFAULT 0,
    prof_principal INT, -- Référence le professeur principal dans Utilisateur
    FOREIGN KEY (prof_principal) REFERENCES Utilisateur(utilisateur_id)
);

-- Table Cours
CREATE TABLE Cours (
    cours_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    duree INT NOT NULL,
    classe_cours INT,
    debut_cours DATETIME,
    prof_cours INT, -- Référence l'utilisateur qui est professeur
    FOREIGN KEY (classe_cours) REFERENCES Classe(classe_id),
    FOREIGN KEY (prof_cours) REFERENCES Utilisateur(utilisateur_id)
);

-- Table Absence
CREATE TABLE Absence (
    absence_id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT, -- Référence l'élève concerné
    date_absence DATE NOT NULL,
    debut TIME NOT NULL,
    fin TIME NOT NULL,
    raison TEXT,
    justificatif VARCHAR(250),
    valide BOOLEAN DEFAULT FALSE,
    professeur_id INT, -- Professeur qui valide l'absence
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(utilisateur_id),
    FOREIGN KEY (professeur_id) REFERENCES Utilisateur(utilisateur_id)
);
