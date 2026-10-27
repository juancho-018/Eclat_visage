<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

if (isset($_POST['actualizar'])) {
    $id_productos     = intval($_POST['id_productos']);
    $nombre_productos = $conexion->real_escape_string($_POST['nombre_productos']);
    $descripcion      = $conexion->real_escape_string($_POST['descripcion']);
    $precio           = floatval($_POST['precio']);
    $stock            = intval($_POST['stock']);
    $id_categoria     = intval($_POST['id_categoria']);
    $id_proveedor     = intval($_POST['id_proveedor']);

    $sql_update = "UPDATE productos SET nombre_producto='$nombre_productos', descripcion='$descripcion', precio=$precio, stock=$stock, id_categoria=$id_categoria, id_proveedor=$id_proveedor WHERE id_producto=$id_productos";
    
    if ($conexion->query($sql_update)) {
        header("Location: productos_crud.php");
        exit();
    } else {
        $error = "Error al actualizar el producto: " . $conexion->error;
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $resultado = $conexion->query("SELECT * FROM productos WHERE id_producto = $id");
    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
    } else {
        header("Location: productos_crud.php");
        exit();
    }
} else {
    header("Location: productos_crud.php");
    exit();
}

$categorias = $conexion->query("SELECT * FROM categoria");
$proveedores = $conexion->query("SELECT * FROM proveedores");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .form-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fdfbf7;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e6dfd5;
        }
        .form-container h2 {
            font-family: 'Playfair Display', serif;
            color: #4a3b32;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #5a4a42;
            font-weight: 500;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d8c5b2;
            border-radius: 6px;
            background: #fff;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        .form-group textarea {
            resize: vertical;
            height: 80px;
        }
        .btn-guardar {
            background-color: #4a3b32;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .btn-guardar:hover {
            background-color: #5d4a3f;
        }
        .btn-cancelar {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #8c7a6b;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-cancelar:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo">eclat visage<span>✦</span></div>
        <nav class="nav">
            <a href="../index.html">INICIO</a>
            <a href="productos_crud.php">VOLVER AL CRUD</a>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Editar Producto</h2>
            <?php if (isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
            
            <form action="productos_editar.php?id=<?php echo $producto['id_producto']; ?>" method="POST">
                <input type="hidden" name="id_productos" value="<?php echo $producto['id_producto']; ?>">

                <div class="form-group">
                    <label>Nombre del Producto:</label>
                    <input type="text" name="nombre_productos" value="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Precio ($):</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Stock (Cantidad):</label>
                    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="id_categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <?php while ($cat = $categorias->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>" <?php echo ($cat['id_categoria'] == $producto['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Proveedor:</label>
                    <select name="id_proveedor" required>
                        <option value="">Seleccione un proveedor</option>
                        <?php while ($prov = $proveedores->fetch_assoc()): ?>
                            <option value="<?php echo $prov['id_proveedor']; ?>" <?php echo ($prov['id_proveedor'] == $producto['id_proveedor']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prov['nombre_empresa']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit" name="actualizar" class="btn-guardar">Guardar Cambios</button>
                <a href="productos_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="copyright">
            ©️ 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>