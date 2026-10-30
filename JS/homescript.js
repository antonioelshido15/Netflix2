document.addEventListener('DOMContentLoaded', () => {
    // 1. Lógica del Carrusel Destacado (Animación fluida e infinita)
    const items = document.querySelectorAll('.hero-item');
    const dots = document.querySelectorAll('.dot');
    let currentIndex = 1; // Índice de la imagen central inicial

    // 2. Lógica para el Scroll Horizontal de las películas (Botones Flechas)
    const wrapper = document.getElementById('sliderWrapper');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');

    function updateCarousel(newIndex) {
        const total = items.length;
        
        // Matemáticas para el ciclo infinito: calcula quién va a la izquierda y a la derecha
        const prevIndex = (newIndex - 1 + total) % total;
        const nextIndex = (newIndex + 1) % total;

        items.forEach((item, index) => {
            // Limpiamos las clases de posición actuales
            item.classList.remove('center-img', 'left-img', 'right-img', 'hidden-img');
            
            // Asignamos las nuevas posiciones
            if (index === newIndex) {
                item.classList.add('center-img');
            } else if (index === prevIndex) {
                item.classList.add('left-img');
            } else if (index === nextIndex) {
                item.classList.add('right-img');
            } else {
                item.classList.add('hidden-img'); // Si llegas a poner 4 o más imágenes
            }
        });

        // Actualizamos los puntos blancos
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === newIndex);
        });

        currentIndex = newIndex;
    }

    // Eventos de clic en los puntos
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => updateCarousel(index));
    });

    // Eventos de clic en las imágenes para avanzar o retroceder de forma interactiva
    items.forEach((item, index) => {
        item.addEventListener('click', () => {
            if (item.classList.contains('left-img') || item.classList.contains('right-img')) {
                updateCarousel(index);
            }
        });
    });

    // Autoplay: Mueve el carrusel suavemente cada 5 segundos
    setInterval(() => {
        let nextIndex = (currentIndex + 1) % items.length;
        updateCarousel(nextIndex);
    }, 5000);

    // Inicializar el carrusel en la posición correcta al cargar la página
    updateCarousel(currentIndex);

    // 2. Lógica para el Scroll Horizontal de las películas (Catálogo)
    const scrollingWrapper = document.querySelector('.scrolling-wrapper');
    if (scrollingWrapper) {
        scrollingWrapper.addEventListener('wheel', (evt) => {
            // Convertimos el scroll vertical del ratón en scroll horizontal
            evt.preventDefault();
            scrollingWrapper.scrollLeft += evt.deltaY * 1.5; 
        });
    }

    if (wrapper && btnPrev && btnNext) {
        
        // Función para verificar si sobra contenido y prender/apagar flechas
        const checkScroll = () => {
            // Activar flecha izquierda si nos movimos del inicio
            if (wrapper.scrollLeft > 0) {
                btnPrev.classList.remove('d-none');
            } else {
                btnPrev.classList.add('d-none');
            }

            // Activar flecha derecha si el total del contenido es mayor al espacio visible
            // Sumamos un pixel de tolerancia para problemas de redondeo del navegador
            if (Math.ceil(wrapper.scrollLeft + wrapper.clientWidth) < wrapper.scrollWidth) {
                btnNext.classList.remove('d-none');
            } else {
                btnNext.classList.add('d-none');
            }
        };

        // Comprobar flechas al cargar la página y cuando se cambie el tamaño de ventana
        checkScroll();
        window.addEventListener('resize', checkScroll);
        
        // Comprobar flechas mientras deslizamos
        wrapper.addEventListener('scroll', checkScroll);

        // Eventos para mover el carrusel al hacer clic (avanza un 80% del ancho visible)
        btnPrev.addEventListener('click', () => {
            wrapper.scrollBy({ left: -wrapper.clientWidth * 0.8, behavior: 'smooth' });
        });

        btnNext.addEventListener('click', () => {
            wrapper.scrollBy({ left: wrapper.clientWidth * 0.8, behavior: 'smooth' });
        });
    }

});