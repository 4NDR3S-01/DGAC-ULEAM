-- Configuración de base de datos para WordPress
CREATE DATABASE IF NOT EXISTS wordpress
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'wpuser'@'localhost'
  IDENTIFIED BY '0043fc294eed0bc74d2baae3506bf76d';

GRANT ALL PRIVILEGES ON wordpress.* TO 'wpuser'@'localhost';

FLUSH PRIVILEGES;
