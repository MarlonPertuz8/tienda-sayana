document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer para animaciones al hacer scroll
    const observerOptions = {
        threshold: 0.2 // Se activa cuando el 20% del elemento es visible
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const animation = entry.target.getAttribute('data-animation');
                entry.target.classList.add('animate__animated', `animate__${animation}`);
                entry.target.style.opacity = "1";
                observer.unobserve(entry.target); // Solo animar una vez
            }
        });
    }, observerOptions);

    // Seleccionamos todos los elementos con la clase de animación
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    animatedElements.forEach(el => {
        el.style.opacity = "0"; // Invisible hasta que se detecte el scroll
        observer.observe(el);
    });
});