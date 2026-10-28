#!/usr/bin/env bash
# Arranque local con Docker. La clave de BD viene de .env (ignorado por git).
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP="$SCRIPT_DIR/wordpress"
[ -f "$SCRIPT_DIR/.env" ] && . "$SCRIPT_DIR/.env"   # define DB_PASS (cp .env.example .env)
BIND="${BIND:-127.0.0.1}"   # 0.0.0.0 en .env solo si hay que compartir la BD/el sitio en la LAN
# Clave root distinta de la de wpuser (solo se usa al crear el contenedor).
ROOT_PASS="${ROOT_PASS:-$(head -c16 /dev/urandom | od -An -tx1 | tr -d ' \n')}"
: "${DB_PASS:?Falta DB_PASS: cp .env.example .env y edítalo}"

port_open() {
  timeout 1 bash -c "echo >/dev/tcp/0.0.0.0/$1" 2>/dev/null
}

if [ ! -d "$WP" ]; then
  echo "ERROR: no existe $WP"
  exit 1
fi

if [ -d "$HOME/.local/php-ext/conf.d" ]; then
  export PHP_INI_SCAN_DIR=":$HOME/.local/php-ext/conf.d"
fi

if port_open 3307; then
  echo "✔ MariaDB ya está corriendo (puerto 3307)."
else
  echo "▶ Arrancando MariaDB..."
  if command -v docker >/dev/null 2>&1 && docker info >/dev/null 2>&1; then
    if docker ps -a --format '{{.Names}}' | grep -qx 'uleam-mariadb'; then
      docker start uleam-mariadb >/dev/null
    else
      docker run -d --name uleam-mariadb \
        -e MYSQL_ROOT_PASSWORD="$ROOT_PASS" \
        -e MYSQL_DATABASE=wordpress \
        -e MYSQL_USER=wpuser \
        -e MYSQL_PASSWORD="$DB_PASS" \
        -p "$BIND:3307:3306" \
        -v uleam-mariadb-data:/var/lib/mysql \
        mariadb:11 >/dev/null
    fi
  elif command -v mariadbd >/dev/null 2>&1; then
    mkdir -p "$HOME/local-mariadb"
    mariadbd --no-defaults \
      --datadir="$HOME/local-mariadb/data" \
      --socket="$HOME/local-mariadb/mysql.sock" \
      --pid-file="$HOME/local-mariadb/mysql.pid" \
      --port=3307 \
      --bind-address="$BIND" \
      > /tmp/mariadb.log 2>&1 &
  else
    echo "ERROR: instala Docker o MariaDB."
    exit 1
  fi
  for i in $(seq 1 40); do
    port_open 3307 && break
    sleep 1
  done
  if ! port_open 3307; then
    echo "ERROR: MariaDB no respondió en el puerto 3307."
    exit 1
  fi
  echo "✔ MariaDB lista."
fi

if port_open 8000; then
  echo "✔ El servidor ya está corriendo (puerto 8000)."
else
  echo "▶ Arrancando servidor PHP..."
  setsid php -S "$BIND:8000" -t "$WP" </dev/null >/tmp/php-server.log 2>&1 &
  disown 2>/dev/null || true
  sleep 2
fi

echo ""
echo "========================================"
echo "  Sitio:  http://localhost:8000"
echo "  Admin:  http://localhost:8000/wp-admin/"
echo "========================================"
