# Guía de despliegue en la máquina virtual — https://dgacweb.uleam.edu.ec/

Sitio WordPress en la **raíz del dominio** (sin subcarpeta), sobre Apache. Los comandos suponen Ubuntu/Debian; ajusta si TI usa otra distribución. Sigue los pasos en orden.

> Hoy el dominio responde `403` con una barrera de JavaScript (cookie `ir_ok`) delante y sin WordPress. Pregunta a TI qué es (proxy/WAF): puede interferir con `wp-login`, la REST y las subidas, y define si hace falta `ULEAM_PROXY_CONFIABLE` (paso 5).

---

## 0. Qué hay que pedir a TI

- Acceso SSH a la VM con un usuario con `sudo` (no root directo) y la ruta del DocumentRoot.
- Certificado TLS válido para `dgacweb.uleam.edu.ec` y redirección HTTP → HTTPS.
- Firewall: solo 80/443 públicos y SSH restringido; MariaDB solo en `127.0.0.1`.
- Confirmar versiones de PHP (≥ 8.1) y MariaDB (≥ 10.5).

## 1. Generar el paquete (en tu equipo de desarrollo)

```bash
MYSQL_ROOT_PASSWORD=<root-del-contenedor> \
  herramientas/despliegue/preparar-paquete.sh https://dgacweb.uleam.edu.ec wordpress_deploy --nuevo-volcado
```

Genera en `dist/` (fuera de git):

| Archivo | Contenido |
|---|---|
| `paquete-uleam-calidad.tar.gz` | WordPress + tema `uleam-calidad` + plugin `gutena-tabs` + `.htaccess` (raíz y `uploads/`). Sin `wp-config.php`, sin `uploads/`, sin mu-plugin de desarrollo. |
| `wordpress_deploy-AAAAMMDD.sql.gz` | Base con URLs `https://dgacweb.uleam.edu.ec`, contraseñas de usuario **bloqueadas**, sin sesiones. |
| `volcado-origen.sql` | Respaldo local con hashes. **No subir.** |

El script falla si queda alguna URL de desarrollo (localhost, IPs privadas, túneles) en el paquete.

## 2. Preparar la VM

```bash
sudo apt update && sudo apt install -y apache2 mariadb-server php php-mysql php-gd php-mbstring \
  php-intl php-xml php-zip php-curl php-imagick php-soap libapache2-mod-php unzip rsync
sudo a2enmod rewrite headers ssl expires && sudo systemctl restart apache2
sudo mysql_secure_installation          # y confirma bind-address = 127.0.0.1 en /etc/mysql/mariadb.conf.d/
```

VirtualHost (`/etc/apache2/sites-available/dgacweb.conf`; el certificado lo provee TI):

```apache
<VirtualHost *:80>
    ServerName dgacweb.uleam.edu.ec
    Redirect permanent / https://dgacweb.uleam.edu.ec/
</VirtualHost>
<VirtualHost *:443>
    ServerName dgacweb.uleam.edu.ec
    DocumentRoot /var/www/dgacweb
    SSLEngine on
    SSLCertificateFile    /ruta/al/certificado.crt
    SSLCertificateKeyFile /ruta/a/la/clave.key
    <Directory /var/www/dgacweb>
        AllowOverride All
        Require all granted
    </Directory>
    ServerSignature Off
</VirtualHost>
```

```bash
sudo a2ensite dgacweb && sudo systemctl reload apache2
sudo sed -i 's/^expose_php.*/expose_php = Off/' /etc/php/*/apache2/php.ini   # no anunciar la versión de PHP
sudo sed -i 's/^ServerTokens.*/ServerTokens Prod/' /etc/apache2/conf-available/security.conf
sudo mkdir -p /var/www/dgacweb && sudo chown $USER:$USER /var/www/dgacweb
```

## 3. Base de datos

```bash
sudo mysql <<'SQL'
CREATE DATABASE dgac_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dgac_web'@'localhost' IDENTIFIED BY '<CLAVE-LARGA-UNICA>';
GRANT ALL PRIVILEGES ON dgac_web.* TO 'dgac_web'@'localhost';
FLUSH PRIVILEGES;
SQL
```

Sube el `.sql.gz` e impórtalo (clave nueva; **no** reutilices ninguna de desarrollo):

```bash
scp dist/wordpress_deploy-*.sql.gz USUARIO@VM:/tmp/
gunzip -c /tmp/wordpress_deploy-*.sql.gz | mysql -u dgac_web -p dgac_web && shred -u /tmp/wordpress_deploy-*.sql.gz
```

## 4. Código y archivos subidos

```bash
scp dist/paquete-uleam-calidad.tar.gz USUARIO@VM:/tmp/
# en la VM:
tar -xzf /tmp/paquete-uleam-calidad.tar.gz -C /var/www/dgacweb --strip-components=1
# desde tu equipo: los uploads (3,2 GB) van aparte
rsync -av --progress wordpress/wp-content/uploads/ USUARIO@VM:/var/www/dgacweb/wp-content/uploads/
```

Comprueba que existen `/var/www/dgacweb/.htaccess` y `/var/www/dgacweb/wp-content/uploads/.htaccess` (bloquea PHP en `uploads/`).

## 5. `wp-config.php`

```bash
cd /var/www/dgacweb
cp /ruta/repo/herramientas/despliegue/wp-config.produccion.php.example wp-config.php   # o cópialo con scp
nano wp-config.php
```

Completa: base/usuario/clave del paso 3, **salts nuevos** (<https://api.wordpress.org/secret-key/1.1/salt/>) y, si usas el asistente con IA, `ULEAM_GROQ_API_KEY` o `ULEAM_CLAUDE_API_KEY`. Activa `ULEAM_PROXY_CONFIABLE` solo si TI confirma un proxy propio delante.

## 6. Permisos

```bash
cd /var/www/dgacweb
sudo chown -R $USER:www-data .
sudo find . -type d -exec chmod 755 {} \; && sudo find . -type f -exec chmod 644 {} \;
sudo chmod 640 wp-config.php
sudo chown -R www-data:www-data wp-content/uploads     # único lugar donde Apache escribe
```

## 7. Administrador

Todas las contraseñas importadas están bloqueadas. Fija la del administrador:

```bash
# con WP-CLI (recomendado)
wp user list --path=/var/www/dgacweb
wp user update <ID> --user_pass='<CLAVE-LARGA-UNICA>' --path=/var/www/dgacweb
# sin WP-CLI
mysql -u dgac_web -p dgac_web -e "UPDATE wp_users SET user_pass = MD5('<CLAVE-TEMPORAL-LARGA>') WHERE ID = <ID>;"
```

Entra a `https://dgacweb.uleam.edu.ec/wp-admin/`, cambia la clave, y en **Ajustes → Generales** cambia el correo del administrador (viene `admin@example.com`). Elimina o desactiva cuentas que no correspondan y usa 2FA para administradores. Luego: **Ajustes → Enlaces permanentes → Guardar**.

## 8. Verificación

```bash
herramientas/despliegue/verificar-sitio.sh https://dgacweb.uleam.edu.ec
```

Comprueba archivos sensibles (403/404), HTTPS, cabeceras de seguridad, ausencia de referencias a localhost/IPs privadas/túneles y que no se listen usuarios. Revisión manual:

- [ ] Portada, menú, Gestión de Procesos, un PDF y un DOCX descargan; el carrusel y las imágenes cargan.
- [ ] `/wp-admin/` pide login y **no redirige a localhost**; los enlaces del escritorio usan `https://dgacweb.uleam.edu.ec`.
- [ ] Un archivo inexistente en `uploads/` da **404** (no redirige a la portada).
- [ ] Un `.php` de prueba en `uploads/` no se ejecuta (bórralo después).
- [ ] El asistente responde.
- [ ] `wp core verify-checksums --path=/var/www/dgacweb` sin errores.

## 9. Operación

- Respaldos diarios de base y `uploads/` **fuera** de la VM.
- Fail2ban para SSH y `wp-login.php`; actualizaciones de seguridad del SO.
- Actualizar WordPress y plugins de forma controlada (`DISALLOW_FILE_MODS` en la plantilla si TI lo hace por línea de comandos).

## Vuelta atrás

Antes de empezar guarda copia de archivos y base de lo que haya en el servidor. Si algo falla: restaurar ambos y comprobar que el sitio anterior responde.

## Notas

- Los archivos referenciados por la base que no estén en `uploads/` (8 de los documentos de procesos ya fallaban en el sitio oficial) seguirán rotos.
- Las credenciales de desarrollo siguen en el historial de git: rótalas y **no** las uses en producción. Reescribir el historial (`git filter-repo`) solo tras rotar y con autorización explícita.
