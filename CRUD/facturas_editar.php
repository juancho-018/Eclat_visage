<?php
include("conexion.php");

$mensaje = "";

if (isset($_GET['id'])) {
    $id_factura = intval($_GET['id']);
    $resultado = $conexion->query("SELECT * FROM factura WHERE id_factura = $id_factura");
    
    if ($resultado->num_rows > 0) {
        $factura = $resultado->fetch_assoc();
    } else {
        header("Location: facturas_crud.php");
        exit();
    }
}

if (isset($_POST['actualizar'])) {
    $id_factura    = intval($_POST['id_factura']);
    $fecha_factura = $_POST['fecha_factura'];
    $total         = floatval($_POST['total']);
    $id_cliente    = intval($_POST['id_cliente']);
    $id_empleado   = intval($_POST['id_empleado']);

    $sql = "UPDATE factura SET 
            fecha_factura = '$fecha_factura', 
            total = $total, 
            id_cliente = $id_cliente, 
            id_empleado = $id_empleado 
            WHERE id_factura = $id_factura";

    if ($conexion->query($sql)) {
        header("Location: facturas_crud.php");
        exit();
    } else {
        $mensaje = "Error al actualizar la factura: " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Factura — Éclat Visage</title>
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
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d8c5b2;
            border-radius: 6px;
            background: #fff;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        .form-group input:disabled {
            background-color: #f4efe6;
            color: #8c7a6b;
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
            <a href="facturas_crud.php">VOLVER AL CRUD</a>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Editar Factura #<?php echo $factura['id_factura']; ?></h2>
            <?php if (!empty($mensaje)) echo "<p style='color:red; text-align:center;'>$mensaje</p>"; ?>
            
            <form action="facturas_editar.php" method="POST">
                <input type="hidden" name="id_factura" value="<?php echo $factura['id_factura']; ?>">

                <div class="form-group">
                    <label>ID Factura (No modificable):</label>
                    <input type="number" value="<?php echo $factura['id_factura']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Fecha:</label>
                    <input type="date" name="fecha_factura" value="<?php echo $factura['fecha_factura']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Total ($):</label>
                    <input type="number" step="0.01" name="total" value="<?php echo $factura['total']; ?>" required>
                </div>

                <div class="form-group">
                    <label>ID Cliente:</label>
                    <input type="number" name="id_cliente" value="<?php echo $factura['id_cliente']; ?>" required>
                </div>

                <div class="form-group">
                    <label>ID Empleado:</label>
                    <input type="number" name="id_empleado" value="<?php echo $factura['id_empleado']; ?>" required>
                </div>
                
                <button type="submit" name="actualizar" class="btn-guardar">Guardar Cambios</button>
                <a href="facturas_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="copyright">
            © 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>