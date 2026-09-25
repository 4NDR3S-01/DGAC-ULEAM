#!/usr/bin/env bash
# ============================================================
#  Arranca WordPress local (ULEAM Calidad)
#  MariaDB (puerto 3307) + servidor PHP (http://localhost:8000)
#  Uso:  ./arrancar.sh
# ============================================================

# Extensiones PHP opcionales (si existen en esta máquina)
if [ -d "$HOME/.local/php-ext/conf.d" ]; then
  export PHP_INI_SCAN_DIR=":$HOME/.local/php-ext/conf.d"
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP="$SCRIPT_DIR/wordpress"
DB_PASS='0043fc294eed0bc74d2baae3506bf76d'

# ---- 1. Base de datos MariaDB (Docker, puerto 3307) ----
if fuser 3307/tcp >/dev/null 2>&1; then
  echo "✔ MariaDB ya está corriendo (puerto 3307)."
else
  echo "▶ Arrancando MariaDB (Docker)..."
  if docker ps -a --format '{{.Names}}' 2>/dev/null | grep -qx 'uleam-mariadb'; then
    docker start uleam-mariadb >/dev/null
  else
    docker run -d --name uleam-mariadb \
      -e MYSQL_ROOT_PASSWORD=root \
      -e MYSQL_DATABASE=wordpress \
      -e MYSQL_USER=wpuser \
      -e MYSQL_PASSWORD="$DB_PASS" \
      -p 127.0.0.1:3307:3306 \
      -v uleam-mariadb-data:/var/lib/mysql \
      mariadb:11 >/dev/null
  fi
  # Espera a que el puerto esté listo (máx ~30 s)
  for i in $(seq 1 30); do
    fuser 3307/tcp >/dev/null 2>&1 && break
    sleep 1
  done
  echo "✔ MariaDB lista."
fi

# ---- 2. Servidor PHP ----
if fuser 8000/tcp >/dev/null 2>&1; then
  echo "✔ El servidor ya está corriendo (puerto 8000)."
else
  echo "▶ Arrancando servidor PHP..."
  setsid php -S 127.0.0.1:8000 -t "$WP" </dev/null >/tmp/php-server.log 2>&1 &
  disown 2>/dev/null || true
  sleep 2
fi

echo ""
echo "========================================"
echo "  🌐  Sitio:  http://localhost:8000"
echo "  🔐  Admin:  http://localhost:8000/wp-admin/"
echo "      usuario: admin   contraseña: Uleam2026"
echo "========================================"
