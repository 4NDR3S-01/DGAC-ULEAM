#!/usr/bin/env python3
"""Compara los archivos que cita la base de trabajo con los que existen en una carpeta uploads/.

Uso:  MYSQL_ROOT_PASSWORD=<root> python3 herramientas/despliegue/verificar-uploads.py [carpeta-uploads] [base]
Por defecto: wordpress/wp-content/uploads y la base wordpress_deploy (la de preparar-paquete.sh, en el contenedor uleam-mariadb).
Escribe la lista de faltantes en dist/uploads-faltantes.txt. Solo lee: no modifica la base ni los archivos.
"""
import os, re, subprocess, sys
from collections import Counter
from urllib.parse import unquote

RAIZ = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
UP = sys.argv[1] if len(sys.argv) > 1 else os.path.join(RAIZ, "wordpress", "wp-content", "uploads")
BD = sys.argv[2] if len(sys.argv) > 2 else "wordpress_deploy"
CLAVE = os.environ.get("MYSQL_ROOT_PASSWORD")
if not CLAVE:
    sys.exit("✘ Define MYSQL_ROOT_PASSWORD (clave root del contenedor uleam-mariadb)")
if not re.fullmatch(r"[A-Za-z0-9_]+", BD):
    sys.exit("✘ Nombre de base no válido")


def q(sql):
    r = subprocess.run(["docker", "exec", "-e", "MYSQL_PWD=" + CLAVE, "uleam-mariadb", "mariadb", "-uroot", BD, "-N", "-e", sql],
                       capture_output=True, text=True)
    if r.returncode:
        sys.exit("✘ " + r.stderr.strip())
    return r.stdout.splitlines()


adjuntos = set(q("SELECT meta_value FROM wp_postmeta WHERE meta_key='_wp_attached_file'"))
visible = " ".join(q("SELECT post_content FROM wp_posts WHERE post_status IN ('publish','private') AND post_type NOT IN ('revision','customize_changeset')"))
visible += " " + " ".join(q("SELECT meta_value FROM wp_postmeta WHERE meta_key LIKE '\\_documento%'"))
citados = {unquote(x) for x in re.findall(r"wp-content/uploads/([^\s\"'<>)\\]+)", visible)}


def faltan(s):
    return sorted(r for r in s if not os.path.exists(os.path.join(UP, r)))


fa, fc = faltan(adjuntos), faltan(citados)
n = sum(len(fs) for _, _, fs in os.walk(UP))
gb = sum(os.path.getsize(os.path.join(d, f)) for d, _, fs in os.walk(UP) for f in fs) / 1e9
print(f"Carpeta: {UP} → {n} archivos, {gb:.2f} GB")
print(f"Adjuntos que cita la base: {len(adjuntos)} | faltan: {len(fa)}")
print(f"Archivos citados en contenido publicado y documentos: {len(citados)} | faltan: {len(fc)}  ← estos se ven rotos en el sitio")
todos = sorted(set(fa) | set(fc))
os.makedirs(os.path.join(RAIZ, "dist"), exist_ok=True)
open(os.path.join(RAIZ, "dist", "uploads-faltantes.txt"), "w").write("\n".join(todos) + "\n")
print("Por mes (top 8):", Counter("/".join(r.split("/")[:2]) for r in todos).most_common(8))
print("Lista completa: dist/uploads-faltantes.txt")
sys.exit(1 if fc else 0)
