<?php
/**
 * ECLAT VISAGE — Procesamiento de Compras & Facturación
 * Registra automáticamente el cliente en la tabla `clientes`,
 * la factura en `factura`, los ítems en `detalle_factura`
 * y descuenta el stock generando el movimiento en `inventario`.
 */
header('Content-Type: application/json; charset=utf-8');

// Cargar la conexión MySQL
if (file_exists(__DIR__ . '/CRUD/conexion.php')) {
    require_once __DIR__ . '/CRUD/conexion.php';
} elseif (file_exists(__DIR__ . '/conexion.php')) {
    require_once __DIR__ . '/conexion.php';
} else {
    echo json_encode(['success' => false, 'error' => 'No se encontró el archivo de conexión a la base de datos.'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Obtener datos recibidos en JSON o POST tradicional
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

if (empty($data)) {
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos para procesar la compra.'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Extraer campos del cliente
$cliente_data = isset($data['cliente']) && is_array($data['cliente']) ? $data['cliente'] : $data;

$nombre    = trim($cliente_data['nombre'] ?? $cliente_data['chk-nombre'] ?? '');
$correo    = trim($cliente_data['correo'] ?? $cliente_data['chk-correo'] ?? '');
$telefono  = trim($cliente_data['telefono'] ?? $cliente_data['chk-telefono'] ?? '');
$direccion = trim($cliente_data['direccion'] ?? $cliente_data['chk-direccion'] ?? '');
$ciudad    = trim($cliente_data['ciudad'] ?? $cliente_data['chk-ciudad'] ?? '');
$metodo    = trim($data['metodo_pago'] ?? $cliente_data['chk-metodo'] ?? 'Nequi / Bancolombia');

if (!empty($ciudad)) {
    $direccion_completa = !empty($direccion) ? $direccion . " (" . $ciudad . ")" : $ciudad;
} else {
    $direccion_completa = $direccion;
}

if (empty($nombre) || empty($correo)) {
    echo json_encode(['success' => false, 'error' => 'El nombre y correo electrónico del cliente son requeridos.'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Extraer productos y totales
$productos = $data['productos'] ?? $data['carrito'] ?? [];
$total     = floatval($data['total'] ?? 0);
$subtotal  = floatval($data['subtotal'] ?? $total);
$envio     = floatval($data['envio'] ?? 0);

if ($total <= 0 && !empty($productos)) {
    foreach ($productos as $p) {
        $total += (floatval($p['precio'] ?? 0) * intval($p['cantidad'] ?? 1));
    }
}

// Iniciar transacción SQL para garantizar atomicidad
$conexion->begin_transaction();

try {
    // ----------------------------------------------------
    // 1. REGISTRAR O ACTUALIZAR CLIENTE (tabla: clientes)
    // ----------------------------------------------------
    $id_cliente = null;
    
    // Verificar si ya existe un cliente con este correo
    $stmt_check = $conexion->prepare("SELECT id_cliente FROM clientes WHERE correo = ? LIMIT 1");
    $stmt_check->bind_param("s", $correo);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($row_cli = $res_check->fetch_assoc()) {
        $id_cliente = intval($row_cli['id_cliente']);
        // Actualizar información de contacto
        $stmt_upd = $conexion->prepare("UPDATE clientes SET nombre = ?, telefono = ?, direccion = ? WHERE id_cliente = ?");
        $stmt_upd->bind_param("sssi", $nombre, $telefono, $direccion_completa, $id_cliente);
        $stmt_upd->execute();
        $stmt_upd->close();
    } else {
        // Insertar nuevo cliente
        $stmt_ins = $conexion->prepare("INSERT INTO clientes (nombre, correo, telefono, direccion, fecha_registro) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt_ins->bind_param("ssss", $nombre, $correo, $telefono, $direccion_completa);
        if (!$stmt_ins->execute()) {
            throw new Exception("Error al registrar cliente: " . $stmt_ins->error);
        }
        $id_cliente = $conexion->insert_id;
        $stmt_ins->close();
    }
    $stmt_check->close();

    if (!$id_cliente) {
        throw new Exception("No fue posible vincular o crear el ID del cliente.");
    }

    // ----------------------------------------------------
    // 2. ASIGNAR EMPLEADO DE TURNO (tabla: empleados)
    // ----------------------------------------------------
    $id_empleado = null;
    $emp_res = $conexion->query("SELECT id_empleado FROM empleados ORDER BY id_empleado ASC LIMIT 1");
    if ($emp_res && $emp_row = $emp_res->fetch_assoc()) {
        $id_empleado = intval($emp_row['id_empleado']);
    }

    // ----------------------------------------------------
    // 3. REGISTRAR FACTURA (tabla: factura)
    // ----------------------------------------------------
    if ($id_empleado !== null) {
        $stmt_fac = $conexion->prepare("INSERT INTO factura (fecha_factura, total, id_cliente, id_empleado) VALUES (CURDATE(), ?, ?, ?)");
        $stmt_fac->bind_param("dii", $total, $id_cliente, $id_empleado);
    } else {
        $stmt_fac = $conexion->prepare("INSERT INTO factura (fecha_factura, total, id_cliente) VALUES (CURDATE(), ?, ?)");
        $stmt_fac->bind_param("di", $total, $id_cliente);
    }

    if (!$stmt_fac->execute()) {
        throw new Exception("Error al registrar la factura: " . $stmt_fac->error);
    }
    $id_factura = $conexion->insert_id;
    $stmt_fac->close();

    // ----------------------------------------------------
    // 4. DETALLE DE FACTURA E INVENTARIO
    // ----------------------------------------------------
    $check_det = $conexion->query("SHOW TABLES LIKE 'detalle_factura'");
    $existe_detalle = ($check_det && $check_det->num_rows > 0);

    // Obtener categoría y proveedor base por si se autogenera un producto
    $cat_default = null;
    $c_res = $conexion->query("SELECT id_categoria FROM categoria LIMIT 1");
    if ($c_res && $c_row = $c_res->fetch_assoc()) $cat_default = intval($c_row['id_categoria']);

    $prov_default = null;
    $pr_res = $conexion->query("SELECT id_proveedor FROM proveedores LIMIT 1");
    if ($pr_res && $pr_row = $pr_res->fetch_assoc()) $prov_default = intval($pr_row['id_proveedor']);

    $items_registrados = 0;

    foreach ($productos as $item) {
        $prod_nombre   = trim($item['nombre'] ?? '');
        $prod_cant     = max(1, intval($item['cantidad'] ?? 1));
        $prod_precio   = floatval($item['precio'] ?? 0);
        $prod_subtotal = $prod_cant * $prod_precio;

        if (empty($prod_nombre)) continue;

        // Buscar producto en la tabla productos
        $p_stmt = $conexion->prepare("SELECT id_producto, stock, precio FROM productos WHERE nombre_producto = ? LIMIT 1");
        $p_stmt->bind_param("s", $prod_nombre);
        $p_stmt->execute();
        $p_res = $p_stmt->get_result();

        $id_producto = null;
        if ($p_row = $p_res->fetch_assoc()) {
            $id_producto = intval($p_row['id_producto']);
        } else {
            // Intento por aproximación
            $like_term = "%" . substr($prod_nombre, 0, 10) . "%";
            $p_like = $conexion->prepare("SELECT id_producto FROM productos WHERE nombre_producto LIKE ? LIMIT 1");
            $p_like->bind_param("s", $like_term);
            $p_like->execute();
            $like_res = $p_like->get_result();
            if ($like_row = $like_res->fetch_assoc()) {
                $id_producto = intval($like_row['id_producto']);
            }
            $p_like->close();
        }
        $p_stmt->close();

        // Si el producto del catálogo no existía aún en la BD, lo registramos para no violar clave foránea
        if (!$id_producto) {
            $stmt_np = $conexion->prepare("INSERT INTO productos (nombre_producto, descripcion, precio, stock, id_categoria, id_proveedor) VALUES (?, 'Fórmula cosmética Éclat Visage', ?, 50, ?, ?)");
            $stmt_np->bind_param("sdii", $prod_nombre, $prod_precio, $cat_default, $prov_default);
            if ($stmt_np->execute()) {
                $id_producto = $conexion->insert_id;
            }
            $stmt_np->close();
        }

        if ($id_producto) {
            // Insertar en detalle_factura si la tabla existe
            if ($existe_detalle) {
                $stmt_df = $conexion->prepare("INSERT INTO detalle_factura (id_producto, id_factura, cantidad, precio, subtotal) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad), subtotal = subtotal + VALUES(subtotal)");
                $stmt_df->bind_param("iiidd", $id_producto, $id_factura, $prod_cant, $prod_precio, $prod_subtotal);
                $stmt_df->execute();
                $stmt_df->close();
            }

            // Descontar inventario / stock
            $conexion->query("UPDATE productos SET stock = GREATEST(0, stock - $prod_cant) WHERE id_producto = $id_producto");

            // Registrar movimiento de salida en inventario (enum: 'ENTRADA', 'SALIDA')
            $tipo_mov = "SALIDA";
            $stmt_inv = $conexion->prepare("INSERT INTO inventario (id_producto, tipo_movimiento, cantidad, fecha_movimiento) VALUES (?, ?, ?, CURDATE())");
            $stmt_inv->bind_param("isi", $id_producto, $tipo_mov, $prod_cant);
            $stmt_inv->execute();
            $stmt_inv->close();

            $items_registrados++;
        }
    }

    // Confirmar y asentar todas las operaciones en MySQL
    $conexion->commit();

    echo json_encode([
        'success'           => true,
        'mensaje'           => '¡Factura registrada exitosamente!',
        'id_factura'        => $id_factura,
        'id_cliente'        => $id_cliente,
        'cliente_nombre'    => $nombre,
        'cliente_correo'    => $correo,
        'cliente_telefono'  => $telefono,
        'cliente_direccion' => $direccion_completa,
        'metodo_pago'       => $metodo,
        'fecha'             => date('Y-m-d'),
        'total'             => $total,
        'items_count'       => $items_registrados
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode([
        'success' => false,
        'error'   => 'Error en el servidor al registrar factura y cliente: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
