# Despliegue en https://dgacweb.uleam.edu.ec/ (máquina virtual, servidor privado)

Sitio en la **raíz del dominio** (sin subcarpeta). El dominio hoy responde `403` con Apache y una barrera de JavaScript (cookie `ir_ok`) delante, sin WordPress instalado: instalación limpia.

## Qué se sube (carpeta `dist/`, generada, no va a git)

| Archivo | Contenido |
|---|---|
| `paquete-uleam-calidad.tar.gz` | WordPress + tema `uleam-calidad` + plugin `gutena-tabs` + `.htaccess` y `uploads/.htaccess` endurecidos. **Sin** `wp-config.php`, sin `uploads/`, sin mu-plugin de desarrollo. |
| `wordpress_deploy-AAAAMMDD.sql.gz` | Base de datos con URLs `https://dgacweb.uleam.edu.ec`. **Todas las contraseñas de usuario vienen bloqueadas** y sin sesiones/transitorios. |
| `volcado-origen.sql` | Solo respaldo local con hashes de usuarios. **No subir.** |

Regenerar: `MYSQL_ROOT_PASSWORD=<root del contenedor> herramientas/despliegue/preparar-paquete.sh https://dgacweb.uleam.edu.ec wordpress_deploy --nuevo-volcado`

## Requisitos de la VM

- Apache 2.4 con `mod_rewrite`, `mod_headers`, `mod_ssl`; `AllowOverride All` (o `FileInfo, AuthConfig, Options`) en el DocumentRoot.
- PHP 8.1+ con `mysqli mbstring gd intl xml zip curl imagick soap`. MariaDB/MySQL 10.5+/8.
- Certificado TLS válido para `dgacweb.uleam.edu.ec` (HTTP → HTTPS).
- ~4 GB libres (`uploads/` pesa 3,2 GB).

## Pasos

**0. Respaldo** de lo que exista en el servidor (archivos y base) antes de tocar nada.

**1. Código.** Extraer el `.tar.gz` en el DocumentRoot (debe quedar `index.php`, `wp-admin/`, `.htaccess` ahí). Propietario: usuario del sistema distinto de `www-data`; permisos `755` en carpetas y `644` en archivos, y escritura para `www-data` solo en `wp-content/uploads/`.

**2. Uploads.** Desde el equipo de desarrollo (los uploads no van en el paquete):
```bash
rsync -av wordpress/wp-content/uploads/ USUARIO@VM:/ruta/sitio/wp-content/uploads/
```
Comprobar que `uploads/.htaccess` (viene en el paquete) sigue ahí; impide ejecutar PHP subido.

**3. Base de datos.** Crear base y usuario **nuevos** con clave larga y única, privilegios solo sobre esa base y solo desde `localhost`:
```bash
gunzip -c wordpress_deploy-AAAAMMDD.sql.gz | mysql -u USUARIO -p NOMBRE_BD
```
Fijar la contraseña del administrador (ninguna importada es utilizable). Con WP-CLI: `wp user update 1 --user_pass='<clave-larga>'`; sin WP-CLI: `UPDATE wp_users SET user_pass = MD5('<clave-temporal-larga>') WHERE ID = 1;` y cambiarla al entrar. Revisar `Ajustes → Generales → correo del administrador` (viene `admin@example.com`).

**4. `wp-config.php`.** Copiar `herramientas/despliegue/wp-config.produccion.php.example` a `wp-config.php`, completar BD y **salts nuevos** (<https://api.wordpress.org/secret-key/1.1/salt/>). Permisos `640`, propietario del usuario del sistema. Para el asistente, definir ahí `ULEAM_GROQ_API_KEY` / `ULEAM_CLAUDE_API_KEY` (no guardarlas en el panel) y `ULEAM_PROXY_CONFIABLE` si hay proxy delante.

**5. Enlaces permanentes.** `Ajustes → Enlaces permanentes → Guardar`.

**6. Cuentas.** Eliminar o desactivar cuentas que no correspondan; contraseñas únicas y, si es posible, 2FA para administradores.

## Verificación

- [ ] `https://dgacweb.uleam.edu.ec/` carga con imágenes; `http://` redirige a `https://`.
- [ ] `curl -I /wp-config.php`, `/xmlrpc.php`, `/readme.html`, `/.git/HEAD`, `/wp-content/uploads/.htaccess` → **403/404**.
- [ ] `curl -I` muestra `Strict-Transport-Security`, `X-Content-Type-Options`, `Content-Security-Policy`.
- [ ] Un `.php` de prueba dentro de `uploads/` NO se ejecuta (borrarlo después).
- [ ] `/wp-admin/` solicita login; `/?author=1` no revela usuarios.
- [ ] Menú, Gestión de Procesos, un PDF y un DOCX descargan; redirecciones antiguas dan 301.
- [ ] Un archivo inexistente en `uploads/` da **404** (no redirige a la portada).
- [ ] El asistente responde y limita tras muchas consultas.

## Endurecimiento de la VM

- Firewall: solo 80/443 públicos y SSH restringido; MariaDB escucha en `127.0.0.1` (`bind-address`).
- Actualizaciones de seguridad del SO; fail2ban para SSH/wp-login; respaldos diarios de base y `uploads/` fuera de la VM.
- Cambios de núcleo/plugins hechos por TI (ver `DISALLOW_FILE_MODS` en la plantilla de `wp-config`).
- `wp core verify-checksums` tras instalar.

## Vuelta atrás

Restaurar los archivos y la base del paso 0 y comprobar que el sitio anterior responde.

## Seguridad: estado

- Hecho: `wp-config.php` y `uploads/` fuera de git (índice); sin volcados, zips, credenciales ni `.deb` en el repositorio; scripts de desarrollo escuchan en `127.0.0.1` por defecto (`BIND=0.0.0.0` en `.env` para compartir en LAN); clave root distinta de la de `wpuser`; `.htaccess` con HSTS, CSP mínima, Permissions-Policy y bloqueo de archivos sensibles; `uploads/.htaccess` bloquea PHP; paquete sin contraseñas de desarrollo.
- **Pendiente obligatorio:** las claves de desarrollo (BD y admin) y los salts siguen en el historial de git (no se reescribió). **Rotarlas en el entorno de desarrollo.** En producción se usan credenciales nuevas, nunca las actuales.
- Opcional: reescribir el historial (`git filter-repo`) solo tras rotar y con autorización explícita; invalida los clones.
