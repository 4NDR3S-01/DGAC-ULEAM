#!/usr/bin/env bash
# Respaldo diario del sitio (se ejecuta EN la VM): base de datos + uploads + wp-config.
# Uso:   respaldo.sh [RAIZ_SITIO] [DESTINO]      (por defecto /var/www/dgacweb y /var/backups/dgacweb)
# Cron:  0 2 * * * /var/www/dgacweb/../respaldo.sh   (ver README-DESPLIEGUE.md, paso 9)
# Los respaldos contienen datos y hashes de usuarios: copiarlos FUERA de la VM (rsync/scp a otro servidor).
set -euo pipefail
umask 077

RAIZ="${1:-/var/www/dgacweb}"
DEST="${2:-/var/backups/dgacweb}"
RETENCION_DIAS="${RETENCION_DIAS:-14}"
[ -f "$RAIZ/wp-config.php" ] || { echo "✘ No encuentro $RAIZ/wp-config.php" >&2; exit 1; }

# Credenciales desde wp-config.php, pasadas por variable de entorno (no por argumentos).
leer() { php -r 'define("ABSPATH","/"); $s=file_get_contents($argv[1]); preg_match("/define\(\s*[\x27\"]".$argv[2]."[\x27\"]\s*,\s*[\x27\"]([^\x27\"]*)[\x27\"]/",$s,$m); echo $m[1] ?? "";' "$RAIZ/wp-config.php" "$1"; }
BD="$(leer DB_NAME)"; USUARIO="$(leer DB_USER)"; HOST="$(leer DB_HOST)"; export MYSQL_PWD="$(leer DB_PASSWORD)"
[ -n "$BD" ] && [ -n "$USUARIO" ] && [ -n "$MYSQL_PWD" ] || { echo "✘ No pude leer las credenciales de la base" >&2; exit 1; }

FECHA="$(date +%Y%m%d-%H%M%S)"
mkdir -p "$DEST"

mysqldump -h "${HOST:-localhost}" -u "$USUARIO" --single-transaction --routines --triggers "$BD" | gzip > "$DEST/bd-$FECHA.sql.gz"
tar -C "$RAIZ" -czf "$DEST/uploads-$FECHA.tar.gz" wp-content/uploads
cp "$RAIZ/wp-config.php" "$DEST/wp-config-$FECHA.php"

# Comprobación mínima: los archivos existen y no están vacíos.
for f in "$DEST/bd-$FECHA.sql.gz" "$DEST/uploads-$FECHA.tar.gz"; do
  [ -s "$f" ] || { echo "✘ Respaldo vacío: $f" >&2; exit 2; }
done
gzip -t "$DEST/bd-$FECHA.sql.gz"

# Retención
find "$DEST" -type f -mtime +"$RETENCION_DIAS" -delete
echo "✔ Respaldo en $DEST ($FECHA); se conservan $RETENCION_DIAS días"
