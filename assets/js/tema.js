(() => {
    const pagina = document.documentElement;
    const temaGuardado = localStorage.getItem('tema');

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

        // El botón indica el tema que se activará al presionarlo
        const actualizarBoton = (temaActual) => {
            const activarTemaClaro = temaActual === 'dark';

            textoTema.textContent = activarTemaClaro
                ? 'Tema claro'
                : 'Tema oscuro';

            botonTema.setAttribute(
                'aria-label',
                activarTemaClaro
                    ? 'Activar tema claro'
                    : 'Activar tema oscuro'
            );
        };

        actualizarBoton(temaInicial);

        botonTema.addEventListener('click', () => {
            const temaActual = pagina.getAttribute('data-bs-theme');

            const nuevoTema = temaActual === 'dark'
                ? 'light'
                : 'dark';

            pagina.setAttribute('data-bs-theme', nuevoTema);
            localStorage.setItem('tema', nuevoTema);

            actualizarBoton(nuevoTema);
        });
    });
})();