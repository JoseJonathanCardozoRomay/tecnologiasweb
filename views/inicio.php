<?php

require_once __DIR__ . '/../includes/sesion.php';

$usuarioSesion = obtenerUsuarioSesion();

?>

<main class="flex-grow-1">
    <!-- Presentación institucional del sistema -->
    <section class="intro-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <p class="section-label">
                        Universidad Privada Domingo Savio
                    </p>

                    <h1>
                        Acompañamiento académico para avanzar con confianza.
                    </h1>

                    <p class="intro-text">
                        El Sistema Académico de Tutorías conecta a estudiantes
                        y tutores de la UPDS para organizar solicitudes,
                        horarios y seguimiento en un solo espacio.
                    </p>

                    <div class="intro-actions">
                        <?php if ($usuarioSesion): ?>
                            <a
                                class="primary-link"
                                href="#funcionamiento"
                            >
                                Conocer el proceso
                            </a>
                        <?php else: ?>
                            <a
                                class="primary-link"
                                href="controllers/login.php"
                            >
                                Ingresar al sistema
                            </a>
                        <?php endif; ?>

                        <span class="institution-note">
                            Acceso para la comunidad universitaria UPDS
                        </span>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div
                        class="process-summary"
                        id="funcionamiento"
                    >
                        <p class="summary-title">
                            Ruta de acompañamiento
                        </p>

                        <div class="process-row">
                            <span>01</span>

                            <div>
                                <strong>Encontrar apoyo</strong>

                                <p>
                                    El estudiante selecciona la materia y
                                    consulta los tutores disponibles.
                                </p>
                            </div>
                        </div>

                        <div class="process-row">
                            <span>02</span>

                            <div>
                                <strong>Coordinar la atención</strong>

                                <p>
                                    Se establece una fecha, un horario y la
                                    modalidad adecuada para la tutoría.
                                </p>
                            </div>
                        </div>

                        <div class="process-row">
                            <span>03</span>

                            <div>
                                <strong>Completar el seguimiento</strong>

                                <p>
                                    Tutor y estudiante consultan el avance
                                    hasta finalizar la atención académica.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Participación de la comunidad universitaria -->
    <section class="roles-section" id="comunidad">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8">
                    <p class="section-label">
                        Comunidad universitaria
                    </p>

                    <h2>
                        Un espacio conectado para aprender, orientar y organizar.
                    </h2>
                </div>
            </div>

            <div class="row g-0 role-list">
                <div class="col-md-4">
                    <article class="role-item">
                        <span class="role-number">01</span>

                        <h3>Gestión académica</h3>

                        <p>
                            El administrador organiza usuarios, carreras,
                            materias y perfiles dentro del sistema.
                        </p>
                    </article>
                </div>

                <div class="col-md-4">
                    <article class="role-item">
                        <span class="role-number">02</span>

                        <h3>Acompañamiento del tutor</h3>

                        <p>
                            El tutor administra su disponibilidad y realiza
                            el seguimiento de cada atención académica.
                        </p>
                    </article>
                </div>

                <div class="col-md-4">
                    <article class="role-item">
                        <span class="role-number">03</span>

                        <h3>Apoyo al estudiante</h3>

                        <p>
                            El estudiante encuentra orientación para reforzar
                            las materias que necesita desarrollar.
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>