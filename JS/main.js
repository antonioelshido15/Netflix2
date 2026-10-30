document.addEventListener('DOMContentLoaded', () => {
    // Escuchar el evento del formulario para validaciones opcionales
    const ctaForm = document.querySelector('.cta-form');
    const ctaInput = document.querySelector('.cta-input');

    if (ctaForm) {
        ctaForm.addEventListener('submit', (e) => {
            if (!ctaInput.value.trim()) {
                e.preventDefault();
                ctaInput.focus();
                ctaInput.style.borderColor = 'var(--glitch-pink)';
            }
        });
    }
});