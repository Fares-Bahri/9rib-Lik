
USE gestion_projet;

CREATE TABLE IF NOT EXISTS password_resets (
  email VARCHAR(255) NOT NULL PRIMARY KEY,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  INDEX idx_password_resets_expiry (expires_at)
) ENGINE=InnoDB;


ALTER TABLE utilisateur
  ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user' AFTER password;


CREATE TABLE IF NOT EXISTS fermes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  emplacement VARCHAR(150) NOT NULL,
  aire DECIMAL(8,2) NOT NULL,
  type_agriculture VARCHAR(50) NOT NULL,
  description TEXT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  categorie VARCHAR(50) NOT NULL,
  prix DECIMAL(8,2) NOT NULL,
  quantite_stock INT NOT NULL DEFAULT 0,
  unite VARCHAR(20) NOT NULL,
  date_recolte DATE NULL,
  ferme_id INT NOT NULL,
  FOREIGN KEY (ferme_id) REFERENCES fermes(id) ON DELETE CASCADE
) ENGINE=InnoDB;


INSERT INTO utilisateur (nom, prenom, email, password, role) VALUES
('Admin', 'Admin', 'admin@ferme.tn', '$2y$10$pjiQRyW1Ob0R2jxn88oCLuEJdOLPBMUCMO1LFU.RJ8IZX.WOfZ3lS', 'admin');


INSERT INTO fermes (nom, emplacement, aire, type_agriculture, description) VALUES
('Ferme El Baraka', 'Béja', 20, 'Céréaliculture', 'Exploitation familiale de blé dur et d''orge, menée en rotation des cultures.'),
('Domaine des Oliviers', 'Sfax', 15, 'Arboriculture', 'Huile d''olive pressée à froid sur le domaine.'),
('Jardin du Cap Bon', 'Nabeul', 12, 'Maraîchage', 'Légumes et agrumes de saison, récoltés à la commande.');

INSERT INTO produits (nom, categorie, prix, quantite_stock, unite, date_recolte, ferme_id) VALUES
('Blé dur', 'Céréales', 2.40, 1200, 'kg', '2026-06-20', 1),
('Orge', 'Céréales', 1.80, 800, 'kg', '2026-06-18', 1),
('Fèves sèches', 'Légumineuses', 4.50, 0, 'kg', '2026-05-30', 1),
('Huile d''olive', 'Huiles', 28.00, 40, 'L', '2025-12-05', 2),
('Tomates', 'Légumes', 2.20, 300, 'kg', '2026-11-20', 3),
('Oranges', 'Fruits', 1.90, 500, 'kg', '2026-11-18', 3);
