CREATE DATABASE IF NOT EXISTS schoolmate;
USE schoolmate;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS montessories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS registered_schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(255) NOT NULL,
    school_type VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    proof VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Montessori schools
INSERT INTO montessories (name, location, image) VALUES
('Montessori Kinderworld', '239 Tanka Prasad Marg, Kathmandu', 'images/montessori_kinderworld.jpg'),
('Bakena Bal Batika School', 'Chetana Marg, Bharatpur', 'images/bakena_bal_batika.jpg'),
('Little Angels Montessori', 'Lake Side, Pokhara', 'images/little_angels_pokhara.jpg'),
('Dharan Montessori', 'Sun marg, dharan-1, Dharan', 'images/dharan_montessori.jpg');


INSERT INTO schools (name, location, image) VALUES
('Kathmandu World School', 'Surya Binayak - 7, Kathmandu', 'images/kathmandu_world.jpg'),
('Ankuram Academy', 'Teresale chowk 1, Bharatpur', 'images/ankuram_academy.jpg'),
('Delhi Public School', 'Bishal Chowk, Biratnagar', 'images/dps_biratnagar.jpg'),
('Fishtail Academy Secondary School', 'Tallo Birauta, Pokhara', 'images/fishtail_academy.jpg');
