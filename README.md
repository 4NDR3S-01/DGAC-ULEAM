# DGAC-ULEAM — Reestructuración del micrositio de la Dirección de Gestión y Aseguramiento de la Calidad

Proyecto 2026 que **reemplaza el micrositio actual de la DGAC** de la Universidad Laica Eloy Alfaro de Manabí (ULEAM) por un sitio WordPress nuevo, con la identidad visual institucional, contenido editable sin código y un repositorio documental organizado.

- **Producción:** <https://dgacweb.uleam.edu.ec/> (máquina virtual, servidor privado ULEAM)
- **Sitio anterior:** `departamentos.uleam.edu.ec/gestion-aseguramiento-calidad/` (los enlaces antiguos se redirigen al nuevo)

## Qué incluye

| Área | Descripción |
|---|---|
| **Tema `uleam-calidad`** | Tema propio (`wordpress/wp-content/themes/uleam-calidad`), versión 1.28.0, con paleta y tipografía institucionales ([guía](docs/IDENTIDAD-VISUAL.md)). |
| **Gestión de Procesos** | Procedimientos por subsistema y proceso con sus archivos (Manual, Flujo, Formatos, Versiones anteriores), editables desde el administrador. Shortcodes `[procesos_universitarios]` y `[procesos_info]`. |
| **Repositorio documental** | Cada archivo es un «Documento» clasificado por sección y área; búsqueda con filtros y sugerencias. |
| **Noticias, carrusel y personal** | Carrusel de portada, noticias con imagen automática y carrusel de Recursos Humanos, todo editable. |
| **Asistente virtual** | Chat que busca documentos y responde preguntas frecuentes; funciona sin IA y, opcionalmente, con Groq o Claude (claves en `wp-config.php`). |
| **Redirecciones** | Tabla editable que conserva los enlaces del sitio anterior (SEO). |
| **Plugin `gutena-tabs`** | Pestañas en el editor. |

## Estructura del repositorio

```
wordpress/                  Núcleo de WordPress + tema + plugins (wp-config.php y uploads/ NO se versionan)
herramientas/despliegue/    Empaquetado y verificación para producción
herramientas/               Descarga de archivos oficiales (migración de procesos)
docs/IDENTIDAD-VISUAL.md    Colores y tipografía institucionales
README-DESPLIEGUE.md        Guía para subir el sitio a la máquina virtual
arrancar.docker.sh          Arranque local (MariaDB en Docker + servidor PHP)
.env.example                Variables para desarrollo local
```

## Desarrollo local

Requisitos: Docker, PHP 8.1+ con `mysqli`, `gd`, `mbstring`.

```bash
cp .env.example .env        # edita DB_PASS
./arrancar.docker.sh        # MariaDB en 127.0.0.1:3307 y sitio en http://localhost:8000
./detener.docker.sh
```

Crea `wordpress/wp-config.php` (ignorado por git) con los datos de tu base local; para producción usa `herramientas/despliegue/wp-config.produccion.php.example`. Por defecto todo escucha solo en `127.0.0.1`; para compartirlo en la red local define `BIND=0.0.0.0` en `.env`.

## Producción

Sigue **[README-DESPLIEGUE.md](README-DESPLIEGUE.md)**. Resumen:

```bash
MYSQL_ROOT_PASSWORD=<root-del-contenedor> herramientas/despliegue/preparar-paquete.sh https://dgacweb.uleam.edu.ec wordpress_deploy
# → dist/paquete-uleam-calidad.tar.gz  +  dist/wordpress_deploy-AAAAMMDD.sql.gz
herramientas/despliegue/verificar-sitio.sh https://dgacweb.uleam.edu.ec     # tras desplegar
```

## Seguridad

- Nunca se versionan `wp-config.php`, `.env`, volcados `.sql`, `uploads/` ni credenciales.
- El paquete de producción sale sin contraseñas de desarrollo (los usuarios quedan bloqueados hasta fijar una nueva) y sin URLs de desarrollo.
- Las credenciales usadas durante el desarrollo permanecen en el historial de git: **deben rotarse** y no usarse en producción.
