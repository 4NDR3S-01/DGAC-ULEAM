#!/usr/bin/env bash
# ============================================================
#  Arranca WordPress local (ULEAM Calidad)
#  MariaDB (puerto 3307) + servidor PHP (http://localhost:8000)
#  Uso:  ./arrancar.sh
# ============================================================

# Extensiones PHP (gd, imagick, soap) cargadas sin sudo
export PHP_INI_SCAN_DIR=":/home/andr3s/.local/php-ext/conf.d"

WP="/home/andr3s/Documentos/wordpress-7.1.2/wordpress"

# ---- 1. Base de datos MariaDB (instancia local, puerto 3307) ----
if fuser 3307/tcp >/dev/null 2>&1; then
  echo "✔ MariaDB ya está corriendo (puerto 3307)."
else
  echo "▶ Arrancando MariaDB..."
  mariadbd --no-defaults \
    --datadir="$HOME/local-mariadb/data" \
    --socket="$HOME/local-mariadb/mysql.sock" \
    --pid-file="$HOME/local-mariadb/mysql.pid" \
    --port=3307 \
    --bind-address=127.0.0.1 \
    > /tmp/mariadb.log 2>&1 &
  # Espera a que el puerto esté listo (máx ~15 s)
  for i in $(seq 1 15); do
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
  nohup php -S 0.0.0.0:8000 -t "$WP" > /tmp/php-server.log 2>&1 &
  sleep 2
fi

echo ""
IP_LAN=$(hostname -I 2>/dev/null | awk '{print $1}')
echo "========================================"
echo "  Sitio (local):   http://localhost:8000"
echo "  Sitio (en red):  http://${IP_LAN:-<tu-IP>}:8000"
echo "  Admin:  http://localhost:8000/wp-admin/"
echo "  usuario: admin   contraseña: Uleam2026"
echo "========================================"
