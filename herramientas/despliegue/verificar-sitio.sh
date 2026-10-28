#!/usr/bin/env bash
# Verifica un sitio ya desplegado: archivos sensibles bloqueados, cabeceras de seguridad y
# que ninguna URL apunte a localhost/IPs privadas/túneles. Solo hace peticiones GET/HEAD.
# Uso: herramientas/despliegue/verificar-sitio.sh [URL]   (por defecto https://dgacweb.uleam.edu.ec)
set -uo pipefail
# CURL_OPTS permite pruebas locales, p. ej. CURL_OPTS="-k --resolve dgacweb.uleam.edu.ec:443:127.0.0.1"
c() { curl $CURL_OPTS "$@"; }
URL="${1:-https://dgacweb.uleam.edu.ec}"; URL="${URL%/}"
fallos=0
ok()  { echo "  ✔ $1"; }
mal() { echo "  ✘ $1"; fallos=$((fallos+1)); }

echo "▶ Archivos sensibles (deben dar 403/404)"
for ruta in /wp-config.php /xmlrpc.php /readme.html /license.txt /.git/HEAD /.env /wp-content/uploads/.htaccess /wp-content/debug.log; do
  c=$(c -s -o /dev/null -w '%{http_code}' --max-time 20 "$URL$ruta")
  case "$c" in 403|404) ok "$ruta → $c" ;; *) mal "$ruta → $c (esperado 403/404)" ;; esac
done

echo "▶ Redirección a HTTPS"
if [[ "$URL" == https://* ]]; then
  c=$(c -s -o /dev/null -w '%{http_code} %{redirect_url}' --max-time 20 "http://${URL#https://}/")
  [[ "$c" == 301*https://* || "$c" == 302*https://* ]] && ok "http → https ($c)" || mal "http no redirige a https ($c)"
fi

echo "▶ Cabeceras de seguridad"
cab=$(c -sI --max-time 20 "$URL/")
for h in Strict-Transport-Security X-Content-Type-Options Content-Security-Policy Referrer-Policy X-Frame-Options; do
  grep -qi "^$h:" <<<"$cab" && ok "$h" || mal "falta $h"
done
grep -qi '^X-Powered-By:' <<<"$cab" && mal "X-Powered-By expuesto" || ok "sin X-Powered-By"

echo "▶ Sin referencias de desarrollo en las páginas"
for ruta in / /wp-login.php /feed/ /wp-json/; do
  html=$(c -sL --max-time 30 "$URL$ruta")
  n=$(grep -cE 'localhost|127\.0\.0\.1|192\.168\.|10\.7\.|trycloudflare|:8000' <<<"$html")
  [ "$n" -eq 0 ] && ok "$ruta limpia" || mal "$ruta contiene $n líneas con referencias de desarrollo"
done

echo "▶ Usuarios no enumerables"
c=$(c -s -o /dev/null -w '%{http_code}' --max-time 20 "$URL/wp-json/wp/v2/users")
case "$c" in 401|403|404) ok "/wp-json/wp/v2/users → $c" ;; *) mal "/wp-json/wp/v2/users → $c (expone usuarios)" ;; esac

echo; [ "$fallos" -eq 0 ] && echo "✔ Todo correcto" || echo "✘ $fallos comprobación(es) fallidas"
exit "$fallos"
