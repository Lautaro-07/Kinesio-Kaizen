<?php
session_start(); // Iniciar sesión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'turnos');
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscar el usuario en la base de datos
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $expected_password = $user['pass'];
        if ($password === $expected_password) {
            session_regenerate_id(true); // Generar un nuevo ID de sesión para evitar problemas de sesión fija
            $_SESSION['logged_in'] = true;
            $_SESSION['profesional'] = $user['name']; // Guardar el nombre del profesional en la sesión
            $_SESSION['user_id'] = $user['id']; // Guardar el ID del usuario en la sesión
            header('Location: pacientes.php');
            exit();
        } else {
            $error = 'Contraseña incorrecta.';
        }
    } else {
        $error = 'Email incorrecto.';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../bootstrap-5.1.3-dist/css/bootstrap.css">
    <script src="../bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&family=Noto+Sans&family=Poppins:wght@300&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/989f8affb2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
    <link rel="icon" href="../img/ISO_Violeta.png">
    <title>Login</title>
</head>
<body>
<header>
    <nav class="nav_container navbar navbar-dark">
      <div class="logo_container container-fluid">
        <img class="logo" src="../img/ISO_Violeta.png" alt="Logo">
      </div>
    </nav>
  </header>
  <div class="color"></div>
    <div class="login-container">
    <h2>Inicia Sesion</h2>
    <hr>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="index.php" method="post">
        <input type="email" name="email" required placeholder="Email"><br>
        <input type="password" name="password" required placeholder="Contraseña"><br>
        <input type="submit" class="btn_login" value="Login">
    </form>
    </div>
</body>
</html>