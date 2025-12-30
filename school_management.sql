-- school_management_congo.sql
-- Exécuter d'abord cette commande pour supprimer la base si elle existe
DROP DATABASE IF EXISTS school_management_congo;
CREATE DATABASE school_management_congo;
USE school_management_congo;

-- Table des écoles
CREATE TABLE schools (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_name VARCHAR(200) NOT NULL,
    school_code VARCHAR(50) UNIQUE NOT NULL,
    logo VARCHAR(255),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    director_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Table des administrateurs par école
CREATE TABLE school_admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('super_admin', 'admin', 'accountant', 'teacher') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id)
);

-- Table des classes
CREATE TABLE classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    class_name VARCHAR(50) NOT NULL,
    level ENUM('Maternelle', 'Primaire', 'Secondaire', 'Lycée') NOT NULL,
    section VARCHAR(50),
    max_students INT DEFAULT 40,
    annual_fee DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id)
);

-- Table des élèves
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    class_id INT,
    student_code VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('M', 'F') NOT NULL,
    birth_date DATE,
    birth_place VARCHAR(100),
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    parent_phone VARCHAR(20),
    parent_email VARCHAR(100),
    address TEXT,
    enrollment_date DATE,
    status ENUM('active', 'graduated', 'transferred', 'inactive') DEFAULT 'active',
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id),
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

-- Table des enseignants
CREATE TABLE teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    teacher_code VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('M', 'F') NOT NULL,
    specialization VARCHAR(100),
    diploma VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    hire_date DATE,
    salary DECIMAL(12,2) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id)
);

-- Table des paiements (en Francs CFA)
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    student_id INT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    payment_type ENUM('inscription', 'mensualite', 'transport', 'uniforme', 'autre') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    amount_paid DECIMAL(12,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'XAF',
    due_date DATE,
    payment_date DATE,
    payment_method ENUM('cash', 'bank_transfer', 'mobile_money', 'check') DEFAULT 'cash',
    status ENUM('pending', 'partial', 'paid', 'overdue') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Table des notes
CREATE TABLE grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    student_id INT,
    class_id INT,
    subject VARCHAR(100) NOT NULL,
    trimester ENUM('1', '2', '3') NOT NULL,
    grade DECIMAL(5,2),
    coefficient INT DEFAULT 1,
    teacher_id INT,
    academic_year YEAR,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);

-- Table des matières
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    subject_name VARCHAR(100) NOT NULL,
    level ENUM('Maternelle', 'Primaire', 'Secondaire', 'Lycée') NOT NULL,
    teacher_id INT,
    coefficient INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);

-- Table des emplois du temps
CREATE TABLE schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    class_id INT,
    subject_id INT,
    teacher_id INT,
    day_of_week ENUM('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);

-- Insérer des données de test
INSERT INTO schools (school_name, school_code, address, phone, email, director_name) 
VALUES 
('École Excellence Brazzaville', 'ECO-001', 'Brazzaville, Plateau', '+242 06 111 2233', 'contact@excellence.cg', 'Dr. Jean KABEYA'),
('Complexe Scolaire Lumière', 'ECO-002', 'Poto-Poto, Brazzaville', '+242 05 222 3344', 'info@lumiere.cg', 'Mme. Marie MBOUALA');

-- Mot de passe: admin123 (hashé)
INSERT INTO school_admins (school_id, username, password, full_name, email, phone, role) 
VALUES 
(1, 'admin', '$2y$10$Vh4X5qQn7t8Z9u0i1Y2Z3A4B5C6D7E8F9G0H1I2J3K4L5M6N7O8P9Q', 'Administrateur Principal', 'admin@excellence.cg', '+242 06 123 4567', 'super_admin'),
(1, 'comptable', '$2y$10$Vh4X5qQn7t8Z9u0i1Y2Z3A4B5C6D7E8F9G0H1I2J3K4L5M6N7O8P9Q', 'Comptable École', 'comptable@excellence.cg', '+242 06 234 5678', 'accountant');

-- Insérer quelques classes
INSERT INTO classes (school_id, class_name, level, section, max_students, annual_fee) VALUES
(1, 'CP1', 'Primaire', 'A', 35, 250000),
(1, 'CP2', 'Primaire', 'A', 35, 250000),
(1, 'CE1', 'Primaire', 'A', 35, 250000),
(1, 'CE2', 'Primaire', 'A', 35, 250000),
(1, 'CM1', 'Primaire', 'A', 35, 250000),
(1, 'CM2', 'Primaire', 'A', 35, 250000),
(1, '6ème', 'Secondaire', 'A', 40, 350000),
(1, '5ème', 'Secondaire', 'A', 40, 350000),
(1, '4ème', 'Secondaire', 'A', 40, 350000),
(1, '3ème', 'Secondaire', 'A', 40, 350000),
(1, 'Seconde', 'Lycée', 'A', 40, 450000),
(1, 'Première', 'Lycée', 'A', 40, 450000),
(1, 'Terminale', 'Lycée', 'A', 40, 450000);

-- Insérer quelques enseignants
INSERT INTO teachers (school_id, teacher_code, first_name, last_name, gender, specialization, phone, hire_date, salary) VALUES
(1, 'ENS-001', 'Paul', 'MAKOSSO', 'M', 'Mathématiques', '+242 06 111 2222', '2020-09-01', 350000),
(1, 'ENS-002', 'Julie', 'NGOMA', 'F', 'Français', '+242 05 222 3333', '2021-09-01', 320000),
(1, 'ENS-003', 'David', 'KOULENGANA', 'M', 'Sciences', '+242 04 333 4444', '2019-09-01', 380000);

-- Ajouter cette table pour les rôles personnalisés
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT,
    role_name VARCHAR(50) NOT NULL,
    role_description TEXT,
    permissions JSON,
    is_system_role BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id)
);

-- Insérer les rôles système par défaut
INSERT INTO roles (school_id, role_name, role_description, permissions, is_system_role) VALUES
(NULL, 'super_admin', 'Super Administrateur', '["*"]', TRUE),
(NULL, 'admin', 'Administrateur École', '["manage_students", "manage_teachers", "manage_classes", "manage_payments", "view_reports", "manage_users"]', TRUE),
(NULL, 'accountant', 'Comptable', '["manage_payments", "view_financial_reports", "generate_invoices"]', TRUE),
(NULL, 'teacher', 'Enseignant', '["view_students", "manage_grades", "view_schedule", "view_attendance"]', TRUE),
(NULL, 'secretary', 'Secrétaire', '["manage_students", "manage_parents", "view_schedule", "send_notifications"]', TRUE),
(NULL, 'parent', 'Parent', '["view_child_grades", "view_child_attendance", "make_payments", "view_schedule"]', TRUE),
(NULL, 'student', 'Élève', '["view_grades", "view_schedule", "view_attendance"]', TRUE);

-- Mettre à jour la table school_admins pour utiliser role_id au lieu de role ENUM
ALTER TABLE school_admins 
ADD COLUMN role_id INT AFTER role,
ADD FOREIGN KEY (role_id) REFERENCES roles(id);

-- Mettre à jour les enregistrements existants
UPDATE school_admins sa 
JOIN roles r ON sa.role = r.role_name 
SET sa.role_id = r.id;

-- Optionnel : Supprimer la colonne role ENUM après migration
-- ALTER TABLE school_admins DROP COLUMN role;