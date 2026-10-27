<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $resultado = $conexion->query("SELECT * FROM categoria WHERE id_categoria = $id");
    if ($resultado->num_rows == 1) {
        $row = $resultado->fetch_assoc();
    } else {
        header("Location: categorias_crud.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id_categoria']);
    $nombre = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion'];

    $sql = "UPDATE categoria SET nombre_categoria = '$nombre', descripcion = '$descripcion' WHERE id_categoria = $id";

    if ($conexion->query($sql)) {
        header("Location: categorias_crud.php");
        exit();
    } else {
        $error = "Error al actualizar: " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría - Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body class="body-registro">
    <div class="registro-card">
        <div class="logo">eclat visage<span>✦</span></div>
        <h2>Editar Categoría</h2>
        <p class="subtitulo-registro">Modifica los datos de la categoría</p>

        <?php if(isset($error)) { echo "<p style='color:red; font-size:12px;'>$error</p>"; } ?>

        <form action="" method="POST">
            <div class="campo">
                <label>ID de la Categoría</label>
                <input type="number" name="id_categoria_disabled" value="<?php echo $row['id_categoria']; ?>" disabled style="background:#f0f0f0;">
                <input type="hidden" name="id_categoria" value="<?php echo $row['id_categoria']; ?>">
            </div>
            
            <div class="campo">
                <label>Nombre de la Categoría</label>
                <input type="text" name="nombre_categoria" value="<?php echo $row['nombre_categoria']; ?>" required>
            </div>
            <div class="campo">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="<?php echo $row['descripcion']; ?>" required>
            </div>
            <button type="submit" class="btn-registro">ACTUALIZAR CATEGORÍA</button>
        </form>
        <br>
        <a href="categorias_crud.php" style="font-size:12px; color:#666; text-decoration:none;">← Volver a la tabla</a>
    </div>
</body>
</html>