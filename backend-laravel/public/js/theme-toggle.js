/**
 * Sistema de Gerenciamento de Tema Claro/Escuro
 * Alterna entre tema claro e escuro com persistência no localStorage
 */

(function() {
    const THEME_KEY = 'profeluno-theme';
    const DARK_THEME = 'dark';
    const LIGHT_THEME = 'light';

    /**
     * Obter o tema salvo ou o tema padrão
     */
    function getSavedTheme() {
        return localStorage.getItem(THEME_KEY) || DARK_THEME;
    }

    /**
     * Salvar o tema no localStorage
     */
    function saveTheme(theme) {
        localStorage.setItem(THEME_KEY, theme);
    }

    /**
     * Aplicar o tema ao documento
     */
    function applyTheme(theme) {
        const html = document.documentElement;
        html.setAttribute('data-theme', theme);
        
        // Atualizar o ícone do botão
        updateThemeButton(theme);
    }

    /**
     * Alternar entre tema claro e escuro
     */
    function toggleTheme() {
        const currentTheme = getSavedTheme();
        const newTheme = currentTheme === DARK_THEME ? LIGHT_THEME : DARK_THEME;
        
        saveTheme(newTheme);
        applyTheme(newTheme);
    }

    /**
     * Atualizar o ícone do botão de tema
     */
    function updateThemeButton(theme) {
        const themeBtn = document.getElementById('themeToggleBtn');
        if (!themeBtn) return;

        const icon = themeBtn.querySelector('i');
        if (theme === LIGHT_THEME) {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            themeBtn.title = 'Modo Escuro';
            themeBtn.setAttribute('aria-label', 'Alternar para modo escuro');
        } else {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            themeBtn.title = 'Modo Claro';
            themeBtn.setAttribute('aria-label', 'Alternar para modo claro');
        }
    }

    /**
     * Inicializar o sistema de tema
     */
    function initTheme() {
        const savedTheme = getSavedTheme();
        applyTheme(savedTheme);
        
        // Adicionar evento ao botão
        const themeBtn = document.getElementById('themeToggleBtn');
        if (themeBtn) {
            themeBtn.addEventListener('click', toggleTheme);
        }
    }

    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }

    // Expor funções globalmente
    window.themeManager = {
        toggle: toggleTheme,
        get: getSavedTheme,
        set: function(theme) {
            if ([LIGHT_THEME, DARK_THEME].includes(theme)) {
                saveTheme(theme);
                applyTheme(theme);
            }
        }
    };
})();
