/**
 * Paralkes+ Theme Manager
 * Light/Dark mode toggle dengan system preference detection
 */
(function () {
    'use strict';

    const html = document.documentElement;
    const THEME_KEY = 'paralkes_theme';

    let currentTheme = (() => {
        try { return localStorage.getItem(THEME_KEY) || null; }
        catch (e) { return null; }
    })() || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    function applyTheme(theme) {
        currentTheme = theme;
        html.setAttribute('data-theme', theme);
        try { localStorage.setItem(THEME_KEY, theme); } catch (e) {}
        updateToggleButton(theme);
    }

    function updateToggleButton(theme) {
        const btn = document.querySelector('[data-theme-toggle]');
        if (!btn) return;
        btn.setAttribute('aria-label', 'Ganti ke mode ' + (theme === 'dark' ? 'terang' : 'gelap'));
        btn.setAttribute('title', btn.getAttribute('aria-label'));

        const sunIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>`;
        const moonIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>`;

        btn.innerHTML = theme === 'dark' ? sunIcon : moonIcon;
    }

    applyTheme(currentTheme);

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            updateToggleButton(currentTheme);
            btn.addEventListener('click', function () {
                applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
            });
        });
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        let stored = null;
        try { stored = localStorage.getItem(THEME_KEY); } catch (err) {}
        if (!stored) applyTheme(e.matches ? 'dark' : 'light');
    });
})();