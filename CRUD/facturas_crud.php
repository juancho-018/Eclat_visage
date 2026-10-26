<?php
include("conexion.php");

// Búsqueda opcional
$busqueda = "";
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $filtro = $conexion->real_escape_string($_GET['buscar']);
    $busqueda = " WHERE id_factura LIKE '%$filtro%' OR id_cliente LIKE '%$filtro%' OR id_empleado LIKE '%$filtro%'";
}

// CORREGIDO: Apuntando a la tabla 'factura' tal como está en la base de datos
$sql = "SELECT * FROM factura $busqueda ORDER BY id_factura DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Facturas — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .crud-container {
            max-width: 1000px;
            margin: 30px auto;
            background: #fdfbf7;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e6dfd5;
        }
        .crud-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .crud-header h2 {
            font-family: 'Playfair Display', serif;
            color: #4a3b32;
            margin: 0;
        }
        .btn-nuevo {
            background-color: #4a3b32;
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.3s;
        }
        .btn-nuevo:hover {
            background-color: #5d4a3f;
        }
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .search-bar input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #d8c5b2;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
        }
        .search-bar button {
            background-color: #8c7a6b;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #5a4a42;
        }
        th {
            background-color: #f4efe6;
            font-weight: 600;
            color: #4a3b32;
        }
        tr:hover {
            background-color: #fdfbf7;
        }
        .acciones a {
            text-decoration: none;
            margin-right: 10px;
            font-size: 13px;
            font-weight: 500;
        }
        .btn-editar {
            color: #b38b59;
        }
        .btn-editar:hover {
            text-decoration: underline;
        }
        .btn-eliminar {
            color: #a85d5d;
        }
        .btn-eliminar:hover {
            text-decoration: underline;
        }
        .no-registros {
            text-align: center;
            padding: 20px;
            color: #8c7a6b;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo">eclat visage<span>✦</span></div>
        <nav class="nav">
            <a href="../index.html">INICIO</a>
            <a href="facturas_agregar.php" class="btn-nuevo" style="color: #fff;">+ Nueva Factura</a>
        </nav>
    </header>

    <main>
        <div class="crud-container">
            <div class="crud-header">
                <h2>Listado de Facturas</h2>
                <a href="facturas_agregar.php" class="btn-nuevo">+ Registrar Factura</a>
            </div>

            <form method="GET" class="search-bar">
                <input type="text" name="buscar" placeholder="Buscar por ID de factura, cliente o empleado..." value="<?php echo isset($_GET['buscar']) ? $_GET['buscar'] : ''; ?>">
                <button type="submit">Buscar</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID Factura</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>ID Cliente</th>
                        <th>ID Empleado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0) { ?>
                        <?php while($row = $resultado->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id_factura']; ?></td>
                                <td><?php echo $row['fecha_factura']; ?></td>
                                <td>$<?php echo number_format($row['total'], 2); ?></td>
                                <td><?php echo $row['id_cliente']; ?></td>
                                <td><?php echo $row['id_empleado']; ?></td>
                                <td class="acciones">
                                    <a href="facturas_editar.php?id=<?php echo $row['id_factura']; ?>" class="btn-editar">Editar</a>
                                    <a href="facturas_eliminar.php?id=<?php echo $row['id_factura']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás segura de eliminar esta factura?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6" class="no-registros">No hay facturas registradas en la base de datos.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="footer">
        <div class="copyright">
            © 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>