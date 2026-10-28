
/**
 * ECLAT VISAGE — Footer centralizado
 * Archivo: modulos/footer.js
 * Compatible con la raíz del proyecto y el directorio /CRUD/.
 */
(function () {
    'use strict';

    const STYLE_ID = 'eclat-footer-estilos';

    // Detectar si la página está dentro de /CRUD/.
    function esDirectorioCrud() {
        const path = window.location.pathname.replace(/\\/g, '/');
        return /\/crud(?:\/|$)/i.test(path);
    }

    const rootPrefix = esDirectorioCrud() ? '../' : '';

    // Insertar los estilos una sola vez.
    function insertarEstilos() {
        if (document.getElementById(STYLE_ID)) return;

        const style = document.createElement('style');
        style.id = STYLE_ID;
        style.textContent = `
            /* FOOTER ÉCLAT VISAGE */
            footer.eclat-footer {
                position: relative;
                isolation: isolate;
                width: 100%;
                margin: 65px 0 0;
                padding: 0;
                overflow: hidden;
                background:
                    radial-gradient(ellipse at 90% 0%, rgba(180,154,114,.13), transparent 35%),
                    linear-gradient(135deg, #30261f 0%, #403329 55%, #352a22 100%);
                color: #e8ded1;
                font-family: 'Poppins', sans-serif;
                font-size: 13px;
                line-height: 1.8;
                -webkit-font-smoothing: antialiased;
            }

            .eclat-footer *,
            .eclat-footer *::before,
            .eclat-footer *::after {
                box-sizing: border-box;
            }

            .eclat-footer__inner {
                width: min(1160px, calc(100% - 64px));
                margin: 0 auto;
            }

            /* CONTENIDO PRINCIPAL */
            .eclat-footer__main {
                display: grid;
                grid-template-columns: 1.5fr 1fr 1.2fr 1fr;
                gap: 42px;
                padding: 62px 0 48px;
            }

            .eclat-footer__col {
                min-width: 0;
            }

            /* LOGOTIPO */
            .eclat-footer__logo {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin: 0 0 19px;
                color: #fff9ef !important;
                font-family: 'Playfair Display', Georgia, serif;
                font-size: 30px;
                font-weight: 400;
                line-height: 1.2;
                letter-spacing: -.4px;
                text-decoration: none !important;
                transition: color .25s ease;
            }

            .eclat-footer__logo-mark {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 37px;
                height: 37px;
                flex-shrink: 0;
                border: 1px solid #b49a72;
                border-radius: 50%;
                color: #d5bd97;
                font-family: 'Playfair Display', Georgia, serif;
                font-size: 21px;
                font-style: italic;
            }

            .eclat-footer__logo:hover {
                color: #d5bd97 !important;
            }

            .eclat-footer__description {
                max-width: 290px;
                margin: 0;
                color: #c9bdb0;
                font-size: 12px;
                font-weight: 300;
                line-height: 2;
            }

            .eclat-footer__tagline {
                display: inline-flex;
                align-items: center;
                gap: 9px;
                margin-top: 21px;
                color: #d5bd97;
                font-size: 9px;
                font-weight: 500;
                letter-spacing: 1.6px;
                text-transform: uppercase;
            }

            .eclat-footer__tagline::before {
                content: "";
                width: 22px;
                height: 1px;
                background: #b49a72;
            }

            /* TÍTULOS Y ENLACES */
            .eclat-footer__heading {
                margin: 5px 0 21px;
                color: #d5bd97;
                font-size: 10px;
                font-weight: 500;
                letter-spacing: 1.8px;
                line-height: 1.6;
                text-transform: uppercase;
            }

            .eclat-footer__text {
                margin: 0 0 10px;
                color: #c9bdb0;
                font-size: 12px;
                font-weight: 300;
                line-height: 1.9;
                overflow-wrap: anywhere;
            }

            .eclat-footer__link {
                display: inline-flex;
                align-items: center;
                gap: 9px;
                max-width: 100%;
                color: #eee3d6 !important;
                font-size: 12px;
                font-weight: 300;
                text-decoration: none !important;
                overflow-wrap: anywhere;
                transition: color .25s ease, transform .25s ease;
            }

            .eclat-footer__link:hover {
                color: #d5bd97 !important;
                transform: translateX(3px);
            }

            .eclat-footer__symbol {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 29px;
                height: 29px;
                flex: 0 0 29px;
                border: 1px solid rgba(213,189,151,.35);
                border-radius: 50%;
                color: #d5bd97;
                font-size: 13px;
            }

            .eclat-footer__contact-item {
                margin-bottom: 15px;
            }

            .eclat-footer__contact-label {
                display: block;
                margin-bottom: 4px;
                color: #9f9080;
                font-size: 9px;
                font-weight: 500;
                letter-spacing: 1px;
                text-transform: uppercase;
            }

            .eclat-footer__address {
                margin: 0;
                color: #c9bdb0;
                font-size: 12px;
                font-weight: 300;
                line-height: 2;
            }

            /* MÉTODOS DE PAGO */
            .eclat-footer__payments {
                display: flex;
                flex-wrap: wrap;
                gap: 7px;
                margin-top: 18px;
            }

            .eclat-footer__payment {
                display: inline-flex;
                align-items: center;
                padding: 5px 9px;
                border: 1px solid rgba(213,189,151,.25);
                border-radius: 5px;
                color: #dfd1bf;
                font-size: 9px;
                font-weight: 400;
                line-height: 1.6;
            }

            /* BARRA INFERIOR */
            .eclat-footer__bottom {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                min-height: 68px;
                padding: 18px 0;
                border-top: 1px solid rgba(255,255,255,.12);
            }

            .eclat-footer__copyright {
                margin: 0;
                color: #b5a697;
                font-size: 10px;
                font-weight: 300;
                letter-spacing: .3px;
                line-height: 1.8;
            }

            .eclat-footer__bottom-link {
                flex-shrink: 0;
                color: #d5bd97 !important;
                font-size: 10px;
                font-weight: 400;
                text-decoration: none !important;
                transition: color .25s ease;
            }

            .eclat-footer__bottom-link:hover {
                color: #fff9ef !important;
            }

            /* TABLET */
            @media (max-width: 900px) {
                .eclat-footer__main {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 36px 42px;
                    padding: 48px 0 38px;
                }

                .eclat-footer__description {
                    max-width: 340px;
                }
            }

            /* CELULARES */
            @media (max-width: 520px) {
                footer.eclat-footer {
                    margin-top: 42px;
                }

                .eclat-footer__inner {
                    width: calc(100% - 40px);
                }

                .eclat-footer__main {
                    grid-template-columns: minmax(0, 1fr);
                    gap: 29px;
                    padding: 39px 0 30px;
                }

                .eclat-footer__logo {
                    font-size: 28px;
                }

                .eclat-footer__heading {
                    margin-bottom: 13px;
                }

                .eclat-footer__bottom {
                    align-items: flex-start;
                    flex-direction: column;
                    gap: 10px;
                    padding: 18px 0;
                }

                .eclat-footer__copyright {
                    font-size: 9px;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .eclat-footer *,
                .eclat-footer *::before,
                .eclat-footer *::after {
                    transition: none !important;
                }
            }
        `;

        document.head.appendChild(style);
    }

    // Generar el contenido del footer.
    function generarFooterHTML() {
        const anioActual = new Date().getFullYear();

        return `
            <div class="eclat-footer__inner">
                <div class="eclat-footer__main">

                    <section class="eclat-footer__col" aria-label="Acerca de Éclat Visage">
                        <a class="eclat-footer__logo" href="${rootPrefix}index.html" aria-label="Éclat Visage, inicio">
                            <span class="eclat-footer__logo-mark" aria-hidden="true">e</span>
                            <span>éclat visage</span>
                        </a>
                        <p class="eclat-footer__description">
                            Skincare diseñado para resaltar la belleza natural y acompañar el cuidado diario de tu piel.
                        </p>
                        <span class="eclat-footer__tagline">Belleza que nace del cuidado</span>
                    </section>

                    <section class="eclat-footer__col" aria-label="Redes sociales">
                        <h3 class="eclat-footer__heading">Conecta con nosotros</h3>
                        <p class="eclat-footer__text">
                            Descubre novedades, inspiración y rituales de cuidado facial.
                        </p>
                        <a class="eclat-footer__link" href="https://www.instagram.com/eclatvisage/" target="_blank" rel="noopener noreferrer">
                            <span class="eclat-footer__symbol" aria-hidden="true">↗</span>
                            @eclatvisage
                        </a>
                    </section>

                    <section class="eclat-footer__col" aria-label="Información de contacto">
                        <h3 class="eclat-footer__heading">Hablemos</h3>
                        <div class="eclat-footer__contact-item">
                            <span class="eclat-footer__contact-label">Correo electrónico</span>
                            <a class="eclat-footer__link" href="mailto:eclatvisage@gmail.com">eclatvisage@gmail.com</a>
                        </div>
                        <div class="eclat-footer__contact-item">
                            <span class="eclat-footer__contact-label">Teléfono</span>
                            <a class="eclat-footer__link" href="tel:3000000000">300 000 0000</a>
                        </div>
                    </section>

                    <section class="eclat-footer__col" aria-label="Ubicación y métodos de pago">
                        <h3 class="eclat-footer__heading">Encuéntranos</h3>
                        <p class="eclat-footer__address">Calarcá, Quindío,<br>Colombia</p>
                        <h3 class="eclat-footer__heading" style="margin-top:22px;margin-bottom:12px;">Métodos de pago</h3>
                        <div class="eclat-footer__payments">
                            <span class="eclat-footer__payment">Nequi</span>
                            <span class="eclat-footer__payment">Bancolombia</span>
                            <span class="eclat-footer__payment">Débito y crédito</span>
                        </div>
                    </section>

                </div>

                <div class="eclat-footer__bottom">
                    <p class="eclat-footer__copyright">
                        © ${anioActual} Éclat Visage. Todos los derechos reservados.
                    </p>
                    <a class="eclat-footer__bottom-link" href="${rootPrefix}index.html">Volver al inicio ↑</a>
                </div>
            </div>
        `;
    }

    // Crear, reemplazar o actualizar el footer.
    function renderizarFooter() {
        insertarEstilos();

        const footerHTML = generarFooterHTML();
        let footer = document.getElementById('eclat-footer-principal');

        // Si existe un contenedor designado, reemplazarlo por el footer.
        const contenedor = document.getElementById('footer-modulo');

        if (contenedor) {
            footer = document.createElement('footer');
            footer.className = 'footer eclat-footer';
            footer.id = 'eclat-footer-principal';
            footer.innerHTML = footerHTML;
            contenedor.replaceWith(footer);
            return;
        }

        // Si ya existe un footer, reutilizarlo para evitar duplicados.
        if (!footer) {
            footer = document.querySelector('footer.footer, footer');
        }

        if (footer) {
            footer.className = 'footer eclat-footer';
            footer.id = 'eclat-footer-principal';
            footer.innerHTML = footerHTML;
            return;
        }

        // Si no hay footer, crearlo al final de la página.
        footer = document.createElement('footer');
        footer.className = 'footer eclat-footer';
        footer.id = 'eclat-footer-principal';
        footer.innerHTML = footerHTML;
        document.body.appendChild(footer);
    }

    // Ejecutar cuando el DOM esté disponible.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderizarFooter);
    } else {
        renderizarFooter();
    }

    // Permitir actualizar el footer manualmente.
    window.actualizarFooterModulo = renderizarFooter;
})();
