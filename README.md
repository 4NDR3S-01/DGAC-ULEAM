# Identidad visual institucional — ULEAM

Documentación de **colores** y **tipografía** según la guía *Aplicación de nueva gráfica institucional* de la Universidad Laica Eloy Alfaro de Manabí (ULEAM).

---

## Tipografía

**Familia tipográfica oficial:** [Montserrat](https://fonts.google.com/specimen/Montserrat)

| Peso | Uso sugerido |
|------|----------------|
| **Montserrat Regular** (`400`) | Cuerpo de texto, párrafos, descripciones |
| **Montserrat SemiBold** (`600`) | Títulos, subtítulos, énfasis, botones, menús |

### Uso en web (Google Fonts)

```html
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet" />
```

```css
:root {
  --font-uleam: "Montserrat", system-ui, sans-serif;
}

body {
  font-family: var(--font-uleam);
  font-weight: 400;
}

h1, h2, h3, .btn, nav {
  font-family: var(--font-uleam);
  font-weight: 600;
}
```

---

## Paleta de colores

### Azules institucionales (80 % de predominancia)

Monocromía del tono azul. Valores **WEB (Hex)**, **RGB** y **CMYK**.

| Vista | Hexadecimal | RGB | CMYK | Rol sugerido |
|-------|-------------|-----|------|----------------|
| ![#005689](https://placehold.co/48x24/005689/005689) | `#005689` | `0, 86, 137` | `95, 63, 22, 7` | Azul principal / header |
| ![#46A4FF](https://placehold.co/48x24/46A4FF/46A4FF) | `#46A4FF` | `70, 164, 255` | `65, 29, 0, 0` | Azul claro / acentos web |
| ![#0059A8](https://placehold.co/48x24/0059A8/0059A8) | `#0059A8` | `0, 89, 168` | `94, 64, 0, 0` | Azul medio / enlaces |
| ![#004499](https://placehold.co/48x24/004499/004499) | `#004499` | `0, 68, 153` | `100, 77, 3, 0` | Azul profundo / tipografía fuerte |

### Variables CSS recomendadas

```css
:root {
  /* Primarios institucionales (80%) */
  --uleam-blue-900: #004499;
  --uleam-blue-800: #005689;
  --uleam-blue-700: #0059A8;
  --uleam-blue-400: #46A4FF;

  --uleam-navy: var(--uleam-blue-800);
  --uleam-accent: var(--uleam-blue-400);
}
```

---

## Colores de apoyo (20 % cromática secundaria)

Rojos institucionales que acompañan a los azules. Valores **WEB (Hex)**, **RGB** y **CMYK**. Usar con moderación (máx. ~20 % de la composición).

| Vista | Color | Hexadecimal | RGB | CMYK |
|-------|-------|-------------|-----|------|
| ![#FF0000](https://placehold.co/48x24/FF0000/FF0000) | Color de apoyo 1 | `#FF0000` | `255, 0, 0` | `0, 95, 91, 0` |
| ![#B11719](https://placehold.co/48x24/B11719/B11719) | Color de apoyo 2 | `#B11719` | `177, 23, 25` | `20, 100, 97, 13` |
| ![#DB1D1F](https://placehold.co/48x24/DB1D1F/DB1D1F) | Color de apoyo 3 | `#DB1D1F` | `219, 29, 31` | `5, 97, 93, 0` |

### Variables CSS (apoyo)

```css
:root {
  --uleam-red-500: #FF0000; /* Color de apoyo 1 */
  --uleam-red-700: #DB1D1F; /* Color de apoyo 3 */
  --uleam-red-900: #B11719; /* Color de apoyo 2 */
}
```

### Uso accesible (contraste WCAG 2.1)

Para que el texto sea legible, el contraste mínimo es **4.5:1** (texto normal) y **3:1** (texto grande ≥ 24 px o elementos gráficos).

| Color | Sobre blanco | Texto blanco encima | Sobre azul `#004499` | Uso permitido |
|-------|--------------|---------------------|----------------------|---------------|
| Apoyo 1 `#FF0000` | 4.00:1 | 4.00:1 | 2.30:1 | **Solo decorativo**: líneas, franjas, detalles gráficos. No para texto pequeño. |
| Apoyo 2 `#B11719` | 6.99:1 | 6.99:1 | 1.31:1 | Texto pequeño en rojo (antetítulos, etiquetas) y fondos con texto blanco. |
| Apoyo 3 `#DB1D1F` | 4.99:1 | 4.99:1 | 1.84:1 | Iconos, bordes, indicadores activos y texto. |

> **Nunca combinar rojo y azul institucional como texto/fondo**: el contraste no supera 2.3:1.

### Combinación con el color principal (sitio web)

| Elemento | Color |
|----------|-------|
| Cabecera, menú, botones, enlaces, títulos, fondos | Azules institucionales (**80 %**) |
| Antetítulos de sección ("REPOSITORIO DOCUMENTAL", "SERVICIOS DEL ÁREA") | Apoyo 2 `#B11719` |
| Iconos de los títulos de sección | Apoyo 3 `#DB1D1F` |
| Indicador de la página activa en el menú | Apoyo 3 `#DB1D1F` |
| Línea bajo el título de cada página y franja superior del pie | Degradado Apoyo 3 → Apoyo 1 |
| Iconos de archivos PDF y avisos | Apoyo 3 sobre fondo rojo muy claro |

Los colores se definen una sola vez en `wordpress/wp-content/themes/uleam-calidad/css/variables.css`; el resto del tema los usa mediante las variables.

### Colores por facultad (complementarios)

Paleta asociada a facultades, para piezas propias de cada una.

| Color | Hex (aprox.) | Facultad |
|-------|--------------|----------|
| ![#E84E2F](https://placehold.co/48x24/E84E2F/E84E2F) | `#E84E2F` | Fac. de Educación y Turismo |
| ![#39BBD8](https://placehold.co/48x24/39BBD8/39BBD8) | `#39BBD8` | Fac. de Ciencias de la Salud |
| ![#309947](https://placehold.co/48x24/309947/309947) | `#309947` | Fac. de Ciencias de la Vida y Tecnología de la Información |
| ![#633252](https://placehold.co/48x24/633252/633252) | `#633252` | Fac. de Artes, Humanidades y Patrimonio |
| ![#035685](https://placehold.co/48x24/035685/035685) | `#035685` | Fac. de Ciencias Administrativas, Contables y Comercio |
| ![#A73145](https://placehold.co/48x24/A73145/A73145) | `#A73145` | Fac. de Ciencias Sociales |
| ![#3C4A93](https://placehold.co/48x24/3C4A93/3C4A93) | `#3C4A93` | Fac. de Ingeniería, Industria y Construcción |

> Los hex por facultad se extrajeron de la guía visual. Si la institución publica valores oficiales distintos, actualizar esta sección.

```css
:root {
  --uleam-fac-educacion: #E84E2F;
  --uleam-fac-salud: #39BBD8;
  --uleam-fac-vida-ti: #309947;
  --uleam-fac-artes: #633252;
  --uleam-fac-admin: #035685;
  --uleam-fac-sociales: #A73145;
  --uleam-fac-ingenieria: #3C4A93;
}
```

---

## Jerarquía de uso

1. **80 %** — Azules institucionales (`#005689`, `#0059A8`, `#004499`, `#46A4FF`)
2. **20 %** — Colores de apoyo: rojos `#B11719`, `#DB1D1F` y `#FF0000` (solo decorativo) como acento; colores por facultad en piezas de cada facultad
3. **Tipografía** — Solo Montserrat Regular y SemiBold en piezas institucionales

---

## Contenido institucional

Esta guía aplica a piezas digitales y de impresión de la ULEAM (sitio web, documentos, presentaciones y material de la Dirección de Gestión y Aseguramiento de la Calidad).

**Proyecto:** DGAC-ULEAM  
**Fuente:** Manual de aplicación de nueva gráfica institucional (Canva / ULEAM)
