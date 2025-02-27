<?php
session_start(); // Iniciar la sesión

// Si el formulario ha sido enviado, guardamos la sede en la sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['sede'] = $_POST['sede'];
    header('Location: servicio.php'); // Redirigir a la página de selección de servicio
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
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
    <title>Kaizen - sedes</title>
    <style>
      .card .content_barber {
    position: relative;
    width: 250px;
    height: 100%;
    background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), 
            no-repeat center/cover url('img/sedeRodriguez.JPG');    
}

.card .content_barber2 {
    position: relative;
    width: 250px;
    height: 100%;
    background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), 
            no-repeat center/cover url('img/sedeDarregueira.JPG');    
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
  
  <section class="sedeContainer">
    <div>
      <form class="sede_container" action="index.php" method="POST">
        <h1 class="title_sede">NUESTRAS SEDES</h1>
        <hr>
        <div class="d-flex flex-wrap justify-content-center">
          <button type="submit" name="sede" value="Kaizen Rodríguez" class="card">
            <div class="content_barber">
              <span></span>
              <h4 class="sede_title">Kaizen Rodríguez</h4>
              <hr style="position: relative; top: 20px;">
              <span class="sede_direccion">RODRÍGUEZ 343</span>
            </div>
            
          </button>
          <button type="submit" name="sede" value="Kaizen Darregueira" class="card">
            <div class="content_barber2">
              <span></span>
              <h4 class="sede_title">Kaizen Darregueira</h4>
              <hr style="position: relative; top: 20px;">
              <span class="sede_direccion">DARREGUEIRA 961</span>
            </div>
            
          </button>
        </div>
      </form>
    </div>
  </section>
</body>
</html>
