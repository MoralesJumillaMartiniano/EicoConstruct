CREATE DATABASE IF NOT EXISTS instrutec_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE instrutec_db;

-- Tabla de usuarios registrados
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de historial de lecturas (Arduino)
CREATE TABLE IF NOT EXISTS lecturas_temperatura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperatura_real FLOAT NOT NULL,
    humedad FLOAT NOT NULL,
    setpoint_potenciometro FLOAT NOT NULL,
    calefaccion_activa TINYINT(1) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);