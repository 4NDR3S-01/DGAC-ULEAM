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

Paleta secundaria asociada a facultades. Usar con moderación (máx. ~20 % de la composición).

| Color | Hex (aprox.) | Facultad |
|-------|--------------|----------|
| ![#E84E2F](https://placehold.co/48x24/E84E2F/E84E2F) | `#E84E2F` | Fac. de Educación y Turismo |
| ![#39BBD8](https://placehold.co/48x24/39BBD8/39BBD8) | `#39BBD8` | Fac. de Ciencias de la Salud |
| ![#309947](https://placehold.co/48x24/309947/309947) | `#309947` | Fac. de Ciencias de la Vida y Tecnología de la Información |
| ![#633252](https://placehold.co/48x24/633252/633252) | `#633252` | Fac. de Artes, Humanidades y Patrimonio |
| ![#035685](https://placehold.co/48x24/035685/035685) | `#035685` | Fac. de Ciencias Administrativas, Contables y Comercio |
| ![#A73145](https://placehold.co/48x24/A73145/A73145) | `#A73145` | Fac. de Ciencias Sociales |
| ![#3C4A93](https://placehold.co/48x24/3C4A93/3C4A93) | `#3C4A93` | Fac. de Ingeniería, Industria y Construcción |

> Los hex de colores de apoyo se extrajeron de la guía visual. Si la institución publica valores oficiales distintos, actualizar esta sección.

### Variables CSS (apoyo)

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
2. **20 %** — Colores de apoyo por facultad / acentos secundarios
3. **Tipografía** — Solo Montserrat Regular y SemiBold en piezas institucionales

---

## Contenido institucional

Esta guía aplica a piezas digitales y de impresión de la ULEAM (sitio web, documentos, presentaciones y material de la Dirección de Gestión y Aseguramiento de la Calidad).

**Proyecto:** DGAC-ULEAM  
**Fuente:** Manual de aplicación de nueva gráfica institucional (Canva / ULEAM)
