<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software de Gestión Integral</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

<style>
    
    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f9f9f9, #e9e9e9);
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    header {
    width: 100%;
    background: #333;
    color: #fff;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
}

header h1 {
    font-size: 24px;
    margin: 0;
}

.menu-toggle {
    display: none;
    font-size: 24px;
    cursor: pointer;
}

nav {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}

nav ul li {
    margin-left: 20px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    padding: 10px;
    transition: color 0.3s, background-color 0.3s;
    display: block; /* Ajuste para centrar texto verticalmente */
    text-align: center; /* Centrar texto horizontalmente */
}

nav ul li a:hover {
    color: #f1f1f1;
    background-color: #444; /* Color de fondo al pasar el mouse */
}

.btn-login {
    padding: 10px 20px;
    background: #555;
    color: #fff;
    text-transform: uppercase;
    text-decoration: none;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}
.carousel {
        width: 100%;
        margin: 0 auto;
    }

.carousel img {
    width: 100%;
    height: auto;
}

.carousel-container {
    width: 100%;
    max-width: 1200px;
    margin: 20px;
}

.btn-login:hover {
    background: #777;
}
    .hero {
        width: 100%;
        background: linear-gradient(135deg, #333, #444);
        color: #fff;
        text-align: center;
        padding: 100px 20px;
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
    }

    .hero h1 {
        font-size: 48px;
        margin: 0;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 24px;
        margin: 0;
        max-width: 800px;
        margin: 0 auto;
    }

    .gallery {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        margin: 20px 0;
    }

    .gallery img {
        width: 100%;
        max-width: 300px;
        margin: 10px;
        border-radius: 15px;
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    }

    .container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    }

    .left-content {
        width: calc(50% - 10px); /* Ajustar según el espacio deseado entre los contenidos */
    }

    .right-content {
        width: calc(50% - 10px); /* Ajustar según el espacio deseado entre los contenidos */
    }

        /* Animación de desvanecimiento hacia arriba */
    .fade-up {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .fade-up.show {
        opacity: 1;
        transform: translateY(0);
    }

    .content-box {
        background: #f9f9f9;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }


    .content-box h2 {
        color: #333;
        margin-bottom: 20px;
    }

    .content-box p, .content-box ul {
        font-size: 18px;
        color: #666;
        line-height: 1.6;
    }

    .content-box ul {
        list-style: disc inside;
        padding-left: 20px;
    }

    .content-box ul li {
        margin-bottom: 10px;
    }

    .image-container {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .image-container img {
        width: 100%;
        max-width: 800px;
        border-radius: 15px;
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    }

    footer {
        text-align: center;
        padding: 10px;
        font-size: 14px;
        background: #333;
        color: #fff;
        width: 100%;
        margin-top: auto;
    }

    @media (max-width: 800px) {
    /* Estilos para pantallas más pequeñas */
    header {
        padding: 10px;
    }

    header h1 {
        font-size: 24px;
    }

    .menu-toggle {
        display: block;
    }

    nav {
        display: none;
        position: absolute;
        top: 60px;
        right: 20px;
        background: rgba(0, 0, 0, 0.9); /* Fondo semi-transparente */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        border-radius: 5px;
    }

    nav.showing {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    nav ul {
        flex-direction: column;
    }

    nav ul li {
        margin-left: 0;
        margin-bottom: 10px;
    }

    nav ul li a {
        padding: 15px; /* Aumentar el espacio para tocar enlaces */
        font-size: 18px; /* Tamaño de fuente más grande */
        color: #fff;
        transition: color 0.3s, background-color 0.3s;
    }

    nav ul li a:hover {
        background-color: #444; /* Color de fondo al pasar el mouse */
    }
}
</style>
    </style>
</head>
<body>
    <header>
        <h1>Gestión Integral</h1>
        <div class="menu-toggle" onclick="toggleMenu()">☰</div>
        <nav>
            <ul id="menu">
                <li><a href="#introduccion">Introducción</a></li>
                <li><a href="#objetivo-general">Objetivo General</a></li>
                <li><a href="#objetivos-especificos">Objetivos Específicos</a></li>
                <li><a href="#planteamiento">Planteamiento del Problema</a></li>
                <li><a href="#justificacion">Justificación</a></li>
                <li><a href="#alcance">Alcance</a></li>
                <li><a href="#impactos">Impactos</a></li>
            </ul>
        </nav>
        <button class="btn-login">Iniciar Sesión</button>
    </header>

<section class="hero">
    <h1>Software de Gestión Integral</h1>
    <p>Mejora la eficiencia en la gestión de seguridad y automatiza procesos administrativos.</p>
</section>
<div class="container">
    <div class="content-box fade-up" id="introduccion">
        <p>
            Este proyecto tiene como objetivo desarrollar un software de gestión integral para empresas colombianas que permita mejorar la eficiencia en la gestión de la seguridad física de las plantas y la automatización de procesos administrativos como la generación de reportes de nómina, certificados laborales, horarios, administración de información de empleados, entre otros.
        </p>
    </div>
</div>

<div class="carousel-container">
    <div class="carousel">
        <div><img src="../assets/imagen1C.jpg" alt="Imagen 1"></div>
        <div><img src="../assets/imagen2C.jpg" alt="Imagen 2"></div>
        <div><img src="../assets/imagen3C.jpg" alt="Imagen 3"></div>
        <div><img src="../assets/imagen3.jpg" alt="Imagen 4"></div>
    </div>
</div>

<div class="gallery">
    <a href="../assets/imagen1.jpeg" data-fancybox="gallery" data-caption="Descripción de la imagen 1">
        <img src="../assets/imagen1.jpeg" alt="Galería 1">
    </a>
    <a href="../assets/imagen2.jpg" data-fancybox="gallery" data-caption="Descripción de la imagen 2">
        <img src="../assets/imagen2.jpg" alt="Galería 2">
    </a>
    <a href="../assets/imagen3.jpg" data-fancybox="gallery" data-caption="Descripción de la imagen 3">
        <img src="../assets/imagen3.jpg" alt="Galería 3">
    </a>
    <a href="../assets/imagen4.jpg" data-fancybox="gallery" data-caption="Descripción de la imagen 4">
        <img src="../assets/imagen4.jpg" alt="Galería 4">
    </a>
</div>
<div class="container">
    <div class="left-content">
        <div class="content-box fade-up" id="objetivos-especificos">
            <h2>Objetivos Específicos</h2>
            <ul>
                <li>Identificar los requerimientos específicos de seguridad física y procesos administrativos.</li>
                <li>Documentar los requerimientos en un documento de especificación de requisitos de software.</li>
                <li>Definir los objetivos específicos del software y los entregables asociados.</li>
                <li>Desarrollar un plan de gestión de riesgos.</li>
                <li>Implementar el software y automatizar los procesos identificados.</li>
                <li>Integrar el software con los sistemas existentes de las empresas.</li>
                <li>Realizar pruebas de funcionalidad, usabilidad y rendimiento.</li>
                <li>Evaluar el impacto del software en la eficiencia de la gestión de la seguridad y procesos administrativos.</li>
            </ul>
        </div>
        <div class="content-box fade-up" id="justificacion">
            <h2>Justificación</h2>
            <p>
                La automatización y gestión eficiente de procesos administrativos y seguridad física es crucial en el entorno empresarial actual.
            </p>
            <p>
                Este proyecto busca solucionar estos problemas mediante un software que optimice la gestión de seguridad física, procesos administrativos y recursos humanos.
            </p>
        </div>
        <div class="content-box fade-up" id="impactos">
            <h2>Impactos</h2>
            <p>
                Económico: Reducción de costos y mayor productividad en gestión de seguridad y procesos.
            </p>
            <p>
                Tecnológico: Mejora competitiva y adopción de tecnología avanzada.
            </p>
            <p>
                Social: Ambiente laboral seguro y eficiente.
            </p>
        </div>
    </div>
    <div class="right-content">
        <div class="content-box fade-up" id="objetivo-general">
            <h2>Objetivo General</h2>
            <p>
                Desarrollar un software que mejore la eficiencia en la gestión de la seguridad física de las plantas y la automatización de procesos administrativos, proporcionando una solución efectiva y eficiente para las empresas colombianas.
            </p>
        </div>
        <div class="content-box fade-up" id="planteamiento">
            <h2>Planteamiento del Problema</h2>
            <p>
                La falta de un sistema integral eficiente para gestionar la seguridad física y procesos administrativos en empresas colombianas conlleva a una gestión ineficiente y errores en los registros manuales.
            </p>
            <p>
                Las dificultades incluyen el control de acceso a instalaciones, supervisión de cámaras de seguridad, gestión de nómina, entre otros.
            </p>
            <p>
                ¿Cómo mejorar la gestión de seguridad física y procesos administrativos de manera eficiente y automatizada? La respuesta: implementar un software integral.
            </p>
        </div>
        <div class="content-box fade-up" id="alcance">
            <h2>Alcance del Proyecto</h2>
            <p>
                El proyecto incluye diseño, desarrollo e implementación de software que automatice y gestione seguridad física y procesos administrativos en empresas colombianas.
            </p>
            <p>
                Funcionalidades: gestión de empleados, generación de reportes de nómina, certificados laborales, horarios y control de acceso.
            </p>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 Software de Gestión Integral - Todos los derechos reservados</p>
</footer>

<script>

    // Función para inicializar Intersection Observer
function initIntersectionObserver() {
    const fadeUpElements = document.querySelectorAll('.fade-up');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    }, {
        threshold: 0.3 // Cambiar según se necesite, controla cuánto del elemento debe estar visible para activar la animación
    });

    fadeUpElements.forEach(element => {
        observer.observe(element);
    });
}

// Evento para cargar animaciones cuando se carga la página
window.addEventListener('load', () => {
    initIntersectionObserver();
});
    
    function toggleMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const menu = document.querySelector('nav');
        menu.classList.toggle('showing');
    }

    // Event listener para el click en el toggle del menú
    document.querySelector('.menu-toggle').addEventListener('click', toggleMenu);

$(document).ready(function() {
$("[data-fancybox]").fancybox({
    buttons: [
        "zoom",
        "slideShow",
        "thumbs",
        "close" // Agregamos el botón de cierre
    ],
    loop: true,
    protect: true
});
});


$(document).ready(function(){
        $('.carousel').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            dots: true,
            arrows: true,
            infinite: true,
            speed: 500,
            fade: true,
            cssEase: 'linear'
        });
    });


let lastScrollTop = 0;
const menuToggle = document.querySelector('.menu-toggle');

window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop) {
        // Descendiendo
        menuToggle.classList.add('hidden');
    } else {
        // Ascendiendo
        menuToggle.classList.remove('hidden');
    }
    
    lastScrollTop = scrollTop;
});
</script>
</body>
</html>