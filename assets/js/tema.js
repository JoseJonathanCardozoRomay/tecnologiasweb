(() => {
    const pagina = document.documentElement;
    let temaGuardado = null;

    try {
        temaGuardado = localStorage.getItem('tema');
    } catch (error) {
        // El selector sigue funcionando cuando el navegador bloquea el almacenamiento.
    }

    const sistemaOscuro = window.matchMedia(
        '(prefers-color-scheme: dark)'
    ).matches;

    // Si no existe una elección guardada, usamos el tema del dispositivo
    const temaInicial = temaGuardado === 'dark' || temaGuardado === 'light'
        ? temaGuardado
        : (sistemaOscuro ? 'dark' : 'light');

    pagina.setAttribute('data-bs-theme', temaInicial);

    document.addEventListener('DOMContentLoaded', () => {
        const botonTema = document.getElementById('botonTema');
        const textoTema = document.getElementById('textoTema');

        if (!botonTema || !textoTema) {
            return;
        }

        const actualizarBoton = (temaActual) => {
            const activarTemaClaro = temaActual === 'dark';

            textoTema.textContent = activarTemaClaro
                ? 'Modo oscuro'
                : 'Modo claro';

            botonTema.setAttribute(
                'aria-label',
                activarTemaClaro
                    ? 'Activar modo claro'
                    : 'Activar modo oscuro'
            );
            botonTema.setAttribute('aria-pressed', String(activarTemaClaro));
            botonTema.title = botonTema.getAttribute('aria-label');
        };

        actualizarBoton(temaInicial);

        botonTema.addEventListener('click', () => {
            const temaActual = pagina.getAttribute('data-bs-theme');

            const nuevoTema = temaActual === 'dark'
                ? 'light'
                : 'dark';

            pagina.setAttribute('data-bs-theme', nuevoTema);
            try {
                localStorage.setItem('tema', nuevoTema);
            } catch (error) {
                // La preferencia permanece activa durante esta visita.
            }

            actualizarBoton(nuevoTema);
        });
    });
})();
