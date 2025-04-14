<?php
session_start(); // Continuar la sesión

require 'vendor/autoload.php'; // Incluir el autoloader de Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Depuración: Verificar el contenido de la sesión


// Verificar que existan los datos necesarios en la sesión
$required_fields = ['fecha', 'hora', 'nombre_paciente', 'profesional', 'servicio', 'telefono', 'gmail', 'obra_social', 'sede'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($_SESSION[$field])) {
        $missing_fields[] = $field;
    }
}

// Datos del turno
$sede = $_SESSION['sede'];
$servicio = $_SESSION['servicio'];
$profesional = $_SESSION['profesional'];
$fecha = $_SESSION['fecha'];
$hora = $_SESSION['hora'];
$nombre = $_SESSION['nombre_paciente']; // Cambiado de nombre a nombre_paciente
$telefono = $_SESSION['telefono'];
$gmail = $_SESSION['gmail'];
$obra_social = $_SESSION['obra_social'];

// Conectar a la base de datos
$mysqli = new mysqli("localhost", "root", "", "turnos");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Insertar el turno en la base de datos
$stmt = $mysqli->prepare("INSERT INTO turnos (sede, servicio, profesional, fecha, hora, nombre, gmail, telefono, obra_social) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sssssssss", $sede, $servicio, $profesional, $fecha, $hora, $nombre, $gmail, $telefono, $obra_social);
    if (!$stmt->execute()) {
        die("Error al registrar el turno: " . $stmt->error);
    }
    $stmt->close();
} else {
    die("Error al preparar la consulta: " . $mysqli->error);
}

// Cerrar conexión
$mysqli->close();

// Enviar el correo de confirmación
$to_email = $gmail;
$subject = 'Confirmación de Turno - Kaizen';

// Crear el mensaje de correo electrónico
$mensaje_email = "
<html>
<head>
    <title>Detalles de tu turno</title>
</head>
<body>
    <h2>¡Turno Confirmado con Éxito!</h2>
    <p>Gracias por confiar en Kaizen. Aquí están los detalles de tu turno:</p>
    <p><strong>Sede:</strong> $sede</p>
    <p><strong>Servicio:</strong> $servicio</p>
    <p><strong>Profesional:</strong> $profesional</p>
    <p><strong>Nombre:</strong> $nombre</p>
    <p><strong>Fecha:</strong> $fecha</p>
    <p><strong>Hora:</strong> $hora</p>
    <p><strong>Obra Social:</strong> $obra_social</p>
</body>
</html>
";

$mail = new PHPMailer(true);

try {
    // Configuración del servidor
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'oligiatielizondo@gmail.com'; // Tu dirección de correo
    $mail->Password = 'ostc ewyt kjhy firp'; // Tu contraseña de aplicación generada
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configuración del correo
    $mail->setFrom('oligiatielizondo@gmail.com', 'Kaizen');
    $mail->addAddress($to_email);
    $mail->Subject = $subject;
    $mail->Body = $mensaje_email;
    $mail->isHTML(true);

    $mail->send();
} catch (Exception $e) {
    echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
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
    <link rel="icon" href="img/ISO_Violeta.png">
    <title>Turno Confirmado</title>
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
    <div class="tarjeta-confirmacion" style="color: #fff !important;">
        <h2>¡Turno Confirmado con Éxito!</h2>
        <p>Gracias por confiar en nosotros. Aquí están los detalles de tu turno:</p>
        <p><strong>Sede:</strong> <?= $sede; ?></p>
        <p><strong>Servicio:</strong> <?= $servicio; ?></p>
        <p><strong>Profesional:</strong> <?= $profesional; ?></p>
        <p><strong>Nombre:</strong> <?= $nombre; ?></p>
        <p><strong>Fecha:</strong> <?= $fecha; ?></p>
        <p><strong>Hora:</strong> <?= $hora; ?></p>
        <p><strong>Obra Social:</strong> <?= $obra_social; ?></p>
        <a href="index.php" class="btn-volver" style="color: #fff !important;">Volver al Inicio</a>
    </div>
</body>
</html>
