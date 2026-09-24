#!/usr/bin/env bash
# ============================================================
#  Detiene WordPress local (servidor PHP + MariaDB)
#  Uso:  ./detener.sh
# ============================================================

echo "▶ Deteniendo servidor PHP (puerto 8000)..."
if fuser 8000/tcp >/dev/null 2>&1; then
  fuser -k 8000/tcp >/dev/null 2>&1
  echo "✔ Servidor PHP detenido."
else
  echo "  (no estaba corriendo)"
fi

echo "▶ Deteniendo MariaDB (puerto 3307)..."
if fuser 3307/tcp >/dev/null 2>&1; then
  # Apagado limpio; si no responde, fuerza el cierre del proceso
  mariadb-admin --socket="$HOME/local-mariadb/mysql.sock" -u root shutdown >/dev/null 2>&1
  sleep 1
  if fuser 3307/tcp >/dev/null 2>&1; then
    fuser -k 3307/tcp >/dev/null 2>&1
  fi
  echo "✔ MariaDB detenida."
else
  echo "  (no estaba corriendo)"
fi
