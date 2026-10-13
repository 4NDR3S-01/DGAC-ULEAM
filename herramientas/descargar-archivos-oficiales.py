#!/usr/bin/env python3
"""Descarga a wordpress/wp-content/uploads los documentos de Gestión de Procesos
que enlaza el sitio oficial (departamentos.uleam.edu.ec). Los uploads no van en git.

Uso (desde cualquier carpeta):  python3 herramientas/descargar-archivos-oficiales.py
Es reanudable: salta lo que ya existe y reutiliza archivos que estén en otra carpeta.
"""
import collections, json, os, shutil, subprocess, time, unicodedata
from concurrent.futures import ThreadPoolExecutor
from urllib.parse import quote, urlsplit, urlunsplit

AQUI = os.path.dirname(os.path.abspath(__file__))
RAIZ = os.path.dirname(AQUI)
WP = os.path.join(RAIZ, 'wordpress')
UPLOADS = os.path.join(WP, 'wp-content', 'uploads')
UA = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120 Safari/537.36'

with open(os.path.join(AQUI, 'archivos-oficiales.json'), encoding='utf-8') as f:
    datos = json.load(f)  # {ruta local: URL oficial}


def norm(s):
    s = unicodedata.normalize('NFD', s)
    return ''.join(c for c in s if unicodedata.category(c) != 'Mn').lower()


por_nombre = collections.defaultdict(list)
for dp, _, fns in os.walk(UPLOADS):
    for fn in fns:
        por_nombre[norm(fn)].append(os.path.join(dp, fn))

pendientes, copiados = [], 0
for ruta, url in datos.items():
    destino = WP + ruta
    if os.path.exists(destino):
        continue
    otro = por_nombre.get(norm(os.path.basename(ruta)))
    if otro:
        os.makedirs(os.path.dirname(destino), exist_ok=True)
        shutil.copy2(otro[0], destino)
        copiados += 1
        continue
    pendientes.append((destino, url))
print(f'Copiados desde otra carpeta: {copiados}. Por descargar: {len(pendientes)}', flush=True)


def bajar(item):
    destino, url = item
    sp = urlsplit(url)
    url = urlunsplit((sp.scheme, sp.netloc, quote(sp.path, safe='/%'), '', ''))
    os.makedirs(os.path.dirname(destino), exist_ok=True)
    tmp = destino + '.part'
    for _ in range(3):
        r = subprocess.run(['curl', '-sfL', '-m', '240', '-A', UA, url, '-o', tmp], capture_output=True)
        if r.returncode == 0 and os.path.exists(tmp) and os.path.getsize(tmp) > 0:
            os.rename(tmp, destino)
            return destino, True
        time.sleep(2)
    if os.path.exists(tmp):
        os.remove(tmp)
    return destino, False


ok = fallos = 0
with ThreadPoolExecutor(6) as ex:
    for destino, bien in ex.map(bajar, pendientes):
        if bien:
            ok += 1
        else:
            fallos += 1
            print('NO SE PUDO:', os.path.relpath(destino, WP), flush=True)
        if (ok + fallos) % 50 == 0:
            print(f'  {ok + fallos}/{len(pendientes)}', flush=True)
print(f'Listo. Descargados: {ok}. Fallidos: {fallos} (suelen ser enlaces rotos del sitio oficial).')
