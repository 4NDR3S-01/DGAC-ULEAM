-- Quita de la copia de trabajo todo lo que no debe llegar a producción (solo se aplica sobre la base de trabajo).
-- Todas las cuentas quedan bloqueadas salvo el administrador de producción `dgac_uleam`, cuyo hash ya viene en el volcado de origen.
-- Ninguna otra contraseña de desarrollo viaja: hay que restablecerla (ver README-DESPLIEGUE.md, paso 7).
UPDATE wp_users SET user_pass = CONCAT('!bloqueada!', SHA2(CONCAT(RAND(), UUID(), user_login), 256)), user_activation_key = ''
 WHERE user_login <> 'dgac_uleam';
UPDATE wp_users SET user_activation_key = '' WHERE user_login = 'dgac_uleam';
DELETE FROM wp_usermeta WHERE meta_key = 'session_tokens';
DELETE FROM wp_options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%';
