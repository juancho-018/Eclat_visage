/**
 * ECLAT VISAGE — Sistema de Carrito de Compras & Autenticación
 * Manejo de estado persistente ligado al usuario autenticado,
 * control de sesión con Bcrypt, redirección al login/registro,
 * modal lateral (drawer), selector de cantidades y botón de cerrar sesión.
 */

// Catálogo base de productos con precios e imágenes oficiales
const ECLAT_CATALOGO = {
    "Sérum Hidratante Profundo": { precio: 18000, img: ".img/serum-hidratante.jpg", categoria: "Sérums" },
    "Elíxir Iluminador Vitamina C": { precio: 24000, img: ".img/serum-vitc.jpg", categoria: "Sérums" },
    "Sérum Renovador Nocturno": { precio: 32000, img: ".img/serum-noche.jpg", categoria: "Sérums" },
    "Sérum de Niacinamida 10%": { precio: 25500, img: ".img/serum.jpg", categoria: "Sérums" },
    "Contorno de Ojos": { precio: 22000, img: ".img/CONTORNO_OJOS.jpg", categoria: "Contorno" },
    "Tónico Botánico Calmante": { precio: 14000, img: ".img/tonico-calmante.jpg", categoria: "Tónicos" },
    "Crema Nutritiva de Día": { precio: 28000, img: ".img/crema-dia.jpg", categoria: "Cremas" },
    "Crema de Brillo Vitamina C": { precio: 26000, img: ".img/crema-brillo.jpg", categoria: "Cremas" },
    "Bálsamo Reparador Intenso": { precio: 30000, img: ".img/balsamo-reparador.jpg", categoria: "Cremas" },
    "Dúo Nutrición Esencial": { precio: 48000, img: ".img/duo-esencial.jpg", categoria: "Combos" },
    "Menú Degustación Completo": { precio: 85000, img: ".img/menu-completo.jpg", categoria: "Combos" },
    "Gel Hidratante de Aloe Vera": { precio: 15000, img: ".img/gel-aloe.jpg", categoria: "Geles" },
    "Mascarilla Ultra Nocturna": { precio: 29000, img: ".img/mascarilla-facial.jpg", categoria: "Mascarillas" },
    "Kit de Viaje Cuidado Diario": { precio: 35000, img: ".img/kit-viaje.jpg", categoria: "Kits" },
    "Espuma Limpiadora Suave": { precio: 19500, img: ".img/limpiador-facial.jpg", categoria: "Limpieza" },
    "Gel Purificante de Árbol de Té": { precio: 21000, img: ".img/gel-te.jpg", categoria: "Geles" },
    "Mascarilla de Arcilla Cremosa": { precio: 16000, img: ".img/crema-noche.jpg", categoria: "Mascarillas" },
    "Crema Exfoliante Micro-gránulos": { precio: 17000, img: ".img/crema-exfoliante.jpg", categoria: "Exfoliantes" },
    "Serum hidratante 30ml": { precio: 18000, img: ".img/serum.jpg", categoria: "Sérums" },
    "Crema facial 50ml": { precio: 28000, img: ".img/crema.jpg", categoria: "Cremas" },
    "Crema de brillo con Vitamina C": { precio: 26000, img: ".img/crema2.jpg", categoria: "Cremas" },
    "Combo de Serum y Crema Hidratante": { precio: 48000, img: ".img/combo.jpg", categoria: "Combos" }
};

const META_ENVIO_GRATIS = 100000;
const COSTO_ENVIO_ESTANDAR = 10000;

/**
 * Determina la ruta base relativa según si la página está en el directorio /CRUD/ o en la raíz.
 */
function getRutaBase() {
    const path = (window.location && window.location.pathname) ? window.location.pathname.replace(/\\/g, '/') : '';
    return /\/crud\//i.test(path) ? '../' : '';
}

// Auto-cargar SweetAlert2 si no está presente en la página
(function cargarSweetAlert() {
    if (typeof Swal === 'undefined' && !document.querySelector('script[src*="sweetalert2"]')) {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(s);
    }
})();

// Helper para alertas SweetAlert2 con estilos corporativos Éclat Visage
function eclatAlerta(opciones) {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            background: '#fdfbf7',
            confirmButtonColor: '#3d352a',
            cancelButtonColor: '#8c7a6b',
            color: '#3d352a',
            ...opciones
        });
    } else {
        if (opciones.showCancelButton) {
            const res = confirm(opciones.text || opciones.title);
            return Promise.resolve({ isConfirmed: res });
        } else {
            alert((opciones.title ? opciones.title + '\n' : '') + (opciones.text || ''));
            return Promise.resolve({ isConfirmed: true });
        }
    }
}

// ----------------------------------------------------
// 1. AUTENTICACIÓN Y SESIÓN DEL USUARIO
// ----------------------------------------------------
function obtenerUsuarioAutenticado() {
    // 1. Verificar cookie 'eclat_usuario'
    const match = document.cookie.match(new RegExp('(^| )eclat_usuario=([^;]+)'));
    if (match) {
        try {
            const decoded = decodeURIComponent(match[2]);
            const parsed = JSON.parse(decoded);
            if (parsed && parsed.usuario) {
                // Sincronizar en localStorage por consistencia
                localStorage.setItem('eclat_usuario', JSON.stringify(parsed));
                return parsed;
            }
        } catch (e) {}
    }

    // 2. Verificar localStorage 'eclat_usuario'
    try {
        const local = localStorage.getItem('eclat_usuario');
        if (local) {
            const parsed = JSON.parse(local);
            if (parsed && parsed.usuario) {
                return parsed;
            }
        }
    } catch (e) {}

    return null;
}

// Clave única de almacenamiento del carrito ligada al usuario
function obtenerStorageKey() {
    const usuario = obtenerUsuarioAutenticado();
    if (usuario && usuario.usuario) {
        return `eclat_visage_carrito_${usuario.usuario}`;
    }
    return 'eclat_visage_carrito_invitado';
}

// Migrar productos de invitado a la cuenta del usuario si acaba de iniciar sesión
function migrarCarritoInvitado() {
    const usuario = obtenerUsuarioAutenticado();
    if (!usuario) return;

    try {
        const invitadoKey = 'eclat_visage_carrito_invitado';
        const usuarioKey = `eclat_visage_carrito_${usuario.usuario}`;
        const itemsInvitado = JSON.parse(localStorage.getItem(invitadoKey) || '[]');
        
        if (itemsInvitado.length > 0) {
            const itemsUsuario = JSON.parse(localStorage.getItem(usuarioKey) || '[]');
            
            // Unir items
            itemsInvitado.forEach(itemInv => {
                const idx = itemsUsuario.findIndex(p => p.nombre.toLowerCase() === itemInv.nombre.toLowerCase());
                if (idx > -1) {
                    itemsUsuario[idx].cantidad += itemInv.cantidad;
                } else {
                    itemsUsuario.push(itemInv);
                }
            });

            localStorage.setItem(usuarioKey, JSON.stringify(itemsUsuario));
            localStorage.removeItem(invitadoKey);
        }
    } catch (e) {
        console.error("Error al migrar carrito:", e);
    }
}

// Cerrar sesión del usuario con SweetAlert2
function cerrarSesionUsuario(e) {
    if (e && e.preventDefault) e.preventDefault();
    
    const ejecutarLogoutInmediato = () => {
        localStorage.removeItem('eclat_usuario');
        document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/CRUD;";
        document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
        const rutaBase = getRutaBase();
        window.location.href = `${rutaBase}logout.php`;
    };

    eclatAlerta({
        title: '¿Cerrar Sesión?',
        text: 'Tu carrito y datos permanecerán guardados para cuando vuelvas a ingresar.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Permanecer conectado',
        confirmButtonColor: '#a85d5d',
        cancelButtonColor: '#8c7a6b'
    }).then((result) => {
        if (result && result.isConfirmed) {
            ejecutarLogoutInmediato();
        }
    }).catch(() => {
        ejecutarLogoutInmediato();
    });
}

// ----------------------------------------------------
// 2. GESTIÓN DEL CARRITO (PERSISTENCIA Y TOTALES)
// ----------------------------------------------------
function formatearMoneda(valor) {
    return '$' + Number(valor || 0).toLocaleString('es-CO');
}

function obtenerCarrito() {
    try {
        const key = obtenerStorageKey();
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : [];
    } catch (e) {
        console.error("Error al leer el carrito:", e);
        return [];
    }
}

function guardarCarrito(carrito) {
    try {
        const key = obtenerStorageKey();
        localStorage.setItem(key, JSON.stringify(carrito));
    } catch (e) {
        console.error("Error al guardar carrito:", e);
    }
    actualizarContadores();
    renderizarDrawer();
    if (typeof window.renderizarPaginaCarrito === 'function') {
        window.renderizarPaginaCarrito();
    }
}

function calcularTotales(carrito) {
    let cantidadTotal = 0;
    let subtotal = 0;

    carrito.forEach(item => {
        cantidadTotal += item.cantidad;
        subtotal += item.precio * item.cantidad;
    });

    const envio = subtotal >= META_ENVIO_GRATIS || subtotal === 0 ? 0 : COSTO_ENVIO_ESTANDAR;
    const total = subtotal + envio;
    const faltaParaEnvioGratis = Math.max(0, META_ENVIO_GRATIS - subtotal);

    return { cantidadTotal, subtotal, envio, total, faltaParaEnvioGratis };
}

function actualizarContadores() {
    const carrito = obtenerCarrito();
    const { cantidadTotal } = calcularTotales(carrito);
    
    const contadores = document.querySelectorAll('#contador-carrito, .contador-carrito-badge');
    contadores.forEach(el => {
        el.innerText = `🛒 (${cantidadTotal})`;
        el.setAttribute('title', `Ver carrito (${cantidadTotal} productos)`);
        el.style.cursor = 'pointer';
    });

    const drawerBadge = document.getElementById('drawer-total-articulos');
    if (drawerBadge) {
        drawerBadge.innerText = `${cantidadTotal} ${cantidadTotal === 1 ? 'producto' : 'productos'}`;
    }
}

// ----------------------------------------------------
// 3. AGREGAR PRODUCTOS Y MODAL / DRAWER
// ----------------------------------------------------
function agregarAlCarrito(nombre, precioParam, imagenParam) {
    const carrito = obtenerCarrito();
    const infoCatalogo = ECLAT_CATALOGO[nombre] || {};

    const precio = Number(precioParam) || infoCatalogo.precio || 20000;
    const imagen = imagenParam || infoCatalogo.img || '.img/serum.jpg';

    const index = carrito.findIndex(p => p.nombre.toLowerCase().trim() === nombre.toLowerCase().trim());

    if (index > -1) {
        carrito[index].cantidad += 1;
    } else {
        carrito.push({
            nombre: nombre,
            precio: precio,
            imagen: imagen,
            cantidad: 1
        });
    }

    guardarCarrito(carrito);
    mostrarToast(`✨ ¡"${nombre}" añadido a tu bolsa!`);
}

window.agregar = agregarAlCarrito;
window.agregarAlCarrito = agregarAlCarrito;

function cambiarCantidad(nombre, delta) {
    const carrito = obtenerCarrito();
    const index = carrito.findIndex(p => p.nombre === nombre);

    if (index > -1) {
        carrito[index].cantidad += delta;
        if (carrito[index].cantidad <= 0) {
            carrito.splice(index, 1);
            mostrarToast(`Producto eliminado de la bolsa`, 'info');
        }
        guardarCarrito(carrito);
    }
}

function eliminarProducto(nombre) {
    let carrito = obtenerCarrito();
    carrito = carrito.filter(p => p.nombre !== nombre);
    guardarCarrito(carrito);
    mostrarToast(`Producto retirado`, 'info');
}

function vaciarCarrito() {
    eclatAlerta({
        title: '¿Vaciar tu bolsa de compras?',
        text: 'Se eliminarán todos los productos que habías seleccionado.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, vaciar bolsa',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#a85d5d',
        cancelButtonColor: '#8c7a6b'
    }).then((result) => {
        if (result.isConfirmed) {
            guardarCarrito([]);
            mostrarToast('La bolsa de compras ha sido vaciada', 'info');
        }
    });
}

/**
 * ABRIR CARRITO:
 * REQUISITO: Verificar autenticación del usuario.
 * Si NO está autenticado -> Redirige automáticamente al Login (que incluye aviso de registro).
 * Si SÍ está autenticado -> Abre el drawer vinculado a su cuenta.
 */
function abrirCarrito() {
    const usuario = obtenerUsuarioAutenticado();
    const rutaBase = getRutaBase();

    if (!usuario) {
        eclatAlerta({
            title: 'Acceso a Tu Carrito',
            text: 'Para ver tu bolsa de compras y guardar tus productos, por favor inicia sesión o regístrate.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Iniciar Sesión',
            cancelButtonText: 'Seguir mirando',
            confirmButtonColor: '#3d352a',
            cancelButtonColor: '#8c7a6b'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${rutaBase}CRUD/registro_login.php?redirect=carrito`;
            }
        });
        return;
    }

    renderizarDrawer();
    const drawer = document.getElementById('eclat-carrito-drawer');
    const overlay = document.getElementById('eclat-carrito-overlay');
    if (drawer && overlay) {
        drawer.classList.add('activo');
        overlay.classList.add('activo');
        document.body.style.overflow = 'hidden';
    }
}

function cerrarCarrito() {
    const drawer = document.getElementById('eclat-carrito-drawer');
    const overlay = document.getElementById('eclat-carrito-overlay');
    if (drawer && overlay) {
        drawer.classList.remove('activo');
        overlay.classList.remove('activo');
        document.body.style.overflow = '';
    }
}

// ----------------------------------------------------
// 4. RENDERIZADO DEL DRAWER LATERAL
// ----------------------------------------------------
function renderizarDrawer() {
    const itemsCont = document.getElementById('drawer-items-contenedor');
    const footerCont = document.getElementById('drawer-footer-contenedor');
    const alertaEnvio = document.getElementById('drawer-barra-envio');
    const userBanner = document.getElementById('drawer-user-banner');
    if (!itemsCont || !footerCont) return;

    const usuario = obtenerUsuarioAutenticado();
    const carrito = obtenerCarrito();
    const { cantidadTotal, subtotal, envio, total, faltaParaEnvioGratis } = calcularTotales(carrito);
    const rutaBase = getRutaBase();

    // Actualizar banner del usuario autenticado
    if (userBanner && usuario) {
        userBanner.innerHTML = `
            <div class="drawer-user-badge">
                <span>  <strong>${usuario.nombre || usuario.usuario}</strong></span>
                <button type="button" class="btn-cerrar-sesion-pill" onclick="cerrarSesionUsuario(event)" title="Cerrar sesión">
                    Cerrar sesión
                </button>
            </div>
        `;
    }

    // Barra de progreso envío gratis
    if (alertaEnvio) {
        if (subtotal === 0) {
            alertaEnvio.innerHTML = `
                <div class="envio-msj">📦 <strong>Envío Gratis</strong> en compras superiores a ${formatearMoneda(META_ENVIO_GRATIS)}</div>
            `;
        } else if (faltaParaEnvioGratis === 0) {
            alertaEnvio.innerHTML = `
                <div class="envio-msj exito">🎉 ¡Felicidades! Tienes <strong>Envío Gratis</strong> en este pedido</div>
                <div class="envio-barra-bg"><div class="envio-barra-fill" style="width: 100%;"></div></div>
            `;
        } else {
            const porcentaje = Math.min(100, Math.round((subtotal / META_ENVIO_GRATIS) * 100));
            alertaEnvio.innerHTML = `
                <div class="envio-msj">Agrega <strong>${formatearMoneda(faltaParaEnvioGratis)}</strong> más para <strong>Envío Gratis</strong></div>
                <div class="envio-barra-bg"><div class="envio-barra-fill" style="width: ${porcentaje}%;"></div></div>
            `;
        }
    }

    // Renderizar productos o estado vacío
    if (carrito.length === 0) {
        itemsCont.innerHTML = `
            <div class="carrito-vacio-box">
                <div class="icono-vacio">🧴✨</div>
                <h4>Tu carrito está vacío</h4>
                <p>Descubre el menú de nutrición facial botánica que diseñamos para consentir tu piel.</p>
                <a href="${rutaBase}tienda.html" class="btn-ir-tienda" onclick="cerrarCarrito()">Explorar la Tienda</a>
            </div>
        `;
        footerCont.style.display = 'none';
        return;
    }

    footerCont.style.display = 'block';

    let htmlItems = '';
    carrito.forEach(item => {
        const itemImgSrc = item.imagen.startsWith('http') || item.imagen.startsWith('/') 
            ? item.imagen 
            : `${rutaBase}${item.imagen.replace(/^\.?\//, '')}`;

        const itemSubtotal = item.precio * item.cantidad;

        htmlItems += `
            <div class="drawer-item-card">
                <img src="${itemImgSrc}" alt="${item.nombre}" class="drawer-item-thumb" onerror="this.src='${rutaBase}.img/serum.jpg'">
                <div class="drawer-item-detalles">
                    <h4 class="drawer-item-titulo">${item.nombre}</h4>
                    <div class="drawer-item-precio">${formatearMoneda(item.precio)} c/u</div>
                    <div class="drawer-item-acciones">
                        <div class="cantidad-control">
                            <button type="button" onclick="cambiarCantidad('${item.nombre.replace(/'/g, "\\'")}', -1)" title="Disminuir">−</button>
                            <span class="cantidad-num">${item.cantidad}</span>
                            <button type="button" onclick="cambiarCantidad('${item.nombre.replace(/'/g, "\\'")}', 1)" title="Aumentar">+</button>
                        </div>
                        <button type="button" class="btn-eliminar-text" onclick="eliminarProducto('${item.nombre.replace(/'/g, "\\'")}')" title="Eliminar del carrito">
                            🗑 Quitar
                        </button>
                    </div>
                </div>
                <div class="drawer-item-subtotal">
                    ${formatearMoneda(itemSubtotal)}
                </div>
            </div>
        `;
    });

    itemsCont.innerHTML = htmlItems;

    // Resumen y botones del footer
    footerCont.innerHTML = `
        <div class="drawer-resumen">
            <div class="resumen-linea">
                <span>Subtotal:</span>
                <strong>${formatearMoneda(subtotal)}</strong>
            </div>
            <div class="resumen-linea">
                <span>Envío estimado:</span>
                <span>${envio === 0 ? '<strong style="color:#2e7d32;">GRATIS</strong>' : formatearMoneda(envio)}</span>
            </div>
            <div class="resumen-linea total-destacado">
                <span>Total a Pagar:</span>
                <strong>${formatearMoneda(total)} COP</strong>
            </div>
        </div>
        <div class="drawer-botones">
            <button type="button" class="btn-drawer-checkout" onclick="abrirCheckoutModal()">
                Finalizar Compra y Facturar ✦
            </button>
            <a href="${rutaBase}carrito.html" class="btn-drawer-ver-todo" onclick="cerrarCarrito()">
                Ver Carrito Detallado
            </a>
            <button type="button" class="btn-drawer-whatsapp" onclick="enviarPedidoWhatsApp()">
                📱 Pedir por WhatsApp
            </button>
            <button type="button" class="btn-drawer-vaciar" onclick="vaciarCarrito()">
                Vaciar Carrito
            </button>
        </div>
    `;
}

// Envío a WhatsApp con detalles del usuario autenticado
function enviarPedidoWhatsApp() {
    const carrito = obtenerCarrito();
    if (carrito.length === 0) {
        eclatAlerta({
            title: 'Bolsa Vacía',
            text: 'Agrega al menos un producto antes de realizar el pedido.',
            icon: 'info',
            confirmButtonColor: '#3d352a'
        });
        return;
    }

    const usuario = obtenerUsuarioAutenticado();
    const nombreCliente = usuario ? (usuario.nombre || usuario.usuario) : "Cliente Éclat";
    const { subtotal, envio, total } = calcularTotales(carrito);
    
    let mensaje = `✨ *¡Hola Éclat Visage! Deseo realizar este pedido:* ✨\n`;
    mensaje += `  *Cliente:* ${nombreCliente}\n\n`;

    carrito.forEach((p, idx) => {
        mensaje += `*${idx + 1}.* ${p.nombre} x${p.cantidad} — ${formatearMoneda(p.precio * p.cantidad)}\n`;
    });
    
    mensaje += `\n📦 *Subtotal:* ${formatearMoneda(subtotal)}`;
    mensaje += `\n🚚 *Envío:* ${envio === 0 ? 'GRATIS' : formatearMoneda(envio)}`;
    mensaje += `\n💳 *TOTAL A PAGAR:* ${formatearMoneda(total)} COP\n`;
    mensaje += `\n📍 *Por favor indíquenme medios de pago para el despacho.* Muchas gracias.`;

    const telefono = "573229795322";
    const url = `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;
    window.open(url, '_blank');
}

// Notificación flotante Toast
function mostrarToast(mensaje, tipo = 'success') {
    let contenedor = document.getElementById('eclat-toast-contenedor');
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = 'eclat-toast-contenedor';
        contenedor.className = 'eclat-toast-contenedor';
        document.body.appendChild(contenedor);
    }

    const toast = document.createElement('div');
    toast.className = `eclat-toast ${tipo}`;
    toast.innerHTML = `
        <span class="toast-texto">${mensaje}</span>
        <button class="toast-btn-ver" onclick="abrirCarrito()">Ver Carrito</button>
    `;

    contenedor.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('visible');
    }, 10);

    setTimeout(() => {
        toast.classList.remove('visible');
        setTimeout(() => toast.remove(), 400);
    }, 3800);
}

// ----------------------------------------------------
// 5. ACTUALIZACIÓN DINÁMICA DEL ENCABEZADO (AUTH & BOTÓN CERRAR SESIÓN)
// ----------------------------------------------------
function sincronizarHeaderAuth() {
    const usuario = obtenerUsuarioAutenticado();
    const rutaBase = getRutaBase();
    // Requisito estricto: único que puede ver el CRUD es quien tenga rol == 'admin'
    const esAdmin = Boolean(usuario && usuario.rol && String(usuario.rol).toLowerCase().trim() === 'admin');

    // 1. REQUISITO: Si el usuario ya está autenticado, NO mostrar botón para iniciar sesión;
    // mostrar en su lugar botón para 'CERRAR SESIÓN'. Si no está autenticado, mostrar 'LOGIN'.
    const navLinks = document.querySelectorAll('.nav a, .header .nav a, header nav a');
    let authNavFound = false;

    navLinks.forEach(link => {
        const txt = link.innerText.trim().toUpperCase();
        const href = (link.getAttribute('href') || '').toLowerCase();

        if (txt === 'LOGIN' || txt === 'INICIAR SESIÓN' || txt === 'CERRAR SESIÓN' || href.includes('registro_login.php') || link.classList.contains('nav-auth-toggle')) {
            authNavFound = true;
            link.classList.add('nav-auth-toggle');

            if (usuario) {
                // Ya está autenticado -> NO SALE MÁS BOTÓN PARA INICIAR SESIÓN; SALE BOTÓN CERRAR SESIÓN
                link.innerText = 'CERRAR SESIÓN';
                link.href = '#';
                link.title = 'Cerrar sesión actual';
                link.style.color = '#a85d5d';
                link.style.fontWeight = '600';
                link.onclick = function(e) {
                    e.preventDefault();
                    cerrarSesionUsuario(e);
                };
            } else {
                // No está autenticado -> SALE BOTÓN DE LOGIN
                link.innerText = 'LOGIN';
                link.href = `${rutaBase}CRUD/registro_login.php`;
                link.title = 'Iniciar sesión';
                link.style.color = '';
                link.style.fontWeight = '';
                link.onclick = null;
            }
        }
    });

    // Si la barra no tenía enlace de login previo, asegurarlo según el estado
    if (!authNavFound) {
        const navBars = document.querySelectorAll('.nav, nav.nav, .header .nav');
        navBars.forEach(nav => {
            let dynLink = nav.querySelector('.nav-auth-toggle');
            if (!dynLink) {
                dynLink = document.createElement('a');
                dynLink.className = 'nav-auth-toggle';
                nav.appendChild(dynLink);
            }
            if (usuario) {
                dynLink.innerText = 'CERRAR SESIÓN';
                dynLink.href = '#';
                dynLink.style.color = '#a85d5d';
                dynLink.style.fontWeight = '600';
                dynLink.onclick = function(e) {
                    e.preventDefault();
                    cerrarSesionUsuario(e);
                };
            } else {
                dynLink.innerText = 'LOGIN';
                dynLink.href = `${rutaBase}CRUD/registro_login.php`;
                dynLink.style.color = '';
                dynLink.style.fontWeight = '';
                dynLink.onclick = null;
            }
        });
    }

    // 2. REQUISITO ESTRICTO: NO MOSTRAR EL BOTÓN CRUD SI NO ES ROL == ADMIN
    if (esAdmin) {
        document.body.classList.add('es-admin');
    } else {
        document.body.classList.remove('es-admin');
    }

    const crudDropdowns = document.querySelectorAll('.dropdown, .nav .dropdown, header .dropdown, [class*="dropdown"]');
    crudDropdowns.forEach(dd => {
        if (esAdmin) {
            dd.classList.add('admin-visible');
            dd.style.setProperty('display', 'inline-block', 'important');
        } else {
            dd.classList.remove('admin-visible');
            dd.style.setProperty('display', 'none', 'important');
        }
    });

    // Ocultar cualquier botón o enlace que contenga la palabra CRUD o enlaces directos al CRUD si no es admin
    const todosLosEnlacesNav = document.querySelectorAll('.nav a, header a, .header a');
    todosLosEnlacesNav.forEach(a => {
        const txt = a.innerText.trim().toUpperCase();
        const href = (a.getAttribute('href') || '').toLowerCase();
        if (txt.includes('CRUD') || href.includes('_crud.php')) {
            const contenedorDropdown = a.closest('.dropdown');
            if (contenedorDropdown) {
                if (esAdmin) {
                    contenedorDropdown.style.setProperty('display', 'inline-block', 'important');
                } else {
                    contenedorDropdown.style.setProperty('display', 'none', 'important');
                }
            } else {
                if (esAdmin) {
                    a.style.removeProperty('display');
                } else {
                    a.style.setProperty('display', 'none', 'important');
                }
            }
        }
    });

    // 3. Inyectar o actualizar bloque de usuario en .header-iconos
    const iconContainers = document.querySelectorAll('.header-iconos, .header-icons');
    iconContainers.forEach(container => {
        const existingAuthBox = container.querySelector('.auth-header-wrapper');
        if (existingAuthBox) existingAuthBox.remove();

        const authWrapper = document.createElement('div');
        authWrapper.className = 'auth-header-wrapper';
        authWrapper.style.display = 'inline-flex';
        authWrapper.style.alignItems = 'center';
        authWrapper.style.gap = '8px';

        if (usuario) {
            const adminPill = esAdmin ? '<span style="font-size:10px; background:#bc9c82; color:#fff; padding:2px 6px; border-radius:10px; font-weight:600;">ADMIN</span>' : '';
            authWrapper.innerHTML = `
                <span style="font-size: 12px; color: #5a4a42; font-weight: 500;">
                      <strong>${(usuario.nombre || usuario.usuario).split(' ')[0]}</strong> ${adminPill}
                </span>
                <button type="button" class="btn-logout-nav" onclick="cerrarSesionUsuario(event)" title="Cerrar sesión">
                    Cerrar sesión
                </button>
            `;
        } else {
            authWrapper.innerHTML = `
                <a href="${rutaBase}CRUD/registro_login.php" title="Iniciar Sesión" style="text-decoration:none; color:inherit; font-size:14px;">
                     
                </a>
            `;
        }

        const cartElement = container.querySelector('#contador-carrito, span:last-child');
        if (cartElement) {
            container.insertBefore(authWrapper, cartElement);
        } else {
            container.appendChild(authWrapper);
        }
    });

    // Inicializar listeners interactivos de los dropdowns (abrir/cerrar al clic, clic fuera, ESC)
    inicializarDropdownsCRUD();
}

// ----------------------------------------------------
// 5.1 CONTROL INTERACTIVO DEL DROPDOWN CRUD
// Permite abrir y CERRAR al hacer clic en CRUD ▾, al dar clic
// por fuera en la pantalla, al mover el mouse o al pulsar ESC.
// ----------------------------------------------------
function inicializarDropdownsCRUD() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dd => {
        const btn = dd.querySelector('.dropbtn');
        const content = dd.querySelector('.dropdown-content');
        if (!btn || !content) return;

        // Evitar que el texto se resalte en azul al hacer doble clic
        btn.style.userSelect = 'none';
        btn.style.webkitUserSelect = 'none';

        if (dd.dataset.dropdownConfigured === 'true') return;
        dd.dataset.dropdownConfigured = 'true';

        // 1. Clic en el botón CRUD ▾: si está abierto lo CIERRA; si está cerrado lo ABRE
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const estaAbierto = dd.classList.contains('abierto') || 
                               (!dd.classList.contains('cerrado-forzado') && window.getComputedStyle(content).display !== 'none');

            // Cerrar cualquier otro dropdown
            document.querySelectorAll('.dropdown').forEach(otro => {
                otro.classList.remove('abierto');
                otro.classList.add('cerrado-forzado');
                const c = otro.querySelector('.dropdown-content');
                if (c) c.style.setProperty('display', 'none', 'important');
            });

            if (estaAbierto) {
                // Ya estaba abierto -> SE CIERRA INMEDIATAMENTE
                dd.classList.remove('abierto');
                dd.classList.add('cerrado-forzado');
                content.style.setProperty('display', 'none', 'important');
            } else {
                // Estaba cerrado -> SE ABRE
                dd.classList.remove('cerrado-forzado');
                dd.classList.add('abierto');
                content.style.setProperty('display', 'block', 'important');
            }
        });

        // 2. Al entrar el mouse (hover intuitivo)
        dd.addEventListener('mouseenter', function() {
            if (!dd.classList.contains('cerrado-forzado')) {
                dd.classList.add('abierto');
                content.style.setProperty('display', 'block', 'important');
            }
        });

        // 3. Al salir el mouse (mouseleave -> SE CIERRA AUTOMÁTICAMENTE)
        dd.addEventListener('mouseleave', function() {
            dd.classList.remove('abierto');
            dd.classList.remove('cerrado-forzado');
            content.style.setProperty('display', 'none', 'important');
        });

        // 4. Al hacer clic en cualquiera de las opciones internas -> SE CIERRA
        content.querySelectorAll('a').forEach(opcion => {
            opcion.addEventListener('click', function() {
                dd.classList.remove('abierto');
                dd.classList.add('cerrado-forzado');
                content.style.setProperty('display', 'none', 'important');
            });
        });
    });
}

// Cierre global por fuera de cualquier dropdown
if (!window._dropdownGlobalListenersAdded) {
    window._dropdownGlobalListenersAdded = true;

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dd => {
                dd.classList.remove('abierto');
                dd.classList.add('cerrado-forzado');
                const content = dd.querySelector('.dropdown-content');
                if (content) content.style.setProperty('display', 'none', 'important');
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            document.querySelectorAll('.dropdown').forEach(dd => {
                dd.classList.remove('abierto');
                dd.classList.add('cerrado-forzado');
                const content = dd.querySelector('.dropdown-content');
                if (content) content.style.setProperty('display', 'none', 'important');
            });
        }
    });
}

// ----------------------------------------------------
// 6. INICIALIZACIÓN COMPLETA
// ----------------------------------------------------
function inicializarSistemaCarrito() {
    migrarCarritoInvitado();
    sincronizarHeaderAuth();
    inicializarDropdownsCRUD();

    // Inyectar Drawer si no existe
    if (!document.getElementById('eclat-carrito-drawer')) {
        const overlay = document.createElement('div');
        overlay.id = 'eclat-carrito-overlay';
        overlay.className = 'eclat-carrito-overlay';
        overlay.addEventListener('click', cerrarCarrito);

        const drawer = document.createElement('aside');
        drawer.id = 'eclat-carrito-drawer';
        drawer.className = 'eclat-carrito-drawer';
        drawer.setAttribute('aria-label', 'Carrito de compras Éclat Visage');

        drawer.innerHTML = `
            <div class="drawer-header">
                <div class="drawer-titulo-box">
                    <h3>Tu Carrito <span>✦</span></h3>
                    <span id="drawer-total-articulos" class="drawer-conteo-badge">0 productos</span>
                </div>
                <button type="button" class="btn-cerrar-drawer" onclick="cerrarCarrito()" aria-label="Cerrar carrito">✕</button>
            </div>

            <div id="drawer-user-banner" class="drawer-user-banner"></div>

            <div id="drawer-barra-envio" class="drawer-barra-envio"></div>

            <div id="drawer-items-contenedor" class="drawer-items-contenedor"></div>

            <div id="drawer-footer-contenedor" class="drawer-footer-contenedor"></div>
        `;

        document.body.appendChild(overlay);
        document.body.appendChild(drawer);
    }

    vincularClicksCarrito();
    actualizarContadores();

    // Si viene con el parámetro abrirCarrito=1 tras iniciar sesión, abrir inmediatamente
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('abrirCarrito') === '1') {
        setTimeout(abrirCarrito, 200);
    }
}

// ----------------------------------------------------
// 6. MODAL DE CHECKOUT & FACTURACIÓN AUTOMÁTICA EN BASE DE DATOS
// ----------------------------------------------------
function asegurarModalCheckoutEnDOM() {
    let modal = document.getElementById('modal-checkout');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'modal-checkout';
        modal.className = 'checkout-modal';
        modal.innerHTML = `
            <div class="checkout-card">
                <button type="button" class="btn-cerrar-modal" onclick="cerrarCheckoutModal()">✕</button>
                <h3>Datos de Entrega y Facturación</h3>
                <p style="font-size: 13px; color: #6e6456; text-align: center; margin-bottom: 20px;">
                    Tus datos se registrarán en la tabla de <strong>Clientes</strong> y se generará tu <strong>Factura</strong> automáticamente.
                </p>

                <form class="checkout-form" id="form-checkout-eclat" onsubmit="procesarCompraEclat(event)">
                    <div class="campo">
                        <label>Nombre Completo:</label>
                        <input type="text" id="chk-nombre" required placeholder="Ej: Valentina Morales">
                    </div>
                    <div class="campo">
                        <label>Correo Electrónico:</label>
                        <input type="email" id="chk-correo" required placeholder="tu_correo@ejemplo.com">
                    </div>
                    <div class="campo">
                        <label>Teléfono de Contacto (WhatsApp):</label>
                        <input type="tel" id="chk-telefono" required placeholder="Ej: 312 345 6789">
                    </div>
                    <div class="campo">
                        <label>Dirección de Envío:</label>
                        <input type="text" id="chk-direccion" required placeholder="Calle, Carrera, Apto / Casa">
                    </div>
                    <div class="campo">
                        <label>Ciudad / Municipio:</label>
                        <input type="text" id="chk-ciudad" required placeholder="Ej: Bogotá, Medellín, Armenia...">
                    </div>
                    <div class="campo">
                        <label>Método de Pago:</label>
                        <select id="chk-metodo" required>
                            <option value="Nequi">Nequi</option>
                            <option value="Bancolombia">Transferencia Bancolombia</option>
                            <option value="Tarjeta">Tarjeta Débito / Crédito</option>
                            <option value="Contraentrega">Pago Contra Entrega</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-confirmar-pedido" id="btn-submit-factura">
                        Confirmar Pedido y Facturar ✦
                    </button>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
    }
    return modal;
}

function abrirCheckoutModal() {
    const usuario = obtenerUsuarioAutenticado();
    const rutaBase = getRutaBase();

    if (!usuario) {
        eclatAlerta({
            title: 'Acceso a Facturación',
            text: 'Para registrar la factura a tu nombre y despachar tu pedido, por favor inicia sesión o regístrate.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Iniciar Sesión',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3d352a',
            cancelButtonColor: '#8c7a6b'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${rutaBase}CRUD/registro_login.php?redirect=carrito`;
            }
        });
        return;
    }

    const carrito = obtenerCarrito();
    if (carrito.length === 0) {
        eclatAlerta({
            title: 'Bolsa Vacía',
            text: 'Añade al menos un producto a tu bolsa de compras antes de facturar.',
            icon: 'warning',
            confirmButtonColor: '#3d352a'
        });
        return;
    }

    const modal = asegurarModalCheckoutEnDOM();

    // Rellenar datos si están disponibles
    const inputNombre = document.getElementById('chk-nombre');
    const inputCorreo = document.getElementById('chk-correo');
    if (inputNombre && !inputNombre.value) {
        inputNombre.value = usuario.nombre || usuario.usuario || '';
    }
    if (inputCorreo && !inputCorreo.value) {
        inputCorreo.value = usuario.correo || usuario.usuario || '';
    }

    // Cerrar drawer lateral si está abierto
    if (typeof cerrarCarrito === 'function') {
        cerrarCarrito();
    }

    modal.classList.add('activo');
}

function cerrarCheckoutModal() {
    const modal = document.getElementById('modal-checkout');
    if (modal) {
        modal.classList.remove('activo');
    }
}

async function procesarCompraEclat(e) {
    if (e && e.preventDefault) e.preventDefault();

    const btnSubmit = document.querySelector('.btn-confirmar-pedido') || document.getElementById('btn-submit-factura');
    const textoOriginal = btnSubmit ? btnSubmit.innerHTML : 'Confirmar Pedido y Facturar ✦';

    const nombre = (document.getElementById('chk-nombre')?.value || '').trim();
    const correo = (document.getElementById('chk-correo')?.value || '').trim();
    const telefono = (document.getElementById('chk-telefono')?.value || '').trim();
    const direccion = (document.getElementById('chk-direccion')?.value || '').trim();
    const ciudad = (document.getElementById('chk-ciudad')?.value || '').trim();
    const metodo = document.getElementById('chk-metodo')?.value || 'Nequi';
    const carrito = obtenerCarrito();

    if (carrito.length === 0) {
        eclatAlerta({
            icon: 'warning',
            title: 'Bolsa Vacía',
            text: 'No tienes productos en tu carrito para facturar.'
        });
        return;
    }

    const { subtotal, envio, total } = calcularTotales(carrito);
    const rutaBase = getRutaBase();

    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '⏳ Registrando cliente y factura...';
    }

    try {
        const respuesta = await fetch(`${rutaBase}procesar_compra.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                cliente: {
                    nombre: nombre,
                    correo: correo,
                    telefono: telefono,
                    direccion: direccion,
                    ciudad: ciudad
                },
                metodo_pago: metodo,
                productos: carrito,
                subtotal: subtotal,
                envio: envio,
                total: total
            })
        });

        const resultado = await respuesta.json();

        if (!resultado.success) {
            throw new Error(resultado.error || 'Ocurrió un error al procesar la factura en la base de datos.');
        }

        cerrarCheckoutModal();

        // Vaciar carrito tras asentar en BD
        localStorage.removeItem('eclat_visage_carrito');
        actualizarContadores();
        if (typeof renderizarDrawer === 'function') {
            renderizarDrawer();
        }

        // Si estamos en carrito.html, actualizar interfaz
        const seccionActiva = document.getElementById('seccion-carrito-activa');
        const confirmacion = document.getElementById('pantalla-confirmacion');
        const detalleConfirmacion = document.getElementById('confirmacion-detalle');

        if (seccionActiva && confirmacion && detalleConfirmacion) {
            seccionActiva.style.display = 'none';
            confirmacion.classList.add('activo');
            detalleConfirmacion.innerHTML = `
                <div style="background:#fdfbf7; border:1px solid #d8c5b2; border-radius:10px; padding:18px; margin:15px auto; text-align:left; max-width:480px; font-size:13.5px; color:#3d352a;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px; border-bottom:1px dashed #d8c5b2; padding-bottom:8px;">
                        <span>🧾 <strong>Factura Registrada:</strong></span>
                        <span style="color:#bc9c82; font-weight:700;">#${resultado.id_factura}</span>
                    </div>
                    <div style="margin-bottom:6px;">  <strong>Cliente:</strong> ${resultado.cliente_nombre} (ID: #${resultado.id_cliente})</div>
                    <div style="margin-bottom:6px;">📧 <strong>Correo:</strong> ${resultado.cliente_correo}</div>
                    <div style="margin-bottom:6px;">📞 <strong>Teléfono:</strong> ${resultado.cliente_telefono || 'No registrado'}</div>
                    <div style="margin-bottom:6px;">📍 <strong>Envío a:</strong> ${resultado.cliente_direccion || 'No especificada'}</div>
                    <div style="margin-bottom:6px;">💳 <strong>Método de Pago:</strong> ${resultado.metodo_pago}</div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px; padding-top:8px; border-top:1px solid #d8c5b2; font-size:15px;">
                        <span><strong>Total Facturado:</strong></span>
                        <span style="font-weight:700; color:#2e7d32;">${formatearMoneda(resultado.total)} COP</span>
                    </div>
                </div>
                <p style="font-size:13px; color:#8c7a6b; margin-top:10px;">
                    El cliente y la factura han sido registrados automáticamente en la base de datos de <strong>Éclat Visage</strong>.
                </p>
                <div style="margin-top:20px; display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                    <a href="${rutaBase}CRUD/facturas_crud.php" class="btn-comprar-principal" style="background:#3d352a; padding:10px 18px; font-size:13px; text-decoration:none;">
                        Ver en Tabla Facturas ➔
                    </a>
                    <a href="${rutaBase}CRUD/clientes_crud.php" class="btn-comprar-principal" style="background:#8c7a6b; padding:10px 18px; font-size:13px; text-decoration:none;">
                        Ver en Tabla Clientes ➔
                    </a>
                </div>
            `;
        }

        // Mostrar SweetAlert2 con el comprobante de factura y cliente
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¡Compra y Factura Registradas!',
                html: `
                    <div style="text-align: left; font-size: 13.5px; line-height: 1.6; color: #3d352a;">
                        <p style="margin-bottom: 12px; text-align:center; color: #2e7d32; font-weight:600;">
                            ✓ Factura creada y cliente registrado en la base de datos.
                        </p>
                        <div style="background:#f9f5f0; padding:14px; border-radius:10px; border:1px solid #e6dfd5; font-size:13px;">
                            <div><strong>Nº Factura:</strong> <span style="color:#bc9c82; font-weight:700;">#${resultado.id_factura}</span></div>
                            <div><strong>Cliente (ID #${resultado.id_cliente}):</strong> ${resultado.cliente_nombre}</div>
                            <div><strong>Correo:</strong> ${resultado.cliente_correo}</div>
                            <div><strong>Teléfono:</strong> ${resultado.cliente_telefono || 'No registrado'}</div>
                            <div><strong>Dirección:</strong> ${resultado.cliente_direccion || 'No especificada'}</div>
                            <div><strong>Método:</strong> ${resultado.metodo_pago}</div>
                            <div style="margin-top:8px; padding-top:6px; border-top:1px dashed #d8c5b2; font-size:14px;">
                                <strong>Total Facturado:</strong> <span style="color:#2e7d32; font-weight:700;">${formatearMoneda(resultado.total)} COP</span>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Ver Facturas (CRUD)',
                cancelButtonText: 'Seguir Comprando',
                confirmButtonColor: '#3d352a',
                cancelButtonColor: '#8c7a6b',
                background: '#fdfbf7',
                color: '#3d352a'
            }).then((res) => {
                if (res.isConfirmed) {
                    window.location.href = `${rutaBase}CRUD/facturas_crud.php`;
                }
            });
        } else {
            alert(`¡Factura #${resultado.id_factura} registrada con éxito para el cliente ${resultado.cliente_nombre} (ID: #${resultado.id_cliente})!`);
        }

    } catch (err) {
        eclatAlerta({
            icon: 'error',
            title: 'Error al Facturar',
            text: err.message || 'No se pudo conectar con el servidor para registrar la factura.',
            confirmButtonColor: '#a85d5d'
        });
    } finally {
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = textoOriginal;
        }
    }
}

function vincularClicksCarrito() {
    const disparadores = document.querySelectorAll('#contador-carrito, .btn-abrir-carrito, .header-iconos span:last-child');
    
    disparadores.forEach(el => {
        el.addEventListener('click', function(e) {
            if (el.id === 'contador-carrito' || el.tagName.toLowerCase() === 'span' || el.classList.contains('btn-abrir-carrito')) {
                e.preventDefault();
                abrirCarrito();
            }
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarCarrito();
        }
    });

    // Interceptar automáticamente botones de eliminar en tablas CRUD para usar SweetAlert2
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-eliminar');
        if (btn && btn.href && !btn.dataset.swalBypassed) {
            e.preventDefault();
            e.stopImmediatePropagation();
            eclatAlerta({
                title: '¿Confirmar eliminación?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#a85d5d',
                cancelButtonColor: '#8c7a6b'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.dataset.swalBypassed = 'true';
                    window.location.href = btn.href;
                }
            });
        }
    }, true);
}

// Iniciar al cargar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarSistemaCarrito);
} else {
    inicializarSistemaCarrito();
}

// Exportar globalmente
window.abrirCarrito = abrirCarrito;
window.cerrarCarrito = cerrarCarrito;
window.cambiarCantidad = cambiarCantidad;
window.eliminarProducto = eliminarProducto;
window.vaciarCarrito = vaciarCarrito;
window.enviarPedidoWhatsApp = enviarPedidoWhatsApp;
window.formatearMoneda = formatearMoneda;
window.obtenerCarrito = obtenerCarrito;
window.guardarCarrito = guardarCarrito;
window.calcularTotales = calcularTotales;
window.actualizarContadores = actualizarContadores;
window.obtenerUsuarioAutenticado = obtenerUsuarioAutenticado;
window.cerrarSesionUsuario = cerrarSesionUsuario;
window.eclatAlerta = eclatAlerta;
window.getRutaBase = getRutaBase;
window.abrirCheckoutModal = abrirCheckoutModal;
window.cerrarCheckoutModal = cerrarCheckoutModal;
window.procesarCompraEclat = procesarCompraEclat;
window.procesarOrden = procesarCompraEclat;
window.inicializarDropdownsCRUD = inicializarDropdownsCRUD;
