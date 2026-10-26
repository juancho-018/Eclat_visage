<?php
include("CRUD/conexiones.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre    = $_POST['nombre'];
    $correo    = $_POST['correo'];
    $telefono  = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    // Consulta SQL ajustada a la tabla 'clientes' de tu diagrama
    $sql = "INSERT INTO clientes (nombre, correo, telefono, direccion, fecha_registro) 
            VALUES ('$nombre', '$correo', '$telefono', '$direccion', CURDATE())";

    if ($conexion->query($sql) === TRUE) {
        echo "<script>
                alert('¡Registro guardado exitosamente en phpMyAdmin!');
                window.location.href = 'index.html';
              </script>";
    } else {
        echo "Error al guardar: " . $conexion->error;
    }
}
?>