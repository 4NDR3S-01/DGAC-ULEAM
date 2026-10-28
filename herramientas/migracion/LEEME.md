# Migración a departamentos.uleam.edu.ec/gestion-aseguramiento-calidad/

El sitio nuevo **reemplaza** al anterior en la misma dirección. Todas las direcciones viejas
(339 páginas/entradas y 2.824 archivos `/files/…`) siguen funcionando: llevan a su equivalente nuevo.

## ⚠️ Tres reglas que evitan romper algo

1. **Base de datos NUEVA y VACÍA.** El sitio anterior es un subsitio de la red WordPress de
   `departamentos.uleam.edu.ec`. El archivo `base-de-datos.sql.gz` trae `DROP TABLE wp_options`,
   `wp_posts`, `wp_users`… Si se importa en la base de datos de la red, **borra todos los sitios
   de departamentos**. Pedir a TI una base de datos aparte, exclusiva para este sitio.
2. **No copiar nada del sitio anterior** (ni archivos, ni plugins, ni base de datos): estaba hackeado
   (~160.000 entradas de spam y enlaces ocultos a casinos). Todo lo útil ya está en el paquete, limpio.
3. **Archivar, no eliminar**, el subsitio anterior hasta comprobar que el nuevo funciona (vuelta atrás).

## 1. En la computadora local (antes de generar el paquete)

- [ ] Ajustes → Generales: **Correo de administración** = `dgac@uleam.edu.ec` (hoy es `admin@example.com`:
      si se olvida una contraseña, el correo de recuperación no le llegaría a nadie). Igual en Usuarios → admin.
- [ ] Ajustes → Generales: **Zona horaria** = Guayaquil (hoy está en UTC+0).
- [ ] Usuarios: crear una cuenta con rol **Editor** para cada persona que mantendrá el sitio (con su
      correo institucional). Las cuentas actuales (autores de noticias) son «Suscriptor» y no pueden editar.
- [ ] Apariencia → Personalizar → ULEAM - Contenido del sitio: ID de Google Analytics si se quiere seguir midiendo
      (el sitio anterior usaba `G-G31S309ZPB`).
- [ ] Ejecutar:

  ```bash
  ./arrancar.sh
  ./herramientas/migracion/preparar-paquete.sh
  ```

  Crea `paquete-migracion-AAAA-MM-DD/` con la BD **ya convertida** a la dirección definitiva (la BD
  local no cambia) y se detiene con un error si encuentra enlaces a `localhost`, a una IP de la red
  local o a un túnel de pruebas.

## 2. En el servidor (TI)

Requisitos: Apache con `mod_rewrite` y `AllowOverride All` en la carpeta; PHP **8.0 o superior**
(probado con 8.4) con `mysqli`, `mbstring`, `gd` (fotos del carrusel), `intl` (recomendado), `curl`
y `zip`; MySQL 5.7+ / MariaDB 10.4+; HTTPS. El asistente virtual necesita salida HTTPS hacia
`api.groq.com`. Subir el límite de archivos a 64 MB (`upload_max_filesize` y `post_max_size`).

1. **Respaldar y archivar** el subsitio anterior: Administrar la red → Sitios →
   `gestion-aseguramiento-calidad` → *Archivar*.
2. Crear la carpeta física `gestion-aseguramiento-calidad/` en la raíz web de `departamentos.uleam.edu.ec`
   (junto al `wp-config.php` de la red). Una carpeta real tiene prioridad sobre las reglas de la red.
3. Descomprimir dentro: `tar -xzf codigo.tar.gz` y `tar -xf uploads.tar -C wp-content/`.
   Comprobar antes: `sha256sum -c SHA256SUMS`.
4. Copiar `.htaccess` y `wp-config.php` del paquete a esa carpeta. En `wp-config.php` completar los
   datos de la base de datos nueva y pegar claves secretas nuevas
   (https://api.wordpress.org/secret-key/1.1/salt/).
5. Importar la BD en la base **nueva**:
   `gunzip -c base-de-datos.sql.gz | mysql -u USUARIO -p BASE_NUEVA`
   (o phpMyAdmin → la base nueva → Importar). **No hace falta** `search-replace`: ya viene hecho.
6. Permisos: carpetas 755, archivos 644, `wp-content/uploads` escribible por el usuario de Apache,
   `wp-config.php` 640.

## 3. Comprobación final (10 minutos)

Abrir en una ventana privada:

- [ ] `…/gestion-aseguramiento-calidad/` — portada con carrusel, menú y pie.
- [ ] `…/gestion-aseguramiento-calidad/wp-admin/` — entrar con `admin`.
      Luego **Ajustes → Enlaces permanentes → Guardar** (sin cambiar nada).
- [ ] Una dirección vieja: `…/gestion-aseguramiento-calidad/files/2019/05/CAMBIO-DE-DEDICACION.pdf`
      debe abrir el PDF. Si da error 404 de la red, TI tiene reglas de la red en la configuración de
      Apache (no en `.htaccess`) que capturan `/files/`: hay que excluir esta carpeta.
- [ ] Gestión de Procesos, Aseguramiento de la Calidad, POA: abrir un documento de cada una.
- [ ] Asistente virtual: preguntar «horario de atención» y seguir el enlace.
- [ ] Editar una página (p. ej. Horario de Atención), cambiar una palabra, Actualizar y ver el cambio.
- [ ] Subir una imagen a Medios.
- [ ] Ver el código fuente de la portada (Ctrl+U) y buscar `localhost`: no debe aparecer.

Si algo falla y no se resuelve: renombrar la carpeta `gestion-aseguramiento-calidad/` y *desarchivar*
el subsitio anterior. Todo vuelve a como estaba.

## 4. Después

- Cambiar la contraseña de `admin` y la clave de Groq (Asistente virtual → Configuración): ambas estuvieron
  en archivos del repositorio git.
- Cuando el sitio lleve unas semanas estable, eliminar el subsitio anterior de la red.
- Google Search Console: enviar `…/gestion-aseguramiento-calidad/wp-sitemap.xml`.

## Quién edita qué (sin tocar código)

| Qué | Dónde |
|---|---|
| Textos de las 12 páginas | Páginas → (página) → Editar |
| Documentos del repositorio (PDF, POA, informes) | Documentos → Añadir nuevo (elegir Sección, Año, Tipo) |
| Procesos y procedimientos | Gestión de Procesos |
| Noticias | Noticias |
| Carrusel de portada | Carrusel de portada |
| Logo, contacto, redes, pie | Apariencia → Personalizar → ULEAM - Contenido del sitio |
| Menú superior y del pie | Apariencia → Menús |
| Preguntas del asistente | Asistente virtual |
| Direcciones viejas → nuevas | Herramientas → Redirecciones |
