<?php
include("conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías - Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <!-- BARRA DE NAVEGACIÓN SUPERIOR -->
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
        <h1>Gestión de Categorías</h1>
        <a href="categorias_agregar.php" class="btn-agregar">+ Nueva Categoría</a>
        
        <div class="tabla-contenedor">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>ID Categoría</th>
                        <th>Nombre Categoría</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM categoria";
                    $resultado = $conexion->query($sql);
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id_categoria'] . "</td>";
                            echo "<td>" . $row['nombre_categoria'] . "</td>";
                            echo "<td>" . $row['descripcion'] . "</td>";
                            echo "<td class='acciones-cell'>
                                    <a href='categorias_editar.php?id=" . $row['id_categoria'] . "' class='btn-editar'>Editar</a>
                                    <a href='categorias_eliminar.php?id=" . $row['id_categoria'] . "' class='btn-eliminar' onclick='return confirm(\"¿Estás seguro de eliminar esta categoría?\");'>Eliminar</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No hay categorías registradas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>