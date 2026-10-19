-- Ajustes para que el sitio funcione bajo /gestion-aseguramiento-calidad/ (aplicar después de reemplazar-urls.php).
-- Los enlaces con ruta absoluta (href="/…" o src="/…") de contenido se prefijan con la subcarpeta.
-- Solo se ejecuta sobre la copia de trabajo; la idempotencia se garantiza porque el patrón exige que no esté ya prefijado.
UPDATE wp_posts
   SET post_content = REPLACE(REPLACE(post_content, 'href="/', 'href="/gestion-aseguramiento-calidad/'), 'src="/', 'src="/gestion-aseguramiento-calidad/')
 WHERE post_content REGEXP '(href|src)="/[a-z]'
   AND post_content NOT REGEXP '(href|src)="/gestion-aseguramiento-calidad/';
