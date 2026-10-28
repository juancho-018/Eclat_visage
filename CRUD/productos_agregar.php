<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$mensaje = "";
if (isset($_POST['guardar'])) {
    $id_producto      = intval($_POST['id_producto']);
    $nombre_producto  = $conexion->real_escape_string($_POST['nombre_producto']);
    $descripcion      = $conexion->real_escape_string($_POST['descripcion']);
    $precio           = floatval($_POST['precio']);
    $stock            = intval($_POST['stock']);
    $id_categoria     = intval($_POST['id_categoria']);
    $id_proveedor     = intval($_POST['id_proveedor']);

    // Columnas corregidas en singular para que coincidan con la base de datos
    $sql = "INSERT INTO productos (id_producto, nombre_producto, descripcion, precio, stock, id_categoria, id_proveedor) 
            VALUES ($id_producto, '$nombre_producto', '$descripcion', $precio, $stock, $id_categoria, $id_proveedor)";
    
    if ($conexion->query($sql)) {
        header("Location: productos_crud.php");
        exit();
    } else {
        $mensaje = "Error al registrar el producto (verifica que el ID no esté repetido): " . $conexion->error;
    }
}

// Obtener categorías y proveedores para los menús desplegables
$categorias = $conexion->query("SELECT * FROM categoria");
$proveedores = $conexion->query("SELECT * FROM proveedores");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main>
        <div class="form-container">
            <h2>Agregar Nuevo Producto</h2>
            <?php if (!empty($mensaje)) echo "<p style='color:red; text-align:center;'>$mensaje</p>"; ?>
            
            <form action="productos_agregar.php" method="POST">
                <div class="form-group">
                    <label>ID Producto:</label>
                    <input type="number" name="id_producto" required>
                </div>

                <div class="form-group">
                    <label>Nombre del Producto:</label>
                    <input type="text" name="nombre_producto" required>
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Precio ($):</label>
                    <input type="number" step="0.01" name="precio" required>
                </div>

                <div class="form-group">
                    <label>Stock (Cantidad):</label>
                    <input type="number" name="stock" required>
                </div>

                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="id_categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <?php while ($cat = $categorias->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre_categoria']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Proveedor:</label>
                    <select name="id_proveedor" required>
                        <option value="">Seleccione un proveedor</option>
                        <?php while ($prov = $proveedores->fetch_assoc()): ?>
                            <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo htmlspecialchars($prov['nombre_empresa']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit" name="guardar" class="btn-guardar">Registrar Producto</button>
                <a href="productos_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

</body>
</html>