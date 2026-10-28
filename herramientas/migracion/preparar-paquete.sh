#!/usr/bin/env bash
# Prepara todo lo que hay que subir al servidor de la ULEAM, SIN tocar la base de datos local.
#
#   ./herramientas/migracion/preparar-paquete.sh
#
# Deja en ./paquete-migracion-AAAA-MM-DD/:
#   base-de-datos.sql.gz   la BD con las direcciones ya cambiadas a la URL definitiva
#   codigo.tar.gz          WordPress + tema + plugins (sin wp-config.php, sin uploads, sin .deb)
#   uploads.tar            los archivos subidos (PDF, Word, imágenes…; ~4 GB)
#   wp-config.php          plantilla de producción (completar con los datos que dé TI)
#   .htaccess              el de la subcarpeta
#   SHA256SUMS             para comprobar que nada se corrompió al subirlo
set -euo pipefail

DESTINO='https://departamentos.uleam.edu.ec/gestion-aseguramiento-calidad'
ORIGEN='http://localhost:8000'

RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
AQUI="$RAIZ/herramientas/migracion"
WPDIR="$RAIZ/wordpress"
WP="${WP_CLI:-$HOME/.local/bin/wp}"
SALIDA="$RAIZ/paquete-migracion-$(date +%F)"

if [ -d "$HOME/.local/php-ext/conf.d" ]; then
  export PHP_INI_SCAN_DIR=":$HOME/.local/php-ext/conf.d"
fi

wp() { "$WP" --path="$WPDIR" "$@"; }

echo "▶ Comprobando que la base de datos local responde…"
wp db check --quiet >/dev/null || { echo "ERROR: arranca primero MariaDB (./arrancar.sh)"; exit 1; }

# Direcciones de pruebas (IP de la red, túneles) que no se reemplazarían y quedarían rotas en el servidor.
echo "▶ Buscando direcciones de pruebas que no sean $ORIGEN…"
OTRAS=$(wp db query "SELECT CONCAT(ID,' ',post_type,' ',post_title) FROM wp_posts
  WHERE post_status IN ('publish','draft','private','inherit')
    AND post_content REGEXP 'https?://(10\\\\.[0-9]+\\\\.[0-9]+\\\\.[0-9]+|192\\\\.168\\\\.[0-9.]+|127\\\\.0\\\\.0\\\\.1|[a-z0-9-]+\\\\.trycloudflare\\\\.com|[a-z0-9-]+\\\\.ngrok[a-z.-]*)'
  UNION SELECT CONCAT('meta ',post_id,' ',meta_key) FROM wp_postmeta
  WHERE meta_value REGEXP 'https?://(10\\\\.[0-9]+\\\\.[0-9]+\\\\.[0-9]+|192\\\\.168\\\\.[0-9.]+|127\\\\.0\\\\.0\\\\.1|[a-z0-9-]+\\\\.trycloudflare\\\\.com)'" --skip-column-names)
if [ -n "$OTRAS" ]; then
  echo "ERROR: estos contenidos tienen enlaces a una IP o túnel de pruebas:"
  echo "$OTRAS"
  echo "Corrígelos con:  wp search-replace 'http://IP:8000' '$ORIGEN' wp_posts wp_postmeta --precise"
  exit 1
fi

# guid es VARCHAR(255): con el dominio largo, algunos no caben y la importación se corta a mitad.
# (Sin la función REPLACE(): wp-cli la toma por una escritura y no devuelve el resultado.)
LARGOS=$(wp db query "SELECT COUNT(*) FROM wp_posts WHERE guid LIKE '$ORIGEN%'
  AND CHAR_LENGTH(guid) - CHAR_LENGTH('$ORIGEN') + CHAR_LENGTH('$DESTINO') > 255" --skip-column-names)
if [ "$LARGOS" != "0" ]; then
  echo "ERROR: $LARGOS entradas tendrían un guid de más de 255 caracteres. Acórtalos con:"
  echo "  wp db query \"UPDATE wp_posts SET guid=CONCAT('$ORIGEN/?p=',ID) WHERE CHAR_LENGTH(REPLACE(guid,'$ORIGEN','$DESTINO'))>255\""
  exit 1
fi

mkdir -p "$SALIDA"

echo "▶ Exportando la BD con $ORIGEN → $DESTINO (la BD local no cambia)…"
wp search-replace "$ORIGEN" "$DESTINO" --all-tables --precise --report-changed-only \
  --export="$SALIDA/base-de-datos.sql"

# Sólo puede quedar la cabecera del volcado ("-- Host: localhost"), nunca una dirección.
if grep -q "$ORIGEN" "$SALIDA/base-de-datos.sql"; then
  echo "ERROR: el volcado todavía contiene $ORIGEN:"
  grep -o ".\{60\}$ORIGEN.\{60\}" "$SALIDA/base-de-datos.sql" | head
  exit 1
fi
if ! grep -q "'home', '$DESTINO'" "$SALIDA/base-de-datos.sql"; then
  echo "ERROR: la opción 'home' no quedó con $DESTINO"
  exit 1
fi
# wp-cli no pone la cabecera de mysqldump: sin ella, un MySQL 8 con NO_ZERO_DATE rechaza las
# fechas '0000-00-00 00:00:00' por defecto de WordPress y la importación falla en la 1.ª tabla.
{
  echo "SET NAMES utf8mb4;"
  echo "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';"
  echo "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;"
  cat "$SALIDA/base-de-datos.sql"
  echo "SET SQL_MODE=@OLD_SQL_MODE;"
  echo "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;"
} > "$SALIDA/base-de-datos.sql.tmp"
mv "$SALIDA/base-de-datos.sql.tmp" "$SALIDA/base-de-datos.sql"
gzip -f "$SALIDA/base-de-datos.sql"

echo "▶ Empaquetando el código…"
tar -C "$WPDIR" -czf "$SALIDA/codigo.tar.gz" \
  --exclude='./wp-config.php' \
  --exclude='./wp-content/uploads' \
  --exclude='./wp-content/upgrade' \
  --exclude='./wp-content/plugins/wordpress-importer' \
  --exclude='./wp-content/languages/plugins/wordpress-importer-*' \
  --exclude='./*.deb' \
  --exclude='*.log' \
  .

echo "▶ Empaquetando los archivos subidos (tarda unos minutos)…"
tar -C "$WPDIR/wp-content" -cf "$SALIDA/uploads.tar" uploads

cp "$AQUI/wp-config-produccion.php" "$SALIDA/wp-config.php"
cp "$AQUI/htaccess-produccion" "$SALIDA/.htaccess"
cp "$AQUI/LEEME.md" "$SALIDA/LEEME.md"

( cd "$SALIDA" && sha256sum base-de-datos.sql.gz codigo.tar.gz uploads.tar > SHA256SUMS )

echo
echo "✔ Paquete listo en $SALIDA"
ls -lh "$SALIDA"
echo
echo "Siguiente paso: seguir $SALIDA/LEEME.md (sección «En el servidor»)."
