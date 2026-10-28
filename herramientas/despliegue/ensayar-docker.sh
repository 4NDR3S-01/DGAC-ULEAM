#!/usr/bin/env bash
# Ensaya el despliegue completo en local: Apache+PHP+MariaDB en Docker, con el paquete y la base de dist/.
# Imita la VM (raíz del dominio, HTTPS con certificado autofirmado) y ejecuta verificar-sitio.sh.
# Uso:  herramientas/despliegue/ensayar-docker.sh [--limpiar]
# Requiere los puertos 80 y 443 libres en 127.0.0.1. No toca la BD de desarrollo ni producción.
set -euo pipefail
RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DEP="$RAIZ/herramientas/despliegue"
DIST="$RAIZ/dist"
RED=dgac-ensayo
SITIO="$DIST/ensayo-sitio"

limpiar() { docker rm -f dgac-ensayo-web dgac-ensayo-db >/dev/null 2>&1 || true; docker network rm "$RED" >/dev/null 2>&1 || true; }
limpiar
[ "${1:-}" = "--limpiar" ] && { rm -rf "$SITIO"; echo "✔ Entorno de ensayo eliminado"; exit 0; }

PAQ="$DIST/paquete-uleam-calidad.tar.gz"; SQL="$(ls -t "$DIST"/wordpress_deploy-*.sql.gz 2>/dev/null | head -1)"
[ -f "$PAQ" ] && [ -n "$SQL" ] || { echo "✘ Falta el paquete o la base en dist/ (ejecuta preparar-paquete.sh)" >&2; exit 1; }

# Claves de usar y tirar para el ensayo (no son las de ningún entorno real).
ROOT_PW="$(head -c16 /dev/urandom | od -An -tx1 | tr -d ' \n')"; DB_PW="$(head -c16 /dev/urandom | od -An -tx1 | tr -d ' \n')"

echo "▶ Construyendo la imagen Apache+PHP"
docker build -q -t dgac-ensayo-apache "$DEP/ensayo-docker" >/dev/null
docker network create "$RED" >/dev/null

echo "▶ Base de datos"
docker run -d --name dgac-ensayo-db --network "$RED" -e MYSQL_ROOT_PASSWORD="$ROOT_PW" -e MYSQL_DATABASE=dgac_web \
  -e MYSQL_USER=dgac_web -e MYSQL_PASSWORD="$DB_PW" mariadb:11 >/dev/null
for _ in $(seq 1 60); do docker exec -e MYSQL_PWD="$ROOT_PW" dgac-ensayo-db mariadb -uroot -e 'select 1' >/dev/null 2>&1 && break; sleep 1; done
gunzip -c "$SQL" | docker exec -i -e MYSQL_PWD="$DB_PW" dgac-ensayo-db mariadb -udgac_web dgac_web

echo "▶ Código (como en la VM: extraer paquete + wp-config desde la plantilla)"
rm -rf "$SITIO"; mkdir -p "$SITIO"; tar -xzf "$PAQ" -C "$SITIO" --strip-components=1
sed -e "s/<base-de-datos>/dgac_web/" -e "s/<usuario>/dgac_web/" -e "s/<clave>/$DB_PW/" -e "s/<host-de-la-base>/dgac-ensayo-db/" \
    -e "s/<generar>/ensayo-$(head -c8 /dev/urandom | od -An -tx1 | tr -d ' \n')/" "$DEP/wp-config.produccion.php.example" > "$SITIO/wp-config.php"
mkdir -p "$SITIO/wp-content/uploads"

docker run -d --name dgac-ensayo-web --network "$RED" -p 127.0.0.1:80:80 -p 127.0.0.1:443:443 \
  -v "$SITIO":/var/www/html docker.io/library/dgac-ensayo-apache >/dev/null
docker exec dgac-ensayo-web bash -c 'chown -R www-data:www-data /var/www/html/wp-content/uploads; chmod 640 /var/www/html/wp-config.php; chown root:www-data /var/www/html/wp-config.php'
sleep 3

echo "▶ Verificación"
CURL_OPTS="-k --resolve dgacweb.uleam.edu.ec:443:127.0.0.1 --resolve dgacweb.uleam.edu.ec:80:127.0.0.1" \
  "$DEP/verificar-sitio.sh" https://dgacweb.uleam.edu.ec || true
echo; echo "Sitio de ensayo: https://dgacweb.uleam.edu.ec (añade '127.0.0.1 dgacweb.uleam.edu.ec' a /etc/hosts para verlo en el navegador)."
echo "Limpiar: herramientas/despliegue/ensayar-docker.sh --limpiar"
