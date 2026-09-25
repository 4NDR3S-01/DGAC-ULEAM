#!/usr/bin/env bash
# ============================================================
#  Detiene WordPress local (servidor PHP + MariaDB Docker)
#  Uso:  ./detener.sh
# ============================================================

echo "▶ Deteniendo servidor PHP (puerto 8000)..."
if fuser 8000/tcp >/dev/null 2>&1; then
  fuser -k 8000/tcp >/dev/null 2>&1
  echo "✔ Servidor PHP detenido."
else
  echo "  (no estaba corriendo)"
fi

echo "▶ Deteniendo MariaDB (Docker, puerto 3307)..."
if docker ps --format '{{.Names}}' 2>/dev/null | grep -qx 'uleam-mariadb'; then
  docker stop uleam-mariadb >/dev/null
  echo "✔ MariaDB detenida."
elif fuser 3307/tcp >/dev/null 2>&1; then
  fuser -k 3307/tcp >/dev/null 2>&1
  echo "✔ Proceso en puerto 3307 detenido."
else
  echo "  (no estaba corriendo)"
fi
