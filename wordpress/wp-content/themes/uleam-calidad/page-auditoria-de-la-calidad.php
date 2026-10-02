<?php
/**
 * Plantilla de Página: Portal de Auditoría de la Calidad
 *
 * Muestra la información institucional de Misión, Objetivo y Productos (9)
 * mediante pestañas interactivas, y presenta los dos accesos directos
 * a los repositorios documentales del área de Auditoría.
 *
 * @package uleam-calidad
 */

get_header();
?>

<header class="page-header">
	<div class="page-header__wrap">
		<span class="page-header__badge"><i class="fa-solid fa-clipboard-check"></i> AUDITORÍA DE LA CALIDAD · ULEAM</span>
		<h1 class="page-header__title"><?php the_title(); ?></h1>
	</div>
</header>

<div class="entry-content">

	<!-- 1. Encabezado / Introducción con Misión, Objetivo y Productos -->
	<section class="dgac-proc-intro" aria-label="Información General del Área">
		<p class="dgac-proc-intro__lead">
			El área de <strong>Auditoría de la Calidad</strong> de la Dirección de Gestión y Aseguramiento de la Calidad (DGAC) lidera la planificación, ejecución y seguimiento de los controles internos institucionales en la Universidad Laica Eloy Alfaro de Manabí, garantizando la obtención y evaluación objetiva de evidencias para certificar el cumplimiento normativo y promover la mejora continua del Sistema de Gestión de la Calidad (SGC).
		</p>

		<!-- Switcher de Pestañas: Misión / Objetivo / Productos -->
		<div class="dgac-pill-tabs" role="tablist" aria-label="Información Institucional">
			<button type="button" class="dgac-pill-tab is-active" role="tab" aria-selected="true" aria-controls="panel-mision" id="tab-mision" data-tab="mision">
				<span class="dot dot--green"></span> Misión
			</button>
			<button type="button" class="dgac-pill-tab" role="tab" aria-selected="false" aria-controls="panel-objetivo" id="tab-objetivo" data-tab="objetivo">
				<span class="dot dot--orange"></span> Objetivo
			</button>
			<button type="button" class="dgac-pill-tab" role="tab" aria-selected="false" aria-controls="panel-productos" id="tab-productos" data-tab="productos">
				<span class="dot dot--blue"></span> Productos Institucionales (9)
			</button>
		</div>

		<!-- Panel Misión -->
		<div id="panel-mision" class="dgac-overview-panel is-active" role="tabpanel" aria-labelledby="tab-mision">
			<div class="dgac-overview-card">
				<p>
					Área responsable de la planificación, ejecución y seguimiento de los controles internos de calidad, mediante procesos orientados a la obtención y evaluación objetiva de evidencias. Su finalidad es verificar que las actividades, procesos y resultados relacionados con la calidad cumplan con los criterios establecidos en el Sistema de Gestión de la Calidad de la Universidad, así como con las disposiciones contempladas en reglamentos, procedimientos, guías, instructivos y demás normativa aplicable superior e interna.
				</p>
			</div>
		</div>

		<!-- Panel Objetivo -->
		<div id="panel-objetivo" class="dgac-overview-panel" role="tabpanel" aria-labelledby="tab-objetivo">
			<div class="dgac-overview-card">
				<p>
					Ejecutar controles internos de calidad para determinar la eficacia y eficiencia del Sistema de Gestión de la Calidad de la Universidad, promoviendo la mejora continua de los procesos y el cumplimiento de los objetivos institucionales.
				</p>
			</div>
		</div>

		<!-- Panel Productos -->
		<div id="panel-productos" class="dgac-overview-panel" role="tabpanel" aria-labelledby="tab-productos">
			<div class="dgac-products-grid">
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Programa / Plan anual de seguimiento y controles a procedimientos/procesos del SGC.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Planificación individual de seguimiento a procesos, procedimientos, guías e instructivos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Planificación de seguimiento a planes de mejoras.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Planificación de seguimiento al plan de aseguramiento de la calidad institucional.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Listas de verificación.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Fichas de incidencias / hallazgos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Informe individual de seguimiento y control a procedimientos/procesos del SGC.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Informe general de seguimiento y control a procedimientos/procesos del SGC.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Informe de seguimiento a planes de mejoramiento y aseguramiento.</span></div>
			</div>
		</div>
	</section>

	<!-- 2. Ejes de Control y Seguimiento Institucional -->
	<div class="dgac-section-header">
		<span class="dgac-section-kicker">Repositorios y Control</span>
		<h2 class="dgac-section-title"><i class="fa-solid fa-folder-tree" aria-hidden="true"></i> Módulos de Seguimiento y Control</h2>
		<p class="dgac-section-subtitle">Acceda a los repositorios oficiales de cronogramas, informes de evaluación de evidencias y archivos históricos de calidad institucional.</p>
	</div>

	<div class="dgac-audit-hub-grid">
		<!-- Tarjeta 1: Seguimiento y Control a Procesos -->
		<div class="dgac-audit-card">
			<div>
				<div class="dgac-audit-card__icon">
					<i class="fa-solid fa-file-shield" aria-hidden="true"></i>
				</div>
				<h3 class="dgac-audit-card__title">Seguimiento y Control a Procesos del Sistema de Gestión de la Calidad</h3>
				<p class="dgac-audit-card__desc">
					Supervisión sistemática del cumplimiento normativo y desempeño en las funciones sustantivas y adjetivas universitarias. Incluye el manual oficial vigente (PCS-01 V2) y el archivo histórico con cronogramas e informes periódicos.
				</p>
			</div>
			<div class="dgac-audit-card__footer">
				<span class="dgac-audit-card__tag"><i class="fa-solid fa-book" aria-hidden="true"></i> PCS-01 V2</span>
				<a href="<?php echo esc_url( home_url( '/seguimiento-y-control-a-procesos-del-sistema-de-gestion-de-la-calidad/' ) ); ?>" class="dgac-audit-card__btn">
					Ingresar al Módulo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</div>

		<!-- Tarjeta 2: Seguimiento y Control a Planes de Mejora -->
		<div class="dgac-audit-card">
			<div>
				<div class="dgac-audit-card__icon">
					<i class="fa-solid fa-chart-line" aria-hidden="true"></i>
				</div>
				<h3 class="dgac-audit-card__title">Seguimiento y Control a Planes de Mejora</h3>
				<p class="dgac-audit-card__desc">
					Verificación del cumplimiento de compromisos, recomendaciones derivadas de autoevaluaciones, evaluaciones externas y auditorías internas para consolidar la mejora continua de carreras y unidades.
				</p>
			</div>
			<div class="dgac-audit-card__footer">
				<span class="dgac-audit-card__tag"><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> Histórico 2018 - 2023</span>
				<a href="<?php echo esc_url( home_url( '/seguimiento-y-control-a-planes-de-mejora/' ) ); ?>" class="dgac-audit-card__btn">
					Ingresar al Módulo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</div>
	</div>

	<!-- 3. Nota Informativa Institucional -->
	<div class="dgac-note-box">
		<i class="fa-solid fa-circle-info" aria-hidden="true"></i>
		<div class="dgac-note-content">
			<p><strong>Atención y Soporte:</strong> Para la solicitud de copias certificadas de expedientes de auditoría, informes de cierre o listas de verificación, comuníquese con el <strong>Área de Auditoría y Control de la Calidad</strong> a través del correo institucional <a href="mailto:maria.salas@uleam.edu.ec">maria.salas@uleam.edu.ec</a>.</p>
		</div>
	</div>

</div>

<?php get_footer(); ?>
