CREATE DATABASE IF NOT EXISTS radovi
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE radovi;

CREATE TABLE IF NOT EXISTS diplomski_radovi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv_rada VARCHAR(600) NOT NULL,
    tekst_rada TEXT,
    link_rada VARCHAR(600) NOT NULL,
    oib_tvrtke VARCHAR(11) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_link (link_rada),
    KEY idx_oib (oib_tvrtke),
    KEY idx_created (created_at)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
