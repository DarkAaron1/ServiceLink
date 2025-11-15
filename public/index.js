document.addEventListener('DOMContentLoaded', () => {
    const sideMenu = document.querySelector('aside');
    const menuBtn = document.getElementById('menu-btn');
    const closeBtn = document.getElementById('close-btn');
    const darkMode = document.querySelector('.dark-mode');

    // Función para guardar el estado del dark mode en localStorage
    function saveDarkModeState(isDarkMode) {
        localStorage.setItem('darkMode', isDarkMode ? 'true' : 'false');
    }

    // Función para cargar el estado del dark mode desde localStorage
    function loadDarkModeState() {
        return localStorage.getItem('darkMode') === 'true';
    }

    // Función para actualizar los indicadores visuales (spans)
    function updateDarkModeUI(isDarkMode) {
        if (!darkMode) return;
        
        const lightSpan = darkMode.querySelector('span:nth-child(1)');
        const darkSpan = darkMode.querySelector('span:nth-child(2)');
        
        if (isDarkMode) {
            lightSpan?.classList.add('active');
            darkSpan?.classList.remove('active');
        } else {
            lightSpan?.classList.remove('active');
            darkSpan?.classList.add('active');
        }
    }

    // Función para aplicar el tema oscuro
    function applyDarkMode(isDarkMode) {
        if (isDarkMode) {
            document.body.classList.add('dark-mode-variables');
        } else {
            document.body.classList.remove('dark-mode-variables');
        }
        updateDarkModeUI(isDarkMode);
    }

    // Cargar y aplicar el estado del dark mode al iniciar
    const isDarkMode = loadDarkModeState();
    applyDarkMode(isDarkMode);

    // Nueva función: marca el enlace del sidebar como 'active' según la ruta actual
        function setActiveSidebar() {
        try {
            const anchors = document.querySelectorAll('aside .sidebar a');
            const currentPath = window.location.pathname.replace(/\/+$/, ''); // sin slash final
            anchors.forEach(a => {
                a.classList.remove('active');
                const href = a.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript:')) return;
                let anchorPath;
                try {
                    // Normaliza rutas absolutas y relativas
                    anchorPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '');
                } catch (e) {
                    // fallback si URL falla
                    anchorPath = href.replace(/\/+$/, '');
                }
                if (anchorPath === currentPath) {
                    a.classList.add('active');
                }
            });
        } catch (e) {
            // silencioso en caso de error
            console.error('setActiveSidebar error', e);
        }
    }

    // llamar al cargar
    setActiveSidebar();

    // Event listeners
    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            if (sideMenu) sideMenu.style.display = 'block';
            // recalcular activo si la estructura del sidebar se renderiza dinámicamente
            setActiveSidebar();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            if (sideMenu) sideMenu.style.display = 'none';
        });
    }

    if (darkMode) {
        darkMode.addEventListener('click', () => {
            const isDarkMode = document.body.classList.toggle('dark-mode-variables');
            updateDarkModeUI(isDarkMode);
            saveDarkModeState(isDarkMode);
        });
    }
});