#!/usr/bin/env bash
# ============================================================
#  Prepara el paquete de despliegue de Gestión y Aseguramiento de la Calidad.
#  1. Volcado de solo lectura de la base de origen (192.168.88.56:3307).
#  2. Importación en una base de trabajo del contenedor local (uleam-mariadb).
#  3. Reemplazo de URLs de desarrollo por la URL final (respeta datos serializados).
#  4. Ajustes para subcarpeta (solo si la URL final tiene ruta; para el dominio raíz se omite).
#  4b. Bloqueo de contraseñas de desarrollo (sanear-para-produccion.sql).
#  5. Exportación de la base a dist/ y árbol de WordPress en dist/paquete/.
#
#  Uso: herramientas/despliegue/preparar-paquete.sh <URL_FINAL> <BASE_TRABAJO> [--nuevo-volcado]
#  Ejemplo: herramientas/despliegue/preparar-paquete.sh https://dgacweb.uleam.edu.ec wordpress_deploy
#
#  Requiere: docker con el contenedor uleam-mariadb, php, rsync, tar y la variable MYSQL_ROOT_PASSWORD (clave root del contenedor).
#  El origen se puede cambiar con ORIGEN_HOST / ORIGEN_PUERTO.
#  No escribe en la base de origen ni en la de desarrollo.
# ============================================================
set -euo pipefail

RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DEP="$RAIZ/herramientas/despliegue"
DIST="$RAIZ/dist"
WP="$RAIZ/wordpress"
ORIGEN_HOST="${ORIGEN_HOST:-192.168.88.56}"
ORIGEN_PUERTO="${ORIGEN_PUERTO:-3307}"

URL="${1:?Falta la URL final, p. ej. https://dgacweb.uleam.edu.ec}"
BD="${2:?Falta el nombre de la base de trabajo, p. ej. wordpress_deploy}"
NUEVO="${3:-}"
URL="${URL%/}"

# Nombre de base: solo letras, números y guion bajo; nunca las bases de desarrollo.
[[ "$BD" =~ ^[A-Za-z0-9_]+$ ]] || { echo "✘ Nombre de base no válido: $BD" >&2; exit 1; }
case "$BD" in wordpress|wordpress_test) echo "✘ '$BD' es una base de desarrollo; usa otro nombre" >&2; exit 1 ;; esac

# Credenciales de origen: las de tu wp-config.php local. Se pasan por variable de entorno, no por argumentos.
ORIGEN_USER="$(sed -n "s/.*define( 'DB_USER', '\([^']*\)'.*/\1/p" "$WP/wp-config.php")"
ORIGEN_PASS="$(sed -n "s/.*define( 'DB_PASSWORD', '\([^']*\)'.*/\1/p" "$WP/wp-config.php")"
ORIGEN_BD="$(sed -n "s/.*define( 'DB_NAME', '\([^']*\)'.*/\1/p" "$WP/wp-config.php")"
if [ -z "$ORIGEN_USER" ] || [ -z "$ORIGEN_PASS" ] || [ -z "$ORIGEN_BD" ]; then
  echo "✘ No pude leer usuario, clave o base de origen en $WP/wp-config.php" >&2
  exit 1
fi

# Clave de la base local (contenedor uleam-mariadb); en variable de entorno para no mostrarla en ps.
LOCAL_PASS="${MYSQL_ROOT_PASSWORD:?Define MYSQL_ROOT_PASSWORD (clave root del contenedor uleam-mariadb)}"

mkdir -p "$DIST"

# ---- 1. Volcado de origen (solo lectura) ----
VOLCADO="$DIST/volcado-origen.sql"
if [ ! -s "$VOLCADO" ] || [ "$NUEVO" = "--nuevo-volcado" ]; then
  echo "▶ Volcado de solo lectura de $ORIGEN_HOST:$ORIGEN_PUERTO"
  docker run --rm --network host -e MYSQL_PWD="$ORIGEN_PASS" mariadb:11 mariadb-dump \
    -h "$ORIGEN_HOST" -P "$ORIGEN_PUERTO" -u "$ORIGEN_USER" \
    --single-transaction --routines --triggers --skip-lock-tables "$ORIGEN_BD" > "$VOLCADO.tmp"
  mv "$VOLCADO.tmp" "$VOLCADO"
else
  echo "✔ Usando volcado existente: $VOLCADO (pasa --nuevo-volcado para repetirlo)"
fi

# ---- 2. Base de trabajo ----
echo "▶ Importando en la base de trabajo $BD"
docker exec -e MYSQL_PWD="$LOCAL_PASS" uleam-mariadb mariadb -uroot -e \
  "DROP DATABASE IF EXISTS \`$BD\`; CREATE DATABASE \`$BD\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
docker exec -i -e MYSQL_PWD="$LOCAL_PASS" uleam-mariadb mariadb -uroot "$BD" < "$VOLCADO"

# ---- 3. URLs ----
echo "▶ Reemplazando URLs de desarrollo por $URL"
# Túneles de desarrollo (trycloudflare) presentes en el volcado: se reemplazan sin importar su subdominio.
URLS_ORIGEN_EXTRA="$(grep -oE 'https?://[A-Za-z0-9-]+\.trycloudflare\.com' "$VOLCADO" | sort -u | paste -sd, - || true)"
export URLS_ORIGEN_EXTRA
MYSQL_PWD="$LOCAL_PASS" php "$DEP/reemplazar-urls.php" 127.0.0.1 3307 root "$BD" "$URL" --aplicar

# ---- 4. Subcarpeta ----
if [[ "$URL" =~ ^https?://[^/]+/.+ ]]; then
  echo "▶ Ajustes para la subcarpeta"
  docker exec -i -e MYSQL_PWD="$LOCAL_PASS" uleam-mariadb mariadb -uroot "$BD" < "$DEP/ajustes-subdirectorio.sql"
else
  echo "✔ Dominio raíz: sin ajustes de subcarpeta"
fi

# ---- 4b. Sin credenciales de desarrollo ----
echo "▶ Bloqueando contraseñas de desarrollo y limpiando sesiones/transitorios"
docker exec -i -e MYSQL_PWD="$LOCAL_PASS" uleam-mariadb mariadb -uroot "$BD" < "$DEP/sanear-para-produccion.sql"

# ---- 5. Exportación ----
SALIDA="$DIST/$BD-$(date +%Y%m%d).sql.gz"
echo "▶ Exportando base a $SALIDA"
docker exec -e MYSQL_PWD="$LOCAL_PASS" uleam-mariadb mariadb-dump -uroot --single-transaction --routines --triggers "$BD" | gzip > "$SALIDA"

# ---- 6. Árbol de WordPress (sin datos locales ni secretos) ----
PAQUETE="$DIST/paquete"
echo "▶ Armando el árbol de WordPress en $PAQUETE"
rm -rf "$PAQUETE"
mkdir -p "$PAQUETE/wp-content/themes" "$PAQUETE/wp-content/plugins"
rsync -a \
  --exclude 'wp-config.php' \
  --exclude 'readme.html' --exclude 'license.txt' --exclude '*.deb' \
  --exclude 'wp-content/uploads/' \
  --exclude 'wp-content/mu-plugins/' \
  --exclude 'wp-content/themes/twenty*' \
  --exclude 'wp-content/plugins/' \
  --exclude 'wp-content/themes/' \
  --exclude 'wp-content/languages/plugins/wordpress-importer-*' \
  --exclude '.git' --exclude '.env*' --exclude '*.sql' --exclude '*.sql.gz' --exclude '*.log' \
  --exclude 'debug.log' --exclude 'wp-content/upgrade/' --exclude 'wp-content/cache/' \
  --exclude 'wp-content/backup*' --exclude '*.bak' --exclude '*~' \
  "$WP/" "$PAQUETE/"
rsync -a "$WP/wp-content/themes/uleam-calidad" "$PAQUETE/wp-content/themes/"
rsync -a "$WP/wp-content/themes/index.php" "$PAQUETE/wp-content/themes/"
rsync -a "$WP/wp-content/plugins/gutena-tabs" "$WP/wp-content/plugins/index.php" "$PAQUETE/wp-content/plugins/"
cp "$DEP/htaccess.example" "$PAQUETE/.htaccess"
mkdir -p "$PAQUETE/wp-content/uploads"
cp "$DEP/htaccess-uploads.example" "$PAQUETE/wp-content/uploads/.htaccess"

# Comprobación: ningún rastro de desarrollo en lo que se empaqueta.
PATRON_DEV='localhost:8000\|10\.7\.25\.51\|trycloudflare\.com\|192\.168\.88\.56'
if grep -rIl "$PATRON_DEV" "$PAQUETE" >/dev/null 2>&1; then
  echo "✘ Quedan referencias de desarrollo o de la base de origen en el paquete:" >&2
  grep -rIl "$PATRON_DEV" "$PAQUETE" >&2
  exit 5
fi

tar -C "$DIST" -czf "$DIST/paquete-uleam-calidad.tar.gz" paquete
echo "✔ Listo: $SALIDA"
echo "✔ Listo: $DIST/paquete-uleam-calidad.tar.gz"
echo "  Uploads: NO incluidos (3,2 GB). Ver README-DESPLIEGUE.md para copiarlos."
