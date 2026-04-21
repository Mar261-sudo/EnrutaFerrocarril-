document.addEventListener("DOMContentLoaded", () => {

    // 🎲 curiosidades
    window.mostrarCuriosidad = function () {
        if (!curiosidades || curiosidades.length === 0) return;

        const random = curiosidades[Math.floor(Math.random() * curiosidades.length)];
        document.getElementById("datoCurioso").textContent = random;
    };

    // 🚂 elementos
    const train = document.querySelector(".train");
    const sections = document.querySelectorAll(".section");

    let estaciones = [];

    // 📍 calcular posiciones
    function calcularEstaciones() {
        estaciones = [];
        sections.forEach(section => {
            estaciones.push(section.offsetTop);
        });
    }

    // 🔄 inicializar
    calcularEstaciones();
    window.addEventListener("resize", calcularEstaciones);

    // 🚀 SCROLL PRINCIPAL (movimiento continuo)
    window.addEventListener("scroll", () => {

        const scroll = window.scrollY;
        const maxScroll = document.body.scrollHeight - window.innerHeight;

        const porcentaje = scroll / maxScroll;
        const movimiento = porcentaje * (window.innerWidth - 100);

        // 🚂 mover tren SIEMPRE
        if (train) {
            train.style.transform = `translateX(${movimiento}px)`;
        }

        // 🎯 detectar estación
        let index = 0;
        estaciones.forEach((pos, i) => {
            if (scroll >= pos - 150) {
                index = i;
            }
        });

        sections.forEach(sec => sec.classList.remove("active"));
        if (sections[index]) {
            sections[index].classList.add("active");
        }

        // 🎬 animación aparición
        const trigger = window.innerHeight * 0.85;

        sections.forEach(section => {
            const top = section.getBoundingClientRect().top;

            if (top < trigger) {
                section.classList.add("visible");
            }
        });

    });

    // 🌐 360
    const pano = document.getElementById("panorama");
    if (pano) {
        pannellum.viewer('panorama', {
            type: "equirectangular",
            panorama: "https://pannellum.org/images/alma.jpg",
            autoLoad: true,
            autoRotate: -2
        });
    }

});