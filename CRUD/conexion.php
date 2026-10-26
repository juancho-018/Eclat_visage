<?php
$host = "localhost";
$usuario = "root";
$password = ""; // Cambia esto si tu servidor local tiene contraseña
$base_datos = "eclat_visage"; // Asegúrate de que este sea el nombre exacto en tu phpMyAdmin

$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar si hay errores de conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// Establecer el juego de caracteres a UTF-8 para evitar problemas con tildes
$conexion->set_charset("utf8");
?>