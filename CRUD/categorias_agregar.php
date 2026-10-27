<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_categoria = intval($_POST['id_categoria']);
    $nombre = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion'];

    $sql = "INSERT INTO categoria (id_categoria, nombre_categoria, descripcion) VALUES ($id_categoria, '$nombre', '$descripcion')";
    
    if ($conexion->query($sql)) {
        header("Location: categorias_crud.php");
        exit();
    } else {
        $error = "Error al registrar la categoría (¿El ID ya existe?): " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Categoría - Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body class="body-registro">
    <div class="registro-card">
        <div class="logo">eclat visage<span>✦</span></div>
        <h2>Nueva Categoría</h2>
        <p class="subtitulo-registro">Añade una categoría al sistema</p>
        
        <?php if(isset($error)) { echo "<p style='color:red; font-size:12px;'>$error</p>"; } ?>

        <form action="" method="POST">
            <div class="campo">
                <label>ID de la Categoría</label>
                <input type="number" name="id_categoria" required>
            </div>
            <div class="campo">
                <label>Nombre de la Categoría</label>
                <input type="text" name="nombre_categoria" required>
            </div>
            <div class="campo">
                <label>Descripción</label>
                <input type="text" name="descripcion" required>
            </div>
            <button type="submit" class="btn-registro">GUARDAR CATEGORÍA</button>
        </form>
        <br>
        <a href="categorias_crud.php" style="font-size:12px; color:#666; text-decoration:none;">← Volver a la tabla</a>
    </div>
</body>
</html>