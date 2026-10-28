-- Quita de la copia de trabajo todo lo que no debe llegar a producción (solo se aplica sobre la base de trabajo).
-- Ninguna contraseña de desarrollo viaja: tras importar hay que fijar una nueva (ver DESPLIEGUE.md, paso 3).
UPDATE wp_users SET user_pass = CONCAT('!bloqueada!', SHA2(CONCAT(RAND(), UUID(), user_login), 256)), user_activation_key = '';
DELETE FROM wp_usermeta WHERE meta_key = 'session_tokens';
DELETE FROM wp_options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%';
