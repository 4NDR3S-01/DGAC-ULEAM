#!/usr/bin/env bash
# Arranca solo el servidor PHP contra TU base de datos (la de wp-config.php).
# No crea ni toca ninguna base. Uso: ./arrancar.php.sh
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP="$SCRIPT_DIR/wordpress"
BIND="${BIND:-127.0.0.1}"

if [ ! -f "$WP/wp-config.php" ]; then
  echo "ERROR: falta wordpress/wp-config.php"
  echo "  cp herramientas/despliegue/wp-config.local.php.example wordpress/wp-config.php"
  echo "  y rellena los datos de tu base."
  exit 1
fi

for ext in mysqli gd mbstring; do
  php -m | grep -qix "$ext" || { echo "ERROR: a PHP le falta la extensión $ext"; exit 1; }
done

echo "Sitio: http://localhost:8000   (Ctrl+C para parar)"
exec php -S "$BIND:8000" -t "$WP"
