<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Inventario - Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <header class="header">
        <div class="logo">eclat visage<span>✦</span></div>
        <nav class="nav">
            <a href="../index.html">INICIO</a>
            <a href="../tienda.html">VER TODO</a>
            <a href="../mas-vendidos.html">MÁS VENDIDOS</a>
            <a href="../test-piel.html">TEST DE PIEL</a>
            <a href="../diario.html">DIARIO</a>
            <a href="../nosotros.html">NOSOTROS</a>
            
            <div class="dropdown">
                <a href="#" class="dropbtn">CRUD ▾</a>
                <div class="dropdown-content">
                    <a href="clientes_crud.php">Tabla Clientes</a>
                    <a href="proveedores_crud.php">Tabla Proveedores</a>
                    <a href="productos_crud.php">Tabla Productos</a>
                    <a href="categorias_crud.php">Tabla Categorías</a>
                    <a href="inventario_crud.php">Tabla Inventario</a>
                    <a href="facturas_crud.php">Tabla Facturas</a>
                    <a href="empleados_crud.php">Tabla Empleados</a>
                </div>
            </div>
        </nav>
        <div class="header-icons">
            <a href="../login.html" title="Cuenta">👤</a>
            <a href="../carrito.html" title="Carrito">🛒 (0)</a>
        </div>
    </header>

    <main class="crud-body">
        <h1>Gestión de Inventario</h1>
        <a href="inventario_agregar.php" class="btn-agregar">+ Nuevo Movimiento</a>
        
        <div class="tabla-contenedor">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>ID Movimiento</th>
                        <th>ID Producto</th>
                        <th>Tipo Movimiento</th>
                        <th>Cantidad</th>
                        <th>Fecha Movimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $resultado = $conexion->query("SELECT * FROM inventario");
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id_movimiento'] . "</td>";
                            echo "<td>" . $row['id_producto'] . "</td>";
                            echo "<td>" . $row['tipo_movimiento'] . "</td>";
                            echo "<td>" . $row['cantidad'] . "</td>";
                            echo "<td>" . $row['fecha_movimiento'] . "</td>";
                            echo "<td class='acciones-cell'>
                                    <a href='inventario_eliminar.php?id=" . $row['id_movimiento'] . "' class='btn-eliminar' onclick='return confirm(\"¿Estás seguro de eliminar este registro?\");'>Eliminar</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay registros de inventario.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>