<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include("conexion.php");
// Lógica de inicio de sesión o registro
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- MENÚ SUPERIOR CON BOTONES VISIBLES -->
    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main>
        <div class="form-container">
            <h2>Iniciar Sesión</h2>
            <form action="registro_login.php" method="POST">
                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="correo" required placeholder="tu_correo@email.com">
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required placeholder="********">
                </div>
                <button type="submit" class="btn-enviar">Ingresar</button>
            </form>
            <a href="../index.html" class="volver-inicio">← Volver al inicio</a>
        </div>
    </main>

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

    <script src="../carrito.js"></script>
</body>
</html>