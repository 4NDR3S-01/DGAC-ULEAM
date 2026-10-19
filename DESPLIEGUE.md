# Despliegue del sitio en departamentos.uleam.edu.ec/gestion-aseguramiento-calidad/

Guía para reemplazar el WordPress actual de la Dirección de Gestión y Aseguramiento de la Calidad por esta versión.
Sigue los pasos en orden. **No borres nada del sitio actual hasta completar el paso 0.**

---

## Qué contiene `dist/` (generado, no va a git)

| Archivo | Para qué sirve |
|---|---|
| `paquete-uleam-calidad.tar.gz` | Archivos de WordPress + tema `uleam-calidad` + plugin `gutena-tabs` + `.htaccess`. **No** incluye `wp-config.php` ni `uploads/`. |
| `wordpress_deploy-AAAAMMDD.sql.gz` | Base de datos lista: URLs finales, enlaces de subcarpeta corregidos. |
| `uploads-faltantes.txt` | Archivos que la base referencia y que no están en la carpeta local `uploads/` (656 de 2721). |
| `volcado-origen.sql` | Copia de la base de origen tomada en solo lectura. Solo respaldo interno. |

Para regenerarlos (en el equipo de desarrollo):

```bash
herramientas/despliegue/preparar-paquete.sh https://departamentos.uleam.edu.ec/gestion-aseguramiento-calidad wordpress_deploy
```

---

## Requisitos del hosting

- PHP **8.0 o superior** con extensiones `mysqli`, `mbstring`, `gd`, `intl`, `xml`, `zip`.
- MySQL / MariaDB con acceso para crear una base y un usuario nuevos.
- Apache con `mod_rewrite` y `AllowOverride All` (o `FileInfo`) en la carpeta del sitio.
- Espacio para ~4 GB (`uploads/` pesa 3,2 GB).
- Confirmar con el área de TI de la ULEAM la versión de PHP y de MySQL/MariaDB antes de empezar.

---

## Paso 0 — Respaldo del sitio actual (obligatorio)

1. **Archivos**: descargar la carpeta completa de `gestion-aseguramiento-calidad/` (o pedir copia completa a TI).
2. **Base de datos**: exportar la base actual con `mysqldump` o phpMyAdmin.
3. Guardar ambos respaldos fuera del servidor y comprobar que se abren.

Sin estos respaldos no se debe continuar: son la vuelta atrás.

---

## Paso 1 — Subir el código

1. Subir `paquete-uleam-calidad.tar.gz` a la carpeta del sitio y extraerlo ahí.
   Debe quedar `index.php`, `wp-admin/`, `wp-includes/`, `wp-content/themes/uleam-calidad/` en esa carpeta.
2. **No** borrar `wp-content/uploads/` del sitio actual: se fusiona en el paso 2.

---

## Paso 2 — Copiar los archivos subidos (`uploads/`)

La base de datos apunta a 2721 archivos en `wp-content/uploads/`. Sin ellos, las imágenes y documentos aparecen rotos.

- Opción preferida: copiar `uploads/` desde el servidor actual (sin borrar lo que exista):

  ```bash
  rsync -av --ignore-existing USUARIO@SERVIDOR:/ruta/actual/wp-content/uploads/ /ruta/nueva/wp-content/uploads/
  ```

- Verificar qué falta: `dist/uploads-faltantes.txt` lista 656 rutas que no están en el equipo de desarrollo. Deben existir en el servidor actual; si alguna no aparece en ningún lado, el enlace ya estaba roto en el sitio anterior.

---

## Paso 3 — Importar la base de datos

En el panel del hosting:

1. Crear una base nueva (p. ej. `uleam_calidad`), con `utf8mb4` / `utf8mb4_unicode_ci`.
2. Crear un usuario con contraseña **nueva** y darle todos los permisos sobre esa base.
3. Importar el archivo:

   ```bash
   gunzip -c wordpress_deploy-AAAAMMDD.sql.gz | mysql -h HOST -u USUARIO -p NOMBRE_BD
   ```

   O subir el `.sql.gz` descomprimido en phpMyAdmin.

No importar sobre la base actual: usar una base nueva y cambiar la configuración en el paso 4.

---

## Paso 4 — Configuración: `wp-config.php` y `.htaccess`

1. Copiar `herramientas/despliegue/wp-config.produccion.php.example` a `wp-config.php` en la carpeta del sitio.
   Completar los valores `<...>`:
   - Datos de la base del paso 3.
   - **Claves y salts nuevos**: generarlos en <https://api.wordpress.org/secret-key/1.1/salt/>.
   - `WP_HOME` y `WP_SITEURL` ya están fijadas a `https://departamentos.uleam.edu.ec/gestion-aseguramiento-calidad`. Si el servidor usa otra dirección, cambiarla ahí.
2. Confirmar que `.htaccess` (copiado del paquete) está en la carpeta del sitio. Si el hosting ya tenía reglas propias de WordPress, conservarlas y añadir solo lo necesario.
3. **Verificar** que `wp-config.php` no es accesible desde el navegador (debe dar 403 o 404).

---

## Paso 5 — Regenerar los enlaces permanentes

En el panel de WordPress: **Ajustes → Enlaces permanentes → Guardar cambios** (sin tocar nada).
Esto regenera las reglas de `.htaccess` con la estructura `/%postname%/`.

---

## Paso 6 — Cuentas y contraseñas

La base incluye 8 cuentas de usuario de la versión de desarrollo. **Antes de publicar:**

- Cambiar la contraseña de **todas** las cuentas. En especial `admin`, cuya contraseña de desarrollo está documentada en `arrancar.sh`: no debe usarse en producción.
- Desactivar o eliminar las cuentas que ya no correspondan.

---

## Paso 7 — Verificación antes de anunciar el cambio

Probar en un navegador (en una ventana privada):

- [ ] `https://departamentos.uleam.edu.ec/gestion-aseguramiento-calidad/` carga la portada con imágenes.
- [ ] Menú: Inicio, Gestión de Procesos, Aseguramiento de la Calidad, Auditoría de la Calidad, POA, Horario de atención.
- [ ] Un documento PDF y uno DOCX descargan bien.
- [ ] Redirecciones antiguas (deben dar 301 a la página nueva):
  - `/2020/07/06/control-a-seguimientos/` → Seguimiento y control a planes de mejora
  - `/autoevaluacion-2018/` → Aseguramiento de la Calidad, evaluación institucional
  - `/recurso-humano/` → Recursos humanos
  - `/admision/` → Gestión de procesos
- [ ] `/wp-admin/` pide inicio de sesión y entra con una cuenta nueva.
- [ ] `/feed/` responde 200.
- [ ] Un archivo que no existe (p. ej. `/gestion-aseguramiento-calidad/wp-content/uploads/zzz.png`) responde **404**, no redirige a la portada.

---

## Vuelta atrás

Si algo falla y hay que volver al sitio anterior:

1. Restaurar la carpeta de archivos del paso 0.
2. Restaurar la base de datos del paso 0 en la base original.
3. Revisar que el sitio anterior responde antes de cerrar la ventana de trabajo.

---

## Limitaciones conocidas

- La prueba de funcionamiento se hizo con un servidor PHP local que simula la subcarpeta. **No se probó con Apache real**: las reglas de `.htaccess` (en particular la de `wp-content/uploads/`) deben verificarse en el hosting con el paso 7.
- `uploads/` no se incluye en el paquete (3,2 GB). Sin el paso 2, las imágenes no aparecen.
- Los enlaces a otras secciones de `departamentos.uleam.edu.ec` (unos 169 contenidos) se mantienen tal cual; algunos usan `http://` y redirigen a `https://` en el servidor.
- El plugin `wordpress-importer` se usó solo para migrar y no va en el paquete. Si aparece en la lista de plugins activos, desactivarlo.

---

## Seguridad

Hallazgos de la auditoría y su estado:

- **Contraseña de la base de desarrollo** en `setup-db.sql`, `arrancar.sh.example`, y en el historial de git (`wp-config.php` estuvo versionado). Hay que **rotarla** en la base de desarrollo y reemplazarla por un placeholder en esos dos archivos. Pendiente de decisión.
- **Contraseña de admin de WordPress** en `arrancar.sh` (versionado). Hay que **quitarla y rotarla**; esa cuenta existe también en la base que se despliega, así que el paso 6 es obligatorio. Pendiente de decisión.
- **`wordpress-2026-09-23.sql`** está versionado y contiene `wp_users` (hashes, correos). Decidir si sacarlo del repo; reescribir el historial solo con autorización explícita.
- Los scripts de despliegue ya no pasan contraseñas por la línea de comandos (usan `MYSQL_PWD`), validan el nombre de la base y rechazan escribir en un host que no sea local.
- `wp-config.php` deja de estar en git: el cambio de índice está preparado pero **no confirmado**.
- `.htaccess` bloquea `wp-config.php`, `xmlrpc.php`, volcados, logs y `readme.html`, y añade cabeceras de seguridad. Si el hosting no tiene `mod_headers`, las cabeceras se omiten sin error.
- Opcional: en `wp-content/uploads/` añadir un `.htaccess` con `RemoveHandler .php .phtml` para que no se ejecute PHP subido por error.
