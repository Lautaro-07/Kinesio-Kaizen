<?php
session_start(); // Continuar la sesión

// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'path/to/error_log.log');

// Si no se ha seleccionado una fecha y hora, redirigir a fecha.php
if (!isset($_SESSION['fecha']) || !isset($_SESSION['hora']) || !isset($_SESSION['sede']) || !isset($_SESSION['servicio']) || !isset($_SESSION['profesional'])) {
    header('Location: fecha.php');
    exit();
}

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'turnos');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Preparar la consulta
$stmt = $conn->prepare("INSERT INTO turnos (sede, servicio, profesional, fecha, hora, nombre, telefono, gmail, obra_social) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt->bind_param(
        "sssssssss",
        $_SESSION['sede'],
        $_SESSION['servicio'],
        $_SESSION['profesional'],
        $_SESSION['fecha'],
        $_SESSION['hora'],
        $_POST['nombre_paciente'],
        $_POST['telefono'],
        $_POST['gmail'],
        $_POST['obra_social']
    );

    if ($stmt->execute()) {
        // Guardar los datos del paciente en la sesión
        $_SESSION['nombre_paciente'] = $_POST['nombre_paciente'];
        $_SESSION['telefono'] = $_POST['telefono'];
        $_SESSION['gmail'] = $_POST['gmail'];
        $_SESSION['obra_social'] = $_POST['obra_social'];

        // Redirigir a la página de confirmación con los detalles del turno
        header('Location: confirmacion.php');
        exit();
    } else {
        echo "Error al registrar el turno: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="bootstrap-5.1.3-dist/css/bootstrap.css">
    <script src="bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&family=Noto+Sans&family=Poppins:wght@300&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/989f8affb2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
    <title>Kaizen - Paciente</title>
    <link rel="icon" href="img/ISO_Violeta.png">

    <style>
        .servicio_title{
            text-align: center;
            margin: auto;
            letter-spacing: 9px;
            font-weight: 700;
            position: relative;
            top: 30px;
        }
    </style>
</head>
<body>
    <header>
    <nav class="nav_container navbar navbar-dark">
      <div class="logo_container container-fluid">
        <img class="logo" src="img/ISO_Violeta.png" alt="Logo">
      </div>
    </nav>
    </header>
    <div class="color"></div>
    <h1 class="servicio_title">TUS DATOS</h1>
    <hr style="position: relative; top: 40px; width: 40%; margin: auto;">

    <form class="paciente_container" action="paciente.php" method="POST">
        <label for="nombre_paciente"></label>
        <input type="text" name="nombre_paciente" placeholder="Nombre completo" required><br>

        <label for="telefono"></label>
        <input type="text" name="telefono" placeholder="Teléfono" required><br>

        <label for="gmail"></label>
        <input type="email" name="gmail"  placeholder="Gmail" required><br>

        <label for="obra_social"></label>
        <input type="text" name="obra_social" placeholder="Obra Social" required><br>

        <button type="submit">Confirmar Turno</button>
    </form>
</body>
</html>