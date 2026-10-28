/**
 * ECLAT VISAGE — Módulo Único de Navegación Centralizado (modulos/nav.js)
 * 
 * Barra de navegación y subnav interactivo sin emojis ni iconos, con apertura
 * estricta mediante clic (abre y cierra alternadamente sin fallos al recargar).
 */

(function () {
    'use strict';

    // Determinar si la página actual está dentro de /CRUD/
    function esDirectorioCrud() {
        const path = (window.location && window.location.pathname) ? window.location.pathname.replace(/\\/g, '/') : '';
        return /\/crud\//i.test(path);
    }

    const isCrud = esDirectorioCrud();
    const rootPrefix = isCrud ? '../' : '';
    const crudPrefix = isCrud ? '' : 'CRUD/';

    // Obtener datos del usuario autenticado
    function obtenerUsuario() {
        const match = document.cookie.match(new RegExp('(^| )eclat_usuario=([^;]+)'));
        if (match) {
            try {
                const decoded = decodeURIComponent(match[2]);
                const parsed = JSON.parse(decoded);
                if (parsed && parsed.usuario) return parsed;
            } catch (e) {}
        }
        try {
            const local = localStorage.getItem('eclat_usuario');
            if (local) {
                const parsed = JSON.parse(local);
                if (parsed && parsed.usuario) return parsed;
            }
        } catch (e) {}
        return null;
    }

    // Cantidad total de productos en el carrito
    function obtenerCantidadCarrito(usuario) {
        try {
            const key = (usuario && usuario.usuario) 
                ? `eclat_visage_carrito_${usuario.usuario}` 
                : 'eclat_visage_carrito_invitado';
            const data = localStorage.getItem(key);
            if (!data) return 0;
            const items = JSON.parse(data);
            if (!Array.isArray(items)) return 0;
            return items.reduce((acc, item) => acc + (Number(item.cantidad) || 1), 0);
        } catch (e) {
            return 0;
        }
    }

    // Nombre de archivo actual
    function obtenerNombreArchivoActual() {
        const path = (window.location && window.location.pathname) ? window.location.pathname.replace(/\\/g, '/') : '';
        const partes = path.split('/');
        return (partes[partes.length - 1] || 'index.html').toLowerCase();
    }

    // Enlaces de navegación principal (sin emojis)
    const ENLACES_NAV = [
        { id: 'inicio', texto: 'INICIO', url: `${rootPrefix}index.html`, match: ['index.html', ''] },
        { id: 'tienda', texto: 'VER TODO', url: `${rootPrefix}tienda.html`, match: ['tienda.html'] },
        { id: 'mas-vendidos', texto: 'MÁS VENDIDOS', url: `${rootPrefix}mas-vendidos.html`, match: ['mas-vendidos.html'] },
        { id: 'test', texto: 'TEST DE PIEL', url: `${rootPrefix}test.html`, match: ['test.html'] },
        { id: 'diario', texto: 'DIARIO', url: `${rootPrefix}blog.html`, match: ['blog.html'] },
        { id: 'nosotros', texto: 'NOSOTROS', url: `${rootPrefix}nosotros.html`, match: ['nosotros.html'] },
        { id: 'contacto', texto: 'CONTACTO', url: `${crudPrefix}contacto.php`, match: ['contacto.php'] },
        { id: 'documentos', texto: 'DOCUMENTOS', url: 'https://drive.google.com', external: true }
    ];

    // Submódulos del CRUD con numeración sobria (sin emojis)
    const ENLACES_CRUD = [
        { num: '01', texto: 'Clientes', desc: 'Usuarios y registros', url: `${crudPrefix}clientes_crud.php`, match: 'clientes' },
        { num: '02', texto: 'Proveedores', desc: 'Insumos y aliados', url: `${crudPrefix}proveedores_crud.php`, match: 'proveedores' },
        { num: '03', texto: 'Productos', desc: 'Catálogo y precios', url: `${crudPrefix}productos_crud.php`, match: 'productos' },
        { num: '04', texto: 'Categorías', desc: 'Líneas faciales', url: `${crudPrefix}categorias_crud.php`, match: 'categorias' },
        { num: '05', texto: 'Inventario', desc: 'Existencias y stock', url: `${crudPrefix}inventario_crud.php`, match: 'inventario' },
        { num: '06', texto: 'Facturas', desc: 'Ventas y compras', url: `${crudPrefix}facturas_crud.php`, match: 'facturas' },
        { num: '07', texto: 'Empleados', desc: 'Personal y accesos', url: `${crudPrefix}empleados_crud.php`, match: 'empleados', full: true }
    ];

    // Renderizar la barra de navegación
    function renderizarNavbar() {
        const usuario = obtenerUsuario();
        const esAdmin = Boolean(usuario && usuario.rol && String(usuario.rol).toLowerCase().trim() === 'admin');
        const archivoActual = obtenerNombreArchivoActual();
        const cantidadCarrito = obtenerCantidadCarrito(usuario);

        const esInicio = archivoActual === 'index.html' || archivoActual === '' || archivoActual.endsWith('/eclat_visage/');
        const esSeccionCrud = isCrud || archivoActual.includes('_crud.php') || archivoActual.includes('_agregar.php') || archivoActual.includes('_editar.php');

        // Enlaces principales
        let htmlEnlaces = '';
        ENLACES_NAV.forEach(enlace => {
            let esActivo = false;
            if (enlace.id === 'inicio' && esInicio) {
                esActivo = true;
            } else if (enlace.match && enlace.match.includes(archivoActual)) {
                esActivo = true;
            }

            const claseActivo = esActivo ? ' class="activo"' : '';
            const targetAttr = enlace.external ? ' target="_blank" rel="noopener noreferrer"' : '';
            htmlEnlaces += `<a href="${enlace.url}"${claseActivo}${targetAttr}>${enlace.texto}</a>\n`;
        });

        // Enlace de Login (solo si no ha iniciado sesión)
        if (!usuario) {
            const loginActivo = archivoActual === 'registro_login.php' ? ' class="activo nav-auth-toggle"' : ' class="nav-auth-toggle"';
            htmlEnlaces += `<a href="${crudPrefix}registro_login.php"${loginActivo}>LOGIN</a>\n`;
        }

        // Submenú desplegable CRUD (sin emojis, 2 columnas)
        let htmlDropdownCrud = '';
        if (esAdmin) {
            let htmlCardsCrud = '';
            ENLACES_CRUD.forEach(item => {
                const itemActivo = archivoActual.includes(item.match) ? ' activo' : '';
                const fullClass = item.full ? ' subnav-card-full' : '';
                htmlCardsCrud += `
                    <a href="${item.url}" class="subnav-card${fullClass}${itemActivo}">
                        <span class="subnav-num">${item.num}</span>
                        <div class="subnav-text">
                            <span class="subnav-name">${item.texto}</span>
                            <span class="subnav-desc">${item.desc}</span>
                        </div>
                    </a>
                `;
            });

            const dropbtnActivo = esSeccionCrud ? ' activo' : '';
            htmlDropdownCrud = `
                <div class="dropdown admin-visible" id="eclat-nav-dropdown">
                    <button type="button" class="dropbtn${dropbtnActivo}" id="eclat-dropbtn" aria-haspopup="true" aria-expanded="false">
                        CRUD
                    </button>
                    <div class="dropdown-content subnav-dropdown" id="eclat-dropdown-content">
                        <div class="subnav-header">
                            <span class="subnav-badge">ADMIN</span>
                            <span class="subnav-title">Módulos del Sistema</span>
                        </div>
                        <div class="subnav-grid">
                            ${htmlCardsCrud}
                        </div>
                        <div class="subnav-footer">
                            <span>Sistema de Gestión Éclat Visage</span>
                        </div>
                    </div>
                </div>
            `;
        }

        // Sección derecha: Usuario y Bolsa
        let htmlUsuario = '';
        if (usuario) {
            const nombrePila = (usuario.nombre || usuario.usuario || 'Usuario').split(' ')[0];
            // const badgeAdmin = esAdmin ? '<span class="badge-admin-nav">ADMIN</span>' : '';
            htmlUsuario = `
                <div class="auth-header-wrapper">
                    <span class="auth-user-name">
                        ${nombrePila}
                    </span>
                    <button type="button" class="btn-logout-nav" id="btn-icono-logout" title="Cerrar sesión actual">
                        Cerrar sesión
                    </button>
                </div>
            `;
        } else {
            htmlUsuario = `
                <a href="${crudPrefix}registro_login.php" class="auth-login-link" title="Iniciar Sesión">
                    Ingresar
                </a>
            `;
        }

        // Header completo
        const headerHTML = `
            <div class="logo">
                <a href="${rootPrefix}index.html">eclat visage</a>
            </div>
            <nav class="nav" id="eclat-main-nav">
                ${htmlEnlaces}
                ${htmlDropdownCrud}
            </nav>
            <div class="header-iconos">
                ${htmlUsuario}
                <span id="contador-carrito" title="Ver bolsa de compras (${cantidadCarrito} productos)">Carrito (${cantidadCarrito})</span>
            </div>
        `;

        // Ubicar o reemplazar contenedor
        let contenedor = document.getElementById('navbar-modulo');
        let headerExistente = document.querySelector('header.header, .header');

        if (contenedor) {
            let header = document.createElement('header');
            header.className = 'header';
            header.id = 'eclat-header-principal';
            header.innerHTML = headerHTML;
            contenedor.replaceWith(header);
        } else if (headerExistente) {
            headerExistente.className = 'header';
            headerExistente.id = 'eclat-header-principal';
            headerExistente.innerHTML = headerHTML;
        } else {
            let header = document.createElement('header');
            header.className = 'header';
            header.id = 'eclat-header-principal';
            header.innerHTML = headerHTML;
            document.body.insertBefore(header, document.body.firstChild);
        }

        // Vincular interactividad estricta de clic
        vincularEventosNav();
    }

    // Vincular interactividad: Clic estricto en dropdown (sin hover, sin desincronización)
    function vincularEventosNav() {
        // 1. Clic en Cerrar Sesión
        const btnLogout = document.getElementById('btn-icono-logout');
        if (btnLogout) {
            btnLogout.onclick = function (e) {
                e.preventDefault();
                if (typeof window.cerrarSesionUsuario === 'function') {
                    window.cerrarSesionUsuario(e);
                } else {
                    localStorage.removeItem('eclat_usuario');
                    document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/CRUD;";
                    document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
                    window.location.href = `${rootPrefix}logout.php`;
                }
            };
        }

        // 2. Control de clic estricto en el Dropdown CRUD
        const dropbtn = document.getElementById('eclat-dropbtn');
        const dropdown = document.getElementById('eclat-nav-dropdown');
        const dropdownContent = document.getElementById('eclat-dropdown-content');

        if (dropbtn && dropdown) {
            // Asegurar que comience estrictamente cerrado en cada render / recarga
            dropdown.classList.remove('abierto');
            dropdown.style.removeProperty('display');
            if (dropdownContent) {
                dropdownContent.style.removeProperty('display');
            }
            dropbtn.setAttribute('aria-expanded', 'false');

            // Clic en el botón: alterna limpiamente abierto / cerrado
            dropbtn.onclick = function (e) {
                e.preventDefault();
                e.stopPropagation();

                const estabaAbierto = dropdown.classList.contains('abierto');
                if (estabaAbierto) {
                    dropdown.classList.remove('abierto');
                    dropbtn.setAttribute('aria-expanded', 'false');
                } else {
                    dropdown.classList.add('abierto');
                    dropbtn.setAttribute('aria-expanded', 'true');
                }
            };

            // Cerrar al hacer clic por fuera
            document.onclick = function (e) {
                if (dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('abierto');
                    dropbtn.setAttribute('aria-expanded', 'false');
                }
            };

            // Cerrar con tecla Escape
            document.onkeydown = function (e) {
                if (e.key === 'Escape' || e.key === 'Esc') {
                    dropdown.classList.remove('abierto');
                    dropbtn.setAttribute('aria-expanded', 'false');
                }
            };

            // Cerrar al hacer clic en cualquier opción interna
            const opciones = dropdown.querySelectorAll('.subnav-card');
            opciones.forEach(op => {
                op.onclick = function () {
                    dropdown.classList.remove('abierto');
                    dropbtn.setAttribute('aria-expanded', 'false');
                };
            });
        }

        // 3. Clic en la Bolsa / Carrito
        const btnCarrito = document.getElementById('contador-carrito');
        if (btnCarrito) {
            btnCarrito.onclick = function (e) {
                e.preventDefault();
                if (typeof window.abrirCarrito === 'function') {
                    window.abrirCarrito();
                } else {
                    window.location.href = `${rootPrefix}carrito.html`;
                }
            };
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderizarNavbar);
    } else {
        renderizarNavbar();
    }

    window.actualizarNavbarModulo = renderizarNavbar;

})();
