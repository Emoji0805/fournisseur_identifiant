CREATE DATABASE identifiant;
\c identifiant;
CREATE TABLE roles(
    idrole SERIAL PRIMARY KEY,
    nom VARCHAR(100)
);

CREATE TABLE Utilisateurs(
    idUtilisateur SERIAL PRIMARY KEY ,
    idrole INT,
    nom VARCHAR(100),
    email VARCHAR(200),
    motDePasse VARCHAR(200),
    FOREIGN KEY (idrole) REFERENCES roles(idrole)
);

INSERT INTO roles (nom) VALUES ('admin');
INSERT INTO roles (nom) VALUES ('membres');

INSERT INTO Utilisateurs (idrole, nom, email, motDePasse) 
VALUES (1, 'Jean Dupont', 'jean.dupont@email.com', 'MotDePasse123');
