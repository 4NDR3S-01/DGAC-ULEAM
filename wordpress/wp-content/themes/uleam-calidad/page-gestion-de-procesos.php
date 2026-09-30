<?php
/**
 * Plantilla de Página: Gestión de Procesos Institucionales
 *
 * Muestra la información integral de Misión, Objetivo, Productos y
 * los 4 Subsistemas de Gestión Universitaria con pestañas, acordeones y
 * despliegue de archivos asociados con óptima experiencia de usuario (UI/UX).
 *
 * @package uleam-calidad
 */

get_header();
?>

<header class="page-header">
	<div class="page-header__wrap">
		<span class="page-header__badge"><i class="fa-solid fa-diagram-project"></i> GESTIÓN DE PROCESOS · ULEAM</span>
		<h1 class="page-header__title"><?php the_title(); ?></h1>
	</div>
</header>

<div class="entry-content">

	<!-- 1. Encabezado / Introducción con Misión, Objetivo y Productos -->
	<section class="dgac-proc-intro" aria-label="Información General del Área">
		<p class="dgac-proc-intro__lead">
			El área de <strong>Gestión de Procesos</strong> de la Dirección de Gestión y Aseguramiento de la Calidad (DGAC) lidera la estandarización, actualización y optimización de los procedimientos académicos y administrativos de la Universidad Laica Eloy Alfaro de Manabí, garantizando operatividad, transparencia y mejora continua en cumplimiento de la misión institucional.
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
				<span class="dot dot--blue"></span> Productos Institucionales (14)
			</button>
		</div>

		<!-- Panel Misión -->
		<div id="panel-mision" class="dgac-overview-panel is-active" role="tabpanel" aria-labelledby="tab-mision">
			<div class="dgac-overview-card">
				<p>
					Liderar el mejoramiento continuo de los procedimientos institucionales, con proyección hacia certificaciones y acreditaciones nacionales e internacionales, mediante el desarrollo de procesos orientados a la optimización y simplificación, garantizando eficiencia, transparencia y excelencia en el cumplimiento de la misión universitaria.
				</p>
			</div>
		</div>

		<!-- Panel Objetivo -->
		<div id="panel-objetivo" class="dgac-overview-panel" role="tabpanel" aria-labelledby="tab-objetivo">
			<div class="dgac-overview-card">
				<p>
					El área de gestión de procesos es la responsable de la elaboración y/o actualización de los procedimientos académicos y administrativos, así como de la aprobación de formatos institucionales para garantizar la operatividad de las funciones sustantivas y adjetivas de la universidad.
				</p>
			</div>
		</div>

		<!-- Panel Productos -->
		<div id="panel-productos" class="dgac-overview-panel" role="tabpanel" aria-labelledby="tab-productos">
			<div class="dgac-products-grid">
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Plan anual de trabajo – Área Gestión de Procesos 2026.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Catálogo de Procedimientos Institucionales.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Catálogo de Formatos Institucionales.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Revisión y codificación de manuales de procedimientos, instructivos o guías de trabajo y formatos institucionales.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Informes sobre revisión y control de calidad de normativas institucionales solicitadas por unidades orgánicas.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Elaboración y actualización de manuales de procedimientos, instructivos o guías de trabajo y formatos institucionales.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Diseño, revisión o actualización de diagramas de flujo de procedimientos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Implementación de recomendaciones de autoevaluación, evaluación externa, auditorías y seguimiento a procesos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Informes o actas de socialización de procedimientos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Actas de verificación de implementación de procedimientos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Actas técnicas de reunión y visitas in-situ orientadas a la gestión del área de procesos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Actualización de reglamentos, normas y/o procedimientos del área de Gestión de Procesos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Revisión de estructuras funcionales, organización y sistemas administrativos.</span></div>
				<div class="dgac-product-item"><i class="fa-solid fa-circle-check"></i> <span>Informe anual de actividades.</span></div>
			</div>
		</div>
	</section>


	<!-- 2. Banner de Catálogos Oficiales Vigentes -->
	<section class="dgac-catalog-banner" aria-label="Catálogos Institucionales">
		<div class="dgac-catalog-banner__head">
			<i class="fa-solid fa-thumbtack"></i>
			<h3>Consulte los Manuales de Procedimientos, Guías, Instructivos y Formatos vigentes en los siguientes Catálogos:</h3>
		</div>
		<div class="dgac-catalog-cards">
			<a href="#dgac-procedimientos-modal" class="dgac-catalog-card dgac-catalog-card--procedimientos dgac-trigger-doc" data-doc-title="Catálogo de Procedimientos Institucionales ULEAM 2026" data-doc-code="CAT-PROC-2026" data-doc-type="Procedimientos">
				<div class="dgac-catalog-card__info">
					<div class="dgac-catalog-card__icon"><i class="fa-solid fa-book-bookmark"></i></div>
					<div>
						<h4 class="dgac-catalog-card__title">Catálogo de Procedimientos Institucionales</h4>
						<p class="dgac-catalog-card__desc">Compendio unificado de manuales, instructivos y guías vigentes</p>
					</div>
				</div>
				<span class="dgac-catalog-card__btn"><i class="fa-solid fa-file-pdf"></i> Consultar <i class="fa-solid fa-arrow-right"></i></span>
			</a>

			<a href="#dgac-formatos-modal" class="dgac-catalog-card dgac-catalog-card--formatos dgac-trigger-doc" data-doc-title="Catálogo de Formatos Institucionales Oficiales ULEAM" data-doc-code="CAT-FORM-2026" data-doc-type="Formatos">
				<div class="dgac-catalog-card__info">
					<div class="dgac-catalog-card__icon"><i class="fa-solid fa-folder-tree"></i></div>
					<div>
						<h4 class="dgac-catalog-card__title">Catálogo de Formatos Institucionales</h4>
						<p class="dgac-catalog-card__desc">Plantillas y formatos oficiales aprobados por la DGAC</p>
					</div>
				</div>
				<span class="dgac-catalog-card__btn"><i class="fa-solid fa-file-lines"></i> Consultar <i class="fa-solid fa-arrow-right"></i></span>
			</a>
		</div>
	</section>


	<!-- 3. Sección Principal: SUBSISTEMAS DE GESTIÓN UNIVERSITARIA -->
	<section class="dgac-subsistemas-section" aria-label="Subsistemas de Gestión Universitaria">
		<div class="dgac-section-title-wrap">
			<h2 class="dgac-section-title"><i class="fa-solid fa-circle-check"></i> Subsistemas de Gestión Universitaria</h2>
			<div class="dgac-count-badge" id="dgacProcCounter">Cargando procedimientos...</div>
		</div>

		<!-- Barra de Búsqueda y Herramientas -->
		<div class="dgac-toolbar">
			<div class="dgac-search-box">
				<i class="fa-solid fa-magnifying-glass"></i>
				<input type="search" id="dgacProcessSearch" class="dgac-search-input" placeholder="Buscar procedimiento, macroproceso o código (ej. Matrícula, Titulación, Nómina)..." aria-label="Buscar procedimiento">
			</div>
			<div class="dgac-toolbar-actions">
				<button type="button" class="dgac-toggle-all-btn" id="dgacToggleAllBtn">
					<i class="fa-solid fa-up-down"></i> <span id="dgacToggleAllText">Colapsar todos</span>
				</button>
			</div>
		</div>

		<!-- Pestañas Principales de los 4 Subsistemas -->
		<nav class="dgac-subsistema-tabs" role="tablist" aria-label="Subsistemas Universitarios">
			<button type="button" class="dgac-subsistema-tab is-active" role="tab" aria-selected="true" aria-controls="subsys-docencia" id="subtab-docencia" data-subsys="docencia">
				<span class="dgac-subtab-badge"><i class="fa-solid fa-graduation-cap"></i> 01. DOCENCIA</span>
				<strong class="dgac-subtab-title">Formación Académica</strong>
				<span class="dgac-subtab-meta">7 Macroprocesos · 37 Procedimientos</span>
			</button>

			<button type="button" class="dgac-subsistema-tab" role="tab" aria-selected="false" aria-controls="subsys-investigacion" id="subtab-investigacion" data-subsys="investigacion">
				<span class="dgac-subtab-badge"><i class="fa-solid fa-microscope"></i> 02. INVESTIGACIÓN</span>
				<strong class="dgac-subtab-title">Innovación y Saberes</strong>
				<span class="dgac-subtab-meta">3 Macroprocesos · I+D+i</span>
			</button>

			<button type="button" class="dgac-subsistema-tab" role="tab" aria-selected="false" aria-controls="subsys-vinculacion" id="subtab-vinculacion" data-subsys="vinculacion">
				<span class="dgac-subtab-badge"><i class="fa-solid fa-handshake-angle"></i> 03. VINCULACIÓN</span>
				<strong class="dgac-subtab-title">Vinculación con la Sociedad</strong>
				<span class="dgac-subtab-meta">4 Macroprocesos · Pertinencia</span>
			</button>

			<button type="button" class="dgac-subsistema-tab" role="tab" aria-selected="false" aria-controls="subsys-admin" id="subtab-admin" data-subsys="admin">
				<span class="dgac-subtab-badge"><i class="fa-solid fa-building-columns"></i> 04. ADMINISTRATIVO</span>
				<strong class="dgac-subtab-title">Administrativo y Financiero</strong>
				<span class="dgac-subtab-meta">9 Macroprocesos · Talento y Gestión</span>
			</button>
		</nav>


		<!-- ===================================================================
		     SUBSISTEMA 01: DOCENCIA / FORMACIÓN ACADÉMICA
		     =================================================================== -->
		<div id="subsys-docencia" class="dgac-subsistema-content is-active" role="tabpanel" aria-labelledby="subtab-docencia">
			
			<!-- Macroproceso 1: Admisión -->
			<details class="dgac-macro-card dgac-macro-card--blue" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-user-plus"></i></div>
						<span class="dgac-macro-title">Macroproceso: Admisión</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">9 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<!-- Subproceso: Admisión de Estudiantes y Matriculación -->
					<div class="dgac-subproceso-group">
						<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> Admisión de Estudiantes y Matriculación</h4>
						<div class="dgac-proc-list">
							<?php
							$procs_admision = array(
								array( 'title' => 'Homologación de Estudios por Análisis Comparativo de Contenidos', 'code' => 'PCS-DOC-ADM-01' ),
								array( 'title' => 'Reingreso de Estudiantes', 'code' => 'PCS-DOC-ADM-02' ),
								array( 'title' => 'Matrícula Ordinaria y Extraordinaria', 'code' => 'PCS-DOC-ADM-03' ),
								array( 'title' => 'Admisión y Matrícula de Estudiantes a Programas de Estudios de Postgrado', 'code' => 'PCS-DOC-ADM-04' ),
								array( 'title' => 'Validación por Ejercicio Profesional o Experiencia Laboral', 'code' => 'PCS-DOC-ADM-05' ),
								array( 'title' => 'Homologación de Estudios por Validación de Conocimientos de Pregrado y Posgrado', 'code' => 'PCS-DOC-ADM-06' ),
								array( 'title' => 'Admisión de Estudiantes a los Programas de Doctorado', 'code' => 'PCS-DOC-ADM-07' ),
								array( 'title' => 'Homologación de Estudios Realizados en Otros Programas de Doctorado', 'code' => 'PCS-DOC-ADM-08' ),
							);
							foreach ( $procs_admision as $proc ) :
								?>
								<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' admision matricula' ) ); ?>">
									<summary class="dgac-proc-row-summary">
										<div class="dgac-proc-row-left">
											<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
											<div class="dgac-proc-info">
												<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
												<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Versión Vigente</div>
											</div>
										</div>
										<div class="dgac-proc-row-right">
											<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
											<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
										</div>
									</summary>
									<div class="dgac-proc-drawer">
										<div class="dgac-proc-drawer__header"><i class="fa-solid fa-paperclip"></i> Documentos y Formatos Aprobados para este Procedimiento:</div>
										<div class="dgac-files-grid">
											<div class="dgac-file-card">
												<div class="dgac-file-card__left">
													<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
													<div class="dgac-file-card__info">
														<div class="dgac-file-card__name">Manual de Procedimiento Oficial</div>
														<div class="dgac-file-card__meta">PDF Institucional · Aprobado DGAC</div>
													</div>
												</div>
												<div class="dgac-file-card__actions">
													<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual de Procedimiento"><i class="fa-solid fa-eye"></i> Ver</button>
													<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual de Procedimiento"><i class="fa-solid fa-download"></i> Descargar</button>
												</div>
											</div>
											<div class="dgac-file-card">
												<div class="dgac-file-card__left">
													<div class="dgac-file-card__icon dgac-file-card__icon--docx"><i class="fa-solid fa-file-word"></i></div>
													<div class="dgac-file-card__info">
														<div class="dgac-file-card__name">Formatos y Formularios Editables</div>
														<div class="dgac-file-card__meta">DOCX/XLSX · Plantilla Oficial</div>
													</div>
												</div>
												<div class="dgac-file-card__actions">
													<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] . ' (Formatos Oficiales)' ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] . '-FMT' ); ?>" data-doc-type="Formato Editable"><i class="fa-solid fa-download"></i> Descargar</button>
												</div>
											</div>
										</div>
									</div>
								</details>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- Subproceso: Admisión y Nivelación -->
					<div class="dgac-subproceso-group">
						<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> Admisión y Nivelación</h4>
						<div class="dgac-proc-list">
							<details class="dgac-proc-row" data-search-term="admision y nivelacion pcs-doc-adm-09">
								<summary class="dgac-proc-row-summary">
									<div class="dgac-proc-row-left">
										<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
										<div class="dgac-proc-info">
											<div class="dgac-proc-name">Admisión y Nivelación Universitaria</div>
											<div class="dgac-proc-code">Código: PCS-DOC-ADM-09 · Versión Vigente</div>
										</div>
									</div>
									<div class="dgac-proc-row-right">
										<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
										<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
									</div>
								</summary>
								<div class="dgac-proc-drawer">
									<div class="dgac-proc-drawer__header"><i class="fa-solid fa-paperclip"></i> Documentos y Formatos Aprobados:</div>
									<div class="dgac-files-grid">
										<div class="dgac-file-card">
											<div class="dgac-file-card__left">
												<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-file-card__info">
													<div class="dgac-file-card__name">Manual de Admisión y Nivelación</div>
													<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
												</div>
											</div>
											<div class="dgac-file-card__actions">
												<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="Admisión y Nivelación Universitaria" data-doc-code="PCS-DOC-ADM-09" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
												<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="Admisión y Nivelación Universitaria" data-doc-code="PCS-DOC-ADM-09" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
											</div>
										</div>
									</div>
								</div>
							</details>
						</div>
					</div>
				</div>
			</details>

			<!-- Macroproceso 2: Aseguramiento de la Calidad -->
			<details class="dgac-macro-card dgac-macro-card--green" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-award"></i></div>
						<span class="dgac-macro-title">Macroproceso: Aseguramiento de la Calidad</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">6 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-subproceso-group">
						<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> Aseguramiento de la Calidad</h4>
						<div class="dgac-proc-list">
							<?php
							$procs_calidad = array(
								array( 'title' => 'Autoevaluación Institucional', 'code' => 'PCS-DOC-CAL-01' ),
								array( 'title' => 'Autoevaluación de Carreras de Grado', 'code' => 'PCS-DOC-CAL-02' ),
								array( 'title' => 'Autoevaluación de Sedes y Extensiones', 'code' => 'PCS-DOC-CAL-03' ),
								array( 'title' => 'Autoevaluación de Programas de Postgrado', 'code' => 'PCS-DOC-CAL-04' ),
								array( 'title' => 'Evaluación Integral del Desempeño del Personal Académico – EIDPA', 'code' => 'PCS-DOC-CAL-05' ),
								array( 'title' => 'Evaluación de Profesores de los Programas de Maestría', 'code' => 'PCS-DOC-CAL-06' ),
							);
							foreach ( $procs_calidad as $proc ) :
								?>
								<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' calidad autoevaluacion eidpa' ) ); ?>">
									<summary class="dgac-proc-row-summary">
										<div class="dgac-proc-row-left">
											<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
											<div class="dgac-proc-info">
												<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
												<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Versión Vigente</div>
											</div>
										</div>
										<div class="dgac-proc-row-right">
											<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
											<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
										</div>
									</summary>
									<div class="dgac-proc-drawer">
										<div class="dgac-files-grid">
											<div class="dgac-file-card">
												<div class="dgac-file-card__left">
													<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
													<div class="dgac-file-card__info">
														<div class="dgac-file-card__name">Manual de Procedimiento</div>
														<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
													</div>
												</div>
												<div class="dgac-file-card__actions">
													<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
													<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
												</div>
											</div>
										</div>
									</div>
								</details>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</details>

			<!-- Macroproceso 3: Gestión Andragógica y Curricular -->
			<details class="dgac-macro-card dgac-macro-card--red" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-book-open-reader"></i></div>
						<span class="dgac-macro-title">Macroproceso: Gestión Andragógica y Curricular</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">13 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<?php
					$grupos_andragogica = array(
						'Carga Horaria' => array(
							array( 'title' => 'Carga Horaria', 'code' => 'PCS-DOC-CUR-01' ),
							array( 'title' => 'Modificación del Régimen de Dedicación Personal Académico Titular', 'code' => 'PCS-DOC-CUR-02' ),
						),
						'Planificación Académica' => array(
							array( 'title' => 'Sílabo Grado', 'code' => 'PCS-DOC-CUR-03' ),
							array( 'title' => 'Sílabo Postgrado', 'code' => 'PCS-DOC-CUR-04' ),
							array( 'title' => 'Codificación de las Asignaturas', 'code' => 'PCS-DOC-CUR-05' ),
							array( 'title' => 'Presentación de Carreras, Programas y Ajustes Curriculares', 'code' => 'PCS-DOC-CUR-06' ),
							array( 'title' => 'Seguimiento y Actualización Curricular de los Programas de Doctorado', 'code' => 'PCS-DOC-CUR-07' ),
							array( 'title' => 'Gestión de Coordinadores de Postgrado', 'code' => 'PCS-DOC-CUR-08' ),
						),
						'Tutorías' => array(
							array( 'title' => 'Tutorías Académicas', 'code' => 'PCS-DOC-CUR-09' ),
							array( 'title' => 'Seguimiento y Acompañamiento Académico de los Doctorandos', 'code' => 'PCS-DOC-CUR-10' ),
						),
						'Resultados de Aprendizaje' => array(
							array( 'title' => 'Seguimiento y Evaluación de Resultados del Aprendizaje', 'code' => 'PCS-DOC-CUR-11' ),
						),
						'Certificaciones Académicas' => array(
							array( 'title' => 'Emisión y Entrega de Certificados de Promoción', 'code' => 'PCS-DOC-CUR-12' ),
						),
						'Formación de Estudiantes de Grado y Postgrado' => array(
							array( 'title' => 'Actualización de Conocimientos para Titulación de Grado', 'code' => 'PCS-DOC-CUR-13' ),
						),
					);
					foreach ( $grupos_andragogica as $grupo_nombre => $procs ) :
						?>
						<div class="dgac-subproceso-group">
							<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> <?php echo esc_html( $grupo_nombre ); ?></h4>
							<div class="dgac-proc-list">
								<?php foreach ( $procs as $proc ) : ?>
									<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' ' . $grupo_nombre ) ); ?>">
										<summary class="dgac-proc-row-summary">
											<div class="dgac-proc-row-left">
												<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-proc-info">
													<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
													<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
												</div>
											</div>
											<div class="dgac-proc-row-right">
												<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
												<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
											</div>
										</summary>
										<div class="dgac-proc-drawer">
											<div class="dgac-files-grid">
												<div class="dgac-file-card">
													<div class="dgac-file-card__left">
														<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
														<div class="dgac-file-card__info">
															<div class="dgac-file-card__name">Manual / Guía de Procedimiento</div>
															<div class="dgac-file-card__meta">Documento Oficial Aprobado</div>
														</div>
													</div>
													<div class="dgac-file-card__actions">
														<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
														<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
													</div>
												</div>
											</div>
										</div>
									</details>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</details>

			<!-- Macroproceso 4: Gestión de Ambientes de Aprendizaje -->
			<details class="dgac-macro-card dgac-macro-card--dark" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-landmark"></i></div>
						<span class="dgac-macro-title">Macroproceso: Gestión de Ambientes de Aprendizaje</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">7 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<?php
					$grupos_ambientes = array(
						'Gestión Académica Administrativa' => array(
							array( 'title' => 'Portafolio Docente', 'code' => 'PCS-DOC-AMB-01' ),
							array( 'title' => 'Gestión de Recursos Bibliográficos', 'code' => 'PCS-DOC-AMB-02' ),
							array( 'title' => 'Otorgamiento de Aval Académico a Eventos', 'code' => 'PCS-DOC-AMB-03' ),
							array( 'title' => 'Actividades del Área de Procesos Técnicos de la Biblioteca General', 'code' => 'PCS-DOC-AMB-04' ),
						),
						'Ambientes de Aprendizajes' => array(
							array( 'title' => 'Cámara de Gesell', 'code' => 'PCS-DOC-AMB-05' ),
							array( 'title' => 'Laboratorios de Simulación', 'code' => 'PCS-DOC-AMB-06' ),
							array( 'title' => 'Bibliotecas y Salas de Estudio', 'code' => 'PCS-DOC-AMB-07' ),
						),
					);
					foreach ( $grupos_ambientes as $grupo_nombre => $procs ) :
						?>
						<div class="dgac-subproceso-group">
							<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> <?php echo esc_html( $grupo_nombre ); ?></h4>
							<div class="dgac-proc-list">
								<?php foreach ( $procs as $proc ) : ?>
									<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' ' . $grupo_nombre ) ); ?>">
										<summary class="dgac-proc-row-summary">
											<div class="dgac-proc-row-left">
												<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-proc-info">
													<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
													<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
												</div>
											</div>
											<div class="dgac-proc-row-right">
												<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
												<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
											</div>
										</summary>
										<div class="dgac-proc-drawer">
											<div class="dgac-files-grid">
												<div class="dgac-file-card">
													<div class="dgac-file-card__left">
														<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
														<div class="dgac-file-card__info">
															<div class="dgac-file-card__name">Procedimiento de Operación y Normas</div>
															<div class="dgac-file-card__meta">Documento Aprobado DGAC</div>
														</div>
													</div>
													<div class="dgac-file-card__actions">
														<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
														<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
													</div>
												</div>
											</div>
										</div>
									</details>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</details>

			<!-- Macroproceso 5: Graduación -->
			<details class="dgac-macro-card dgac-macro-card--orange" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-user-graduate"></i></div>
						<span class="dgac-macro-title">Macroproceso: Graduación</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">6 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-subproceso-group">
						<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> Titulación</h4>
						<div class="dgac-proc-list">
							<?php
							$procs_graduacion = array(
								array( 'title' => 'Titulación de Estudiantes de Grado (Carreras No Vigentes)', 'code' => 'PCS-DOC-GRA-01' ),
								array( 'title' => 'Emisión y Registro de Título de Grado y Posgrado', 'code' => 'PCS-DOC-GRA-02' ),
								array( 'title' => 'Titulación de Estudiantes de Posgrado', 'code' => 'PCS-DOC-GRA-03' ),
								array( 'title' => 'Titulación de Estudiantes Adscritos al Instituto de Postgrado e Investigación en Ciencias de la Salud (IPICS)', 'code' => 'PCS-DOC-GRA-04' ),
								array( 'title' => 'Titulación de Estudiantes de Grado: Unidad de Integración Curricular y Unidad de Titulación', 'code' => 'PCS-DOC-GRA-05' ),
								array( 'title' => 'Titulación de Estudiantes de las Carreras Técnicas y Tecnológicas', 'code' => 'PCS-DOC-GRA-06' ),
							);
							foreach ( $procs_graduacion as $proc ) :
								?>
								<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' graduacion titulacion grado posgrado' ) ); ?>">
									<summary class="dgac-proc-row-summary">
										<div class="dgac-proc-row-left">
											<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
											<div class="dgac-proc-info">
												<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
												<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
											</div>
										</div>
										<div class="dgac-proc-row-right">
											<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
											<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
										</div>
									</summary>
									<div class="dgac-proc-drawer">
										<div class="dgac-files-grid">
											<div class="dgac-file-card">
												<div class="dgac-file-card__left">
													<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
													<div class="dgac-file-card__info">
														<div class="dgac-file-card__name">Manual de Procedimiento de Titulación</div>
														<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
													</div>
												</div>
												<div class="dgac-file-card__actions">
													<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
													<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
												</div>
											</div>
											<div class="dgac-file-card">
												<div class="dgac-file-card__left">
													<div class="dgac-file-card__icon dgac-file-card__icon--docx"><i class="fa-solid fa-file-word"></i></div>
													<div class="dgac-file-card__info">
														<div class="dgac-file-card__name">Formato de Solicitud y Rúbricas</div>
														<div class="dgac-file-card__meta">DOCX Editable · Plantilla Oficial</div>
													</div>
												</div>
												<div class="dgac-file-card__actions">
													<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] . ' (Formatos Oficiales)' ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] . '-FMT' ); ?>" data-doc-type="Formato Editable"><i class="fa-solid fa-download"></i> Descargar</button>
												</div>
											</div>
										</div>
									</div>
								</details>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</details>

			<!-- Macroproceso 6: Gestión del Personal Académico -->
			<details class="dgac-macro-card dgac-macro-card--blue" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
						<span class="dgac-macro-title">Macroproceso: Gestión del Personal Académico</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">6 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-subproceso-group">
						<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> Desarrollo Docente</h4>
						<div class="dgac-proc-list">
							<?php
							$procs_docente = array(
								array( 'title' => 'Promoción y Estímulos al Personal Académico', 'code' => 'PCS-DOC-DOC-01' ),
								array( 'title' => 'Otorgamiento de Becas y/o Ayudas Económicas para Docentes', 'code' => 'PCS-DOC-DOC-02' ),
								array( 'title' => 'Formación Docente', 'code' => 'PCS-DOC-DOC-03' ),
								array( 'title' => 'Concesión de Periodo Sabático', 'code' => 'PCS-DOC-DOC-04' ),
								array( 'title' => 'Perfeccionamiento Académico', 'code' => 'PCS-DOC-DOC-05' ),
								array( 'title' => 'Movilidad Académica Docente', 'code' => 'PCS-DOC-DOC-06' ),
							);
							foreach ( $procs_docente as $proc ) :
								?>
								<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' docente sabatico becas movilidad' ) ); ?>">
									<summary class="dgac-proc-row-summary">
										<div class="dgac-proc-row-left">
											<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
											<div class="dgac-proc-info">
												<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
												<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
											</div>
										</div>
										<div class="dgac-proc-row-right">
											<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
											<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
										</div>
									</summary>
									<div class="dgac-proc-drawer">
										<div class="dgac-files-grid">
											<div class="dgac-file-card">
												<div class="dgac-file-card__left">
													<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
													<div class="dgac-file-card__info">
														<div class="dgac-file-card__name">Manual de Procedimiento</div>
														<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
													</div>
												</div>
												<div class="dgac-file-card__actions">
													<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
													<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
												</div>
											</div>
										</div>
									</div>
								</details>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</details>

			<!-- Macroproceso 7: Gestión y Desarrollo Estudiantil -->
			<details class="dgac-macro-card dgac-macro-card--purple" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-users"></i></div>
						<span class="dgac-macro-title">Macroproceso: Gestión y Desarrollo Estudiantil</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">6 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<?php
					$grupos_estudiantil = array(
						'Prácticas Preprofesionales y Pasantías' => array(
							array( 'title' => 'Planificación, Ejecución, Supervisión y Evaluación de Prácticas Preprofesionales y Pasantías', 'code' => 'PCS-DOC-EST-01' ),
							array( 'title' => 'Planificación, Ejecución y Supervisión de Prácticas de Cuarto Nivel y/o Asistenciales', 'code' => 'PCS-DOC-EST-02' ),
						),
						'Ayudantía de Cátedra e Investigación' => array(
							array( 'title' => 'Ayudantía de Cátedra', 'code' => 'PCS-DOC-EST-03' ),
						),
						'Movilidad Académica / Convenios' => array(
							array( 'title' => 'Celebración, Seguimiento y Evaluación de Acuerdos y/o Convenios de Cooperación Internacional', 'code' => 'PCS-DOC-EST-04' ),
							array( 'title' => 'Movilidad Académica Estudiantil', 'code' => 'PCS-DOC-EST-05' ),
						),
						'Seguimiento a Graduados' => array(
							array( 'title' => 'Seguimiento y Vinculación con Graduados e Inserción Laboral', 'code' => 'PCS-DOC-EST-06' ),
						),
					);
					foreach ( $grupos_estudiantil as $grupo_nombre => $procs ) :
						?>
						<div class="dgac-subproceso-group">
							<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> <?php echo esc_html( $grupo_nombre ); ?></h4>
							<div class="dgac-proc-list">
								<?php foreach ( $procs as $proc ) : ?>
									<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' ' . $grupo_nombre ) ); ?>">
										<summary class="dgac-proc-row-summary">
											<div class="dgac-proc-row-left">
												<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-proc-info">
													<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
													<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
												</div>
											</div>
											<div class="dgac-proc-row-right">
												<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
												<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
											</div>
										</summary>
										<div class="dgac-proc-drawer">
											<div class="dgac-files-grid">
												<div class="dgac-file-card">
													<div class="dgac-file-card__left">
														<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
														<div class="dgac-file-card__info">
															<div class="dgac-file-card__name">Procedimiento de Gestión Estudiantil</div>
															<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
														</div>
													</div>
													<div class="dgac-file-card__actions">
														<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
														<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
													</div>
												</div>
											</div>
										</div>
									</details>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</details>

		</div><!-- Fin Subsys Docencia -->


		<!-- ===================================================================
		     SUBSISTEMA 02: INVESTIGACIÓN
		     =================================================================== -->
		<div id="subsys-investigacion" class="dgac-subsistema-content" role="tabpanel" aria-labelledby="subtab-investigacion">
			
			<!-- Macroproceso 1: Gestión del Conocimiento, Innovación y Transferencia Tecnológica -->
			<details class="dgac-macro-card dgac-macro-card--green" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-lightbulb"></i></div>
						<span class="dgac-macro-title">Macroproceso: Gestión del Conocimiento, Innovación y Transferencia Tecnológica</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">4 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-proc-list">
						<?php
						$procs_inv_1 = array(
							array( 'title' => 'Propiedad Intelectual, Patentes y Registro de Invenciones', 'code' => 'PCS-INV-INN-01' ),
							array( 'title' => 'Transferencia Tecnológica y Vinculación con Sectores Productivos', 'code' => 'PCS-INV-INN-02' ),
							array( 'title' => 'Gestión de Semilleros y Grupos de Investigación Institucionales', 'code' => 'PCS-INV-INN-03' ),
							array( 'title' => 'Fondos Concursables Internos para Proyectos de Investigación', 'code' => 'PCS-INV-INN-04' ),
						);
						foreach ( $procs_inv_1 as $proc ) :
							?>
							<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' investigacion transferencia innovacion' ) ); ?>">
								<summary class="dgac-proc-row-summary">
									<div class="dgac-proc-row-left">
										<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
										<div class="dgac-proc-info">
											<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
											<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
										</div>
									</div>
									<div class="dgac-proc-row-right">
										<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
										<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
									</div>
								</summary>
								<div class="dgac-proc-drawer">
									<div class="dgac-files-grid">
										<div class="dgac-file-card">
											<div class="dgac-file-card__left">
												<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-file-card__info">
													<div class="dgac-file-card__name">Manual del Procedimiento de Investigación</div>
													<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
												</div>
											</div>
											<div class="dgac-file-card__actions">
												<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
												<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
											</div>
										</div>
									</div>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</details>

			<!-- Macroproceso 2: Redes del Conocimiento, Investigación e Innovación -->
			<details class="dgac-macro-card dgac-macro-card--blue" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-network-wired"></i></div>
						<span class="dgac-macro-title">Macroproceso: Redes del Conocimiento, Investigación e Innovación</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">3 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-proc-list">
						<?php
						$procs_inv_2 = array(
							array( 'title' => 'Gestión y Publicación en Revistas Científicas Indexadas ULEAM', 'code' => 'PCS-INV-RED-01' ),
							array( 'title' => 'Afiliación y Participación en Redes Académico-Científicas Internacionales', 'code' => 'PCS-INV-RED-02' ),
							array( 'title' => 'Organización y Aval Científico de Congresos y Jornadas de Investigación', 'code' => 'PCS-INV-RED-03' ),
						);
						foreach ( $procs_inv_2 as $proc ) :
							?>
							<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' redes revistas cientificas congresos' ) ); ?>">
								<summary class="dgac-proc-row-summary">
									<div class="dgac-proc-row-left">
										<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
										<div class="dgac-proc-info">
											<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
											<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
										</div>
									</div>
									<div class="dgac-proc-row-right">
										<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
										<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
									</div>
								</summary>
								<div class="dgac-proc-drawer">
									<div class="dgac-files-grid">
										<div class="dgac-file-card">
											<div class="dgac-file-card__left">
												<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-file-card__info">
													<div class="dgac-file-card__name">Procedimiento de Publicaciones y Redes</div>
													<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
												</div>
											</div>
											<div class="dgac-file-card__actions">
												<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
												<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
											</div>
										</div>
									</div>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</details>

			<!-- Macroproceso 3: Generación del Conocimiento y Saberes -->
			<details class="dgac-macro-card dgac-macro-card--purple" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-brain"></i></div>
						<span class="dgac-macro-title">Macroproceso: Generación del Conocimiento y Saberes</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">3 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-proc-list">
						<?php
						$procs_inv_3 = array(
							array( 'title' => 'Formulación, Aprobación y Monitoreo de Proyectos I+D+i', 'code' => 'PCS-INV-GEN-01' ),
							array( 'title' => 'Evaluación Ética y Aprobación por Comité de Bioética (CEISH)', 'code' => 'PCS-INV-GEN-02' ),
							array( 'title' => 'Catalogación, Publicación y Financiamiento de Libros y Capítulos Científicos', 'code' => 'PCS-INV-GEN-03' ),
						);
						foreach ( $procs_inv_3 as $proc ) :
							?>
							<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' ceish bioetica proyectos libros' ) ); ?>">
								<summary class="dgac-proc-row-summary">
									<div class="dgac-proc-row-left">
										<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
										<div class="dgac-proc-info">
											<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
											<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
										</div>
									</div>
									<div class="dgac-proc-row-right">
										<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
										<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
									</div>
								</summary>
								<div class="dgac-proc-drawer">
									<div class="dgac-files-grid">
										<div class="dgac-file-card">
											<div class="dgac-file-card__left">
												<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-file-card__info">
													<div class="dgac-file-card__name">Manual de Proyectos y Bioética CEISH</div>
													<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
												</div>
											</div>
											<div class="dgac-file-card__actions">
												<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
												<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
											</div>
										</div>
									</div>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</details>

		</div><!-- Fin Subsys Investigación -->


		<!-- ===================================================================
		     SUBSISTEMA 03: VINCULACIÓN CON LA SOCIEDAD
		     =================================================================== -->
		<div id="subsys-vinculacion" class="dgac-subsistema-content" role="tabpanel" aria-labelledby="subtab-vinculacion">
			
			<!-- Macroproceso 1: Gestión del Conocimiento -->
			<details class="dgac-macro-card dgac-macro-card--purple" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-people-roof"></i></div>
						<span class="dgac-macro-title">Macroproceso: Gestión del Conocimiento</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">3 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-proc-list">
						<?php
						$procs_vinc_1 = array(
							array( 'title' => 'Diagnóstico de Necesidades Sociales y Pertinencia de Programas Comunitarios', 'code' => 'PCS-VINC-GC-01' ),
							array( 'title' => 'Formulación, Aprobación, Ejecución y Monitoreo de Proyectos de Vinculación', 'code' => 'PCS-VINC-GC-02' ),
							array( 'title' => 'Evaluación de Impacto Social y Sistematización de Resultados Comunitarios', 'code' => 'PCS-VINC-GC-03' ),
						);
						foreach ( $procs_vinc_1 as $proc ) :
							?>
							<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' vinculacion pertinencia proyectos sociales' ) ); ?>">
								<summary class="dgac-proc-row-summary">
									<div class="dgac-proc-row-left">
										<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
										<div class="dgac-proc-info">
											<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
											<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
										</div>
									</div>
									<div class="dgac-proc-row-right">
										<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
										<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
									</div>
								</summary>
								<div class="dgac-proc-drawer">
									<div class="dgac-files-grid">
										<div class="dgac-file-card">
											<div class="dgac-file-card__left">
												<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-file-card__info">
													<div class="dgac-file-card__name">Manual de Vinculación con la Sociedad</div>
													<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
												</div>
											</div>
											<div class="dgac-file-card__actions">
												<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
												<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
											</div>
										</div>
									</div>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</details>

			<!-- Macroproceso 2: Educación Continua -->
			<details class="dgac-macro-card dgac-macro-card--orange" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-chalkboard"></i></div>
						<span class="dgac-macro-title">Macroproceso: Educación Continua</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">3 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-proc-list">
						<?php
						$procs_vinc_2 = array(
							array( 'title' => 'Planificación, Diseño y Aprobación de Cursos, Talleres y Diplomados', 'code' => 'PCS-VINC-EC-01' ),
							array( 'title' => 'Ejecución y Control Académico de Programas de Educación Continua', 'code' => 'PCS-VINC-EC-02' ),
							array( 'title' => 'Certificación y Acreditación de Competencias Laborales y Cursos de Capacitación', 'code' => 'PCS-VINC-EC-03' ),
						);
						foreach ( $procs_vinc_2 as $proc ) :
							?>
							<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' educacion continua cursos talleres certificacion' ) ); ?>">
								<summary class="dgac-proc-row-summary">
									<div class="dgac-proc-row-left">
										<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
										<div class="dgac-proc-info">
											<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
											<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
										</div>
									</div>
									<div class="dgac-proc-row-right">
										<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
										<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
									</div>
								</summary>
								<div class="dgac-proc-drawer">
									<div class="dgac-files-grid">
										<div class="dgac-file-card">
											<div class="dgac-file-card__left">
												<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-file-card__info">
													<div class="dgac-file-card__name">Procedimiento de Educación Continua</div>
													<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
												</div>
											</div>
											<div class="dgac-file-card__actions">
												<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
												<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
											</div>
										</div>
									</div>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</details>

			<!-- Macroproceso 3: Cooperación, Desarrollo y Emprendimiento -->
			<details class="dgac-macro-card dgac-macro-card--blue" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-rocket"></i></div>
						<span class="dgac-macro-title">Macroproceso: Cooperación, Desarrollo y Emprendimiento</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">3 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-proc-list">
						<?php
						$procs_vinc_3 = array(
							array( 'title' => 'Incubación de Emprendimientos Comunitarios y Asistencia a PyMES', 'code' => 'PCS-VINC-COOP-01' ),
							array( 'title' => 'Alianzas Estratégicas y Cooperación Técnica con Sectores Productivos y GADs', 'code' => 'PCS-VINC-COOP-02' ),
							array( 'title' => 'Voluntariado Universitario y Programas de Responsabilidad Social Institucional', 'code' => 'PCS-VINC-COOP-03' ),
						);
						foreach ( $procs_vinc_3 as $proc ) :
							?>
							<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' emprendimiento cooperacion alianzas pymes gads' ) ); ?>">
								<summary class="dgac-proc-row-summary">
									<div class="dgac-proc-row-left">
										<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
										<div class="dgac-proc-info">
											<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
											<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
										</div>
									</div>
									<div class="dgac-proc-row-right">
										<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
										<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
									</div>
								</summary>
								<div class="dgac-proc-drawer">
									<div class="dgac-files-grid">
										<div class="dgac-file-card">
											<div class="dgac-file-card__left">
												<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-file-card__info">
													<div class="dgac-file-card__name">Procedimiento de Emprendimiento y Cooperación</div>
													<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
												</div>
											</div>
											<div class="dgac-file-card__actions">
												<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
												<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
											</div>
										</div>
									</div>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</details>

			<!-- Macroproceso 4: Redes -->
			<details class="dgac-macro-card dgac-macro-card--red" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-circle-nodes"></i></div>
						<span class="dgac-macro-title">Macroproceso: Redes</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">2 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<div class="dgac-proc-list">
						<?php
						$procs_vinc_4 = array(
							array( 'title' => 'Gestión, Integración y Operación en Redes Interinstitucionales de Vinculación', 'code' => 'PCS-VINC-RED-01' ),
							array( 'title' => 'Seguimiento, Control y Evaluación del Cumplimiento de Convenios de Vinculación', 'code' => 'PCS-VINC-RED-02' ),
						);
						foreach ( $procs_vinc_4 as $proc ) :
							?>
							<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' redes convenios vinculacion' ) ); ?>">
								<summary class="dgac-proc-row-summary">
									<div class="dgac-proc-row-left">
										<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
										<div class="dgac-proc-info">
											<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
											<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
										</div>
									</div>
									<div class="dgac-proc-row-right">
										<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
										<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
									</div>
								</summary>
								<div class="dgac-proc-drawer">
									<div class="dgac-files-grid">
										<div class="dgac-file-card">
											<div class="dgac-file-card__left">
												<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-file-card__info">
													<div class="dgac-file-card__name">Procedimiento de Redes de Vinculación</div>
													<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
												</div>
											</div>
											<div class="dgac-file-card__actions">
												<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
												<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
											</div>
										</div>
									</div>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</details>

		</div><!-- Fin Subsys Vinculación -->


		<!-- ===================================================================
		     SUBSISTEMA 04: ADMINISTRATIVO Y FINANCIERO
		     =================================================================== -->
		<div id="subsys-admin" class="dgac-subsistema-content" role="tabpanel" aria-labelledby="subtab-admin">
			
			<!-- Macroproceso 1: Gestión Administrativa del Talento Humano -->
			<details class="dgac-macro-card dgac-macro-card--green" open>
				<summary class="dgac-macro-summary">
					<div class="dgac-macro-summary__left">
						<div class="dgac-macro-icon"><i class="fa-solid fa-id-card-clip"></i></div>
						<span class="dgac-macro-title">Macroproceso: Gestión Administrativa del Talento Humano</span>
					</div>
					<div class="dgac-macro-summary__right">
						<span class="dgac-macro-count">28 Procedimientos</span>
						<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
					</div>
				</summary>
				<div class="dgac-macro-body">
					<?php
					$grupos_talento = array(
						'Captación del Talento Humano' => array(
							array( 'title' => 'Planificación del Talento Humano, Seguimiento y Cumplimiento', 'code' => 'PCS-TH-CAP-01' ),
							array( 'title' => 'Reclutamiento y Selección de Personal Administrativo - LOSEP', 'code' => 'PCS-TH-CAP-02' ),
							array( 'title' => 'Contratación Ocasional de Personal Administrativo y Asesores', 'code' => 'PCS-TH-CAP-03' ),
							array( 'title' => 'Inducción del Talento Humano', 'code' => 'PCS-TH-CAP-04' ),
							array( 'title' => 'Contratación de Docentes para Postgrado', 'code' => 'PCS-TH-CAP-05' ),
							array( 'title' => 'Ingreso de Docentes Titulares', 'code' => 'PCS-TH-CAP-06' ),
							array( 'title' => 'Selección y Designación de Decanos y Subdecanos', 'code' => 'PCS-TH-CAP-07' ),
							array( 'title' => 'Selección y Designación de Directores Institucionales', 'code' => 'PCS-TH-CAP-08' ),
							array( 'title' => 'Selección de Miembros del CEISH', 'code' => 'PCS-TH-CAP-09' ),
							array( 'title' => 'Contratación de Profesores de Programas de Doctorado bajo la Modalidad de Servicios Profesionales', 'code' => 'PCS-TH-CAP-10' ),
							array( 'title' => 'Contratación Civil y Pago de Personal Académico (Servicios Profesionales)', 'code' => 'PCS-TH-CAP-11' ),
							array( 'title' => 'Contratación de Docentes Ocasionales', 'code' => 'PCS-TH-CAP-12' ),
							array( 'title' => 'Consulta y Registro de Impedimento Laboral', 'code' => 'PCS-TH-CAP-13' ),
						),
						'Mantenimiento del Talento Humano' => array(
							array( 'title' => 'Nómina', 'code' => 'PCS-TH-MAN-01' ),
							array( 'title' => 'Bienestar Social', 'code' => 'PCS-TH-MAN-02' ),
							array( 'title' => 'Viáticos, Subsistencia, y Movilización', 'code' => 'PCS-TH-MAN-03' ),
							array( 'title' => 'Licencia para Formación y Capacitación Personal Académico Titular', 'code' => 'PCS-TH-MAN-04' ),
							array( 'title' => 'Financiamiento para Capacitación y Perfeccionamiento del Personal Administrativo', 'code' => 'PCS-TH-MAN-05' ),
							array( 'title' => 'Movimiento de Personal', 'code' => 'PCS-TH-MAN-06' ),
							array( 'title' => 'Asistencia y Permanencia del Talento Humano', 'code' => 'PCS-TH-MAN-07' ),
							array( 'title' => 'Vacaciones', 'code' => 'PCS-TH-MAN-08' ),
						),
						'Desarrollo del Talento Humano' => array(
							array( 'title' => 'Capacitación y Entrenamiento', 'code' => 'PCS-TH-DES-01' ),
							array( 'title' => 'Evaluación de Desempeño', 'code' => 'PCS-TH-DES-02' ),
						),
						'Desvinculación del Talento Humano' => array(
							array( 'title' => 'Desvinculación de Servidor por Acogerse a Compensación de Retiro por Jubilación', 'code' => 'PCS-TH-DESV-01' ),
							array( 'title' => 'Desvinculación de Servidor por Renuncia Voluntaria', 'code' => 'PCS-TH-DESV-02' ),
							array( 'title' => 'Cesación de Funciones por Supresión de Puestos', 'code' => 'PCS-TH-DESV-03' ),
							array( 'title' => 'Planificación de Proyectos para el Pago de Compensación por Jubilación o Renuncia', 'code' => 'PCS-TH-DESV-04' ),
							array( 'title' => 'Liquidación de Haberes', 'code' => 'PCS-TH-DESV-05' ),
						),
					);
					foreach ( $grupos_talento as $grupo_nombre => $procs ) :
						?>
						<div class="dgac-subproceso-group">
							<h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> <?php echo esc_html( $grupo_nombre ); ?></h4>
							<div class="dgac-proc-list">
								<?php foreach ( $procs as $proc ) : ?>
									<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc['title'] . ' ' . $proc['code'] . ' ' . $grupo_nombre . ' talento humano losep nomina vacaciones' ) ); ?>">
										<summary class="dgac-proc-row-summary">
											<div class="dgac-proc-row-left">
												<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
												<div class="dgac-proc-info">
													<div class="dgac-proc-name"><?php echo esc_html( $proc['title'] ); ?></div>
													<div class="dgac-proc-code">Código: <?php echo esc_html( $proc['code'] ); ?> · Vigente</div>
												</div>
											</div>
											<div class="dgac-proc-row-right">
												<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
												<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
											</div>
										</summary>
										<div class="dgac-proc-drawer">
											<div class="dgac-files-grid">
												<div class="dgac-file-card">
													<div class="dgac-file-card__left">
														<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
														<div class="dgac-file-card__info">
															<div class="dgac-file-card__name">Manual del Procedimiento de Talento Humano</div>
															<div class="dgac-file-card__meta">PDF Oficial · DGAC ULEAM</div>
														</div>
													</div>
													<div class="dgac-file-card__actions">
														<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
														<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
													</div>
												</div>
												<div class="dgac-file-card">
													<div class="dgac-file-card__left">
														<div class="dgac-file-card__icon dgac-file-card__icon--docx"><i class="fa-solid fa-file-word"></i></div>
														<div class="dgac-file-card__info">
															<div class="dgac-file-card__name">Formato Editable Oficial</div>
															<div class="dgac-file-card__meta">DOCX/XLSX · Plantilla Institucional</div>
														</div>
													</div>
													<div class="dgac-file-card__actions">
														<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc['title'] . ' (Formatos Oficiales)' ); ?>" data-doc-code="<?php echo esc_attr( $proc['code'] . '-FMT' ); ?>" data-doc-type="Formato"><i class="fa-solid fa-download"></i> Descargar</button>
													</div>
												</div>
											</div>
										</div>
									</details>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</details>

			<!-- Otros Macroprocesos Administrativos -->
			<?php
			$otros_macroprocesos = array(
				array(
					'title' => 'Gestión Jurídica y Mediación de Conflictos',
					'icon' => 'fa-scale-balanced',
					'color' => 'blue',
					'code_prefix' => 'PCS-JUR',
					'procs' => array(
						'Asesoría y Dictámenes Jurídicos Institucionales',
						'Mediación y Resolución Alternativa de Conflictos',
						'Patrocinio Judicial y Representación Institucional',
						'Elaboración, Revisión y Control de Convenios y Contratos',
					),
				),
				array(
					'title' => 'Gestión de Mantenimiento y Construcción',
					'icon' => 'fa-trowel-bricks',
					'color' => 'orange',
					'code_prefix' => 'PCS-MNT',
					'procs' => array(
						'Planificación y Fiscalización de Obras e Infraestructura',
						'Mantenimiento Preventivo y Correctivo de Instalaciones Físicas',
						'Operación y Mantenimiento de Redes Eléctricas, Climatización e Hidrosanitarias',
					),
				),
				array(
					'title' => 'Gestión de Servicios Administrativos',
					'icon' => 'fa-boxes-packing',
					'color' => 'dark',
					'code_prefix' => 'PCS-ADM',
					'procs' => array(
						'Administración y Operación del Parque Automotor y Movilización',
						'Recepción, Almacenamiento y Custodia de Bienes e Inventarios',
						'Servicios Generales, Intendencia y Mantenimiento de Áreas',
						'Gestión Documental, Archivo General y Correspondencia',
					),
				),
				array(
					'title' => 'Gestión Financiera',
					'icon' => 'fa-calculator',
					'color' => 'green',
					'code_prefix' => 'PCS-FIN',
					'procs' => array(
						'Formulación, Modificación y Cierre del Presupuesto Institucional',
						'Contabilidad Gubernamental y Control Previo al Devengo',
						'Tesorería, Transferencias y Pagos a Proveedores y Servidores',
						'Facturación, Recaudación y Cobranzas Universitarias',
					),
				),
				array(
					'title' => 'Gestión de Comunicación',
					'icon' => 'fa-bullhorn',
					'color' => 'red',
					'code_prefix' => 'PCS-COM',
					'procs' => array(
						'Prensa, Boletines Informativos y Relaciones Públicas',
						'Diseño Gráfico, Imagen Corporativa y Producción Multimedia',
						'Gestión de Redes Sociales y Plataformas Informativas',
					),
				),
				array(
					'title' => 'Gestión de la Información y Sistemas Informáticos',
					'icon' => 'fa-server',
					'color' => 'blue',
					'code_prefix' => 'PCS-TIC',
					'procs' => array(
						'Desarrollo y Mantenimiento de Sistemas Académicos y Administrativos (SGA)',
						'Soporte Técnico a Usuarios y Mantenimiento de Equipamiento TIC',
						'Administración de Servidores, Infraestructura de Red y Ciberseguridad',
					),
				),
				array(
					'title' => 'Gestión Estratégica',
					'icon' => 'fa-chart-line',
					'color' => 'purple',
					'code_prefix' => 'PCS-ESTR',
					'procs' => array(
						'Plan Estratégico de Desarrollo Institucional (PEDI)',
						'Plan Operativo Anual (POA) y Monitoreo del Cumplimiento de Metas',
						'Gestión de Riesgos Institucionales y Continuidad Operativa',
					),
				),
				array(
					'title' => 'Gestión de la Calidad',
					'icon' => 'fa-certificate',
					'color' => 'green',
					'code_prefix' => 'PCS-CAL',
					'procs' => array(
						'Normalización, Estandarización y Aprobación de Procedimientos Institucionales',
						'Auditorías Internas al Sistema de Gestión de la Calidad (SGC)',
						'Seguimiento a Planes de Mejora y No Conformidades',
					),
				),
			);
			foreach ( $otros_macroprocesos as $macro ) :
				?>
				<details class="dgac-macro-card dgac-macro-card--<?php echo esc_attr( $macro['color'] ); ?>" open>
					<summary class="dgac-macro-summary">
						<div class="dgac-macro-summary__left">
							<div class="dgac-macro-icon"><i class="fa-solid <?php echo esc_attr( $macro['icon'] ); ?>"></i></div>
							<span class="dgac-macro-title">Macroproceso: <?php echo esc_html( $macro['title'] ); ?></span>
						</div>
						<div class="dgac-macro-summary__right">
							<span class="dgac-macro-count"><?php echo count( $macro['procs'] ); ?> Procedimientos</span>
							<span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span>
						</div>
					</summary>
					<div class="dgac-macro-body">
						<div class="dgac-proc-list">
							<?php
							$i = 1;
							foreach ( $macro['procs'] as $proc_title ) :
								$proc_code = sprintf( '%s-%02d', $macro['code_prefix'], $i );
								$i++;
								?>
								<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( strtolower( $proc_title . ' ' . $proc_code . ' ' . $macro['title'] ) ); ?>">
									<summary class="dgac-proc-row-summary">
										<div class="dgac-proc-row-left">
											<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
											<div class="dgac-proc-info">
												<div class="dgac-proc-name"><?php echo esc_html( $proc_title ); ?></div>
												<div class="dgac-proc-code">Código: <?php echo esc_html( $proc_code ); ?> · Vigente</div>
											</div>
										</div>
										<div class="dgac-proc-row-right">
											<span class="dgac-proc-badge-status"><i class="fa-solid fa-circle-check"></i> Vigente</span>
											<span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> Ver Archivos <i class="fa-solid fa-chevron-down"></i></span>
										</div>
									</summary>
									<div class="dgac-proc-drawer">
										<div class="dgac-files-grid">
											<div class="dgac-file-card">
												<div class="dgac-file-card__left">
													<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
													<div class="dgac-file-card__info">
														<div class="dgac-file-card__name">Manual de Procedimiento Oficial</div>
														<div class="dgac-file-card__meta">PDF Institucional · DGAC ULEAM</div>
													</div>
												</div>
												<div class="dgac-file-card__actions">
													<button type="button" class="dgac-file-btn dgac-file-btn--view dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc_title ); ?>" data-doc-code="<?php echo esc_attr( $proc_code ); ?>" data-doc-type="Manual"><i class="fa-solid fa-eye"></i> Ver</button>
													<button type="button" class="dgac-file-btn dgac-file-btn--download dgac-trigger-doc" data-doc-title="<?php echo esc_attr( $proc_title ); ?>" data-doc-code="<?php echo esc_attr( $proc_code ); ?>" data-doc-type="Manual"><i class="fa-solid fa-download"></i> Descargar</button>
												</div>
											</div>
										</div>
									</div>
								</details>
							<?php endforeach; ?>
						</div>
					</div>
				</details>
			<?php endforeach; ?>

		</div><!-- Fin Subsys Administrativo -->

	</section><!-- Fin Subsistemas Section -->

</div><!-- Fin entry-content -->


<!-- Modal Informativo Institucional para Archivos y Catálogos -->
<div class="dgac-modal-overlay" id="dgacDocModal" role="dialog" aria-modal="true" aria-labelledby="modalDocTitle">
	<div class="dgac-modal">
		<div class="dgac-modal__header">
			<h4><i class="fa-solid fa-folder-open"></i> <span id="modalDocHeader">Documento Institucional</span></h4>
			<button type="button" class="dgac-modal__close" id="modalCloseBtn" aria-label="Cerrar modal">&times;</button>
		</div>
		<div class="dgac-modal__body">
			<div class="dgac-modal__icon"><i class="fa-solid fa-file-shield"></i></div>
			<h3 class="dgac-modal__title" id="modalDocTitle">Título del Documento</h3>
			<p class="dgac-modal__desc">
				Código de referencia: <strong id="modalDocCode">PCS-001</strong><br>
				Tipo: <span id="modalDocType">Manual Oficial</span><br><br>
				<em>Este documento se encuentra en proceso de digitalización y enlace directo por la Dirección de Gestión y Aseguramiento de la Calidad (DGAC) para su descarga en línea.</em>
			</p>
		</div>
		<div class="dgac-modal__footer">
			<button type="button" class="dgac-btn-close-modal" id="modalAcceptBtn">Entendido</button>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// 1. Manejo de Pestañas Misión / Objetivo / Productos
	const pillTabs = document.querySelectorAll('.dgac-pill-tab');
	const overviewPanels = document.querySelectorAll('.dgac-overview-panel');

	pillTabs.forEach(tab => {
		tab.addEventListener('click', function() {
			const target = this.getAttribute('data-tab');
			pillTabs.forEach(t => {
				t.classList.remove('is-active');
				t.setAttribute('aria-selected', 'false');
			});
			overviewPanels.forEach(p => p.classList.remove('is-active'));

			this.classList.add('is-active');
			this.setAttribute('aria-selected', 'true');
			const activePanel = document.getElementById('panel-' + target);
			if (activePanel) {
				activePanel.classList.add('is-active');
			}
		});
	});

	// 2. Manejo de Pestañas de los 4 Subsistemas
	const subsysTabs = document.querySelectorAll('.dgac-subsistema-tab');
	const subsysPanels = document.querySelectorAll('.dgac-subsistema-content');

	subsysTabs.forEach(tab => {
		tab.addEventListener('click', function() {
			const target = this.getAttribute('data-subsys');
			subsysTabs.forEach(t => {
				t.classList.remove('is-active');
				t.setAttribute('aria-selected', 'false');
			});
			subsysPanels.forEach(p => p.classList.remove('is-active'));

			this.classList.add('is-active');
			this.setAttribute('aria-selected', 'true');
			const activePanel = document.getElementById('subsys-' + target);
			if (activePanel) {
				activePanel.classList.add('is-active');
			}
			updateCounter();
		});
	});

	// 3. Conteo dinámico y buscador en vivo
	const searchInput = document.getElementById('dgacProcessSearch');
	const procRows = document.querySelectorAll('.dgac-proc-row');
	const counter = document.getElementById('dgacProcCounter');

	function updateCounter() {
		const activeSubsys = document.querySelector('.dgac-subsistema-content.is-active');
		if (!activeSubsys) return;
		const visibleRows = activeSubsys.querySelectorAll('.dgac-proc-row:not([style*="display: none"])');
		const totalActiveRows = activeSubsys.querySelectorAll('.dgac-proc-row');
		if (searchInput && searchInput.value.trim() !== '') {
			counter.textContent = `${visibleRows.length} procedimiento(s) encontrado(s)`;
		} else {
			counter.textContent = `${totalActiveRows.length} procedimientos en este subsistema`;
		}
	}
	updateCounter();

	if (searchInput) {
		searchInput.addEventListener('input', function() {
			const query = this.value.toLowerCase().trim();
			const activeSubsys = document.querySelector('.dgac-subsistema-content.is-active');
			if (!activeSubsys) return;

			const rows = activeSubsys.querySelectorAll('.dgac-proc-row');
			const macroCards = activeSubsys.querySelectorAll('.dgac-macro-card');

			rows.forEach(row => {
				const text = row.getAttribute('data-search-term') || row.textContent.toLowerCase();
				if (query === '' || text.includes(query)) {
					row.style.display = '';
					if (query !== '') {
						row.open = true;
					}
				} else {
					row.style.display = 'none';
				}
			});

			macroCards.forEach(card => {
				const hasVisibleRows = Array.from(card.querySelectorAll('.dgac-proc-row')).some(r => r.style.display !== 'none');
				card.style.display = hasVisibleRows ? '' : 'none';
				if (query !== '' && hasVisibleRows) {
					card.open = true;
				}
			});

			updateCounter();
		});
	}

	// 4. Botón Expandir / Colapsar todos
	const toggleBtn = document.getElementById('dgacToggleAllBtn');
	const toggleText = document.getElementById('dgacToggleAllText');
	let allExpanded = true;

	if (toggleBtn) {
		toggleBtn.addEventListener('click', function() {
			const activeSubsys = document.querySelector('.dgac-subsistema-content.is-active');
			if (!activeSubsys) return;
			const macroCards = activeSubsys.querySelectorAll('.dgac-macro-card');
			const procRows = activeSubsys.querySelectorAll('.dgac-proc-row');

			allExpanded = !allExpanded;
			macroCards.forEach(card => card.open = allExpanded);
			procRows.forEach(row => row.open = allExpanded);

			toggleText.textContent = allExpanded ? 'Colapsar todos' : 'Expandir todos';
		});
	}

	// 5. Modal amigable para avisos de descarga / consulta de archivos
	const modalOverlay = document.getElementById('dgacDocModal');
	const modalDocTitle = document.getElementById('modalDocTitle');
	const modalDocCode = document.getElementById('modalDocCode');
	const modalDocType = document.getElementById('modalDocType');
	const modalCloseBtn = document.getElementById('modalCloseBtn');
	const modalAcceptBtn = document.getElementById('modalAcceptBtn');

	function openModal(title, code, type) {
		if (!modalOverlay) return;
		modalDocTitle.textContent = title || 'Documento Oficial';
		modalDocCode.textContent = code || 'PCS-00';
		modalDocType.textContent = type || 'Manual de Procedimiento';
		modalOverlay.classList.add('is-active');
		document.body.style.overflow = 'hidden';
	}

	function closeModal() {
		if (!modalOverlay) return;
		modalOverlay.classList.remove('is-active');
		document.body.style.overflow = '';
	}

	document.querySelectorAll('.dgac-trigger-doc').forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			const title = this.getAttribute('data-doc-title') || this.closest('.dgac-proc-row')?.querySelector('.dgac-proc-name')?.textContent;
			const code = this.getAttribute('data-doc-code') || 'PCS-2026';
			const type = this.getAttribute('data-doc-type') || 'Documento / Formato';
			openModal(title, code, type);
		});
	});

	if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
	if (modalAcceptBtn) modalAcceptBtn.addEventListener('click', closeModal);
	if (modalOverlay) {
		modalOverlay.addEventListener('click', function(e) {
			if (e.target === modalOverlay) closeModal();
		});
	}
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('is-active')) {
			closeModal();
		}
	});
});
</script>

<?php
get_footer();
