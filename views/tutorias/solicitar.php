<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="mb-4">
    <span class="eyebrow">Solicitud académica</span>
    <h1 class="page-title mb-1">¿Qué tipo de tutoría necesitas?</h1>
    <p class="text-muted mb-0">Elige la modalidad que corresponda a tu necesidad académica.</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card card-custom h-100 p-4 p-xl-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="metric-icon text-primary bg-primary-subtle"><i class="bi bi-people"></i></div>
                <div><span class="eyebrow">Modalidad grupal</span><h3 class="fw-bold mb-0">Tutoría de materias</h3></div>
            </div>
            <p class="text-muted">Para reforzar una materia junto con otros estudiantes. La institución publica los horarios y el tutor asignado.</p>
            <div class="alert alert-light border mt-3 mb-4">
                <div class="fw-semibold mb-2"><i class="bi bi-clock me-1"></i>Bloques oficiales</div>
                <div class="small">Mañana 07:00–10:00 · Tarde 15:00–18:00 · Noche 19:00–22:00</div>
                <div class="small text-muted mt-1">De lunes a viernes.</div>
            </div>
            <a href="tutorias_grupales_listar.php" class="btn btn-primary mt-auto"><i class="bi bi-arrow-right-circle me-1"></i>Ver tutorías de materias</a>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-custom h-100 p-4 p-xl-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="metric-icon text-success bg-success-subtle"><i class="bi bi-mortarboard"></i></div>
                <div><span class="eyebrow">Modalidad individual</span><h3 class="fw-bold mb-0">Proyectos de grado</h3></div>
            </div>
            <p class="text-muted">Para tesis, proyecto de grado, trabajo de grado o proyecto final, con acompañamiento individual. El horario se coordina según la disponibilidad real del tutor.</p>
            <div class="alert alert-light border mt-3 mb-4">
                <div class="fw-semibold mb-2"><i class="bi bi-person-check me-1"></i>Acompañamiento individual</div>
                <div class="small">Se relaciona con tu proyecto de grado, carrera, tutor y sesión específica.</div>
            </div>
            <a href="proyectos_grado_listar.php" class="btn btn-success mt-auto"><i class="bi bi-arrow-right-circle me-1"></i>Ver proyectos de grado</a>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mt-4">
    <div class="row align-items-center g-3">
        <div class="col-auto"><div class="metric-icon text-secondary bg-light"><i class="bi bi-list-check"></i></div></div>
        <div class="col"><strong>Consulta tus solicitudes</strong><div class="small text-muted">Puedes revisar tus tutorías de materias y de proyectos de grado desde el módulo Proyectos de grado y Mis tutorías.</div></div>
        <div class="col-auto"><a href="proyectos_grado_listar.php?seccion=solicitudes" class="btn btn-outline-secondary">Ver solicitudes personales</a></div>
    </div>
</div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
