<?php 
session_start(); 

if (!isset($_SESSION['sede']) || !isset($_SESSION['servicio'])) {
    header('Location: index.php');
    exit();
}

$sede = $_SESSION['sede'];
$servicio = $_SESSION['servicio'];

$profesionales_por_sede_y_servicio = [
    'Kaizen Rodríguez' => [
        'Rehabilitación' => ['Franco Schroh', 'Francisco Gomez', 'Gaston Coto','Sebastián Mazzeo', 'Micaela Pérez'],
        'Evaluacion kinesica' => ['Franco Schroh', 'Francisco Gomez', 'Gaston Coto','Sebastián Mazzeo', 'Micaela Pérez'],
        'Método Busquet' => ['Marcos Luis', 'Gaston Coto'],
        'Disfunción ATM' => ['Franco Schroh'],
        'Terapia Manual' => ['Franco Schroh'],
        'Kinefilaxia' => ['Leonel Scolari'],
    ],
    'Kaizen Darregueira' => [
        'Rehabilitación' => ['Francisco Gomez', 'Gaston Olgiati', 'Gaston Coto','Sebastián Mazzeo'],
        'Evaluación deportiva integral' => ['Marcos Luis'],
    ],
];


$profesionales = $profesionales_por_sede_y_servicio[$sede][$servicio] ?? [];

if (empty($profesionales)) {
    $no_profesionales_msg = "
    <div class='falta_profesionalesContainer'>
        <p class='falta_profesionales'>No hay profesionales disponibles para el servicio de $servicio en la sede $sede actualmente.</p>
        <hr>
        <span class='falta_profesionales'>Contacta con nuestra sede</span>
        <a href='https://wa.me/5492915347980' class='whatsapp-button' target='_blank'>
            <i style='color: #fff;' class='fa-solid fa-phone'></i>
        </a>
    </div>
    ";
} else {
    $no_profesionales_msg = "";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['profesional'] = $_POST['profesional'];
    header('Location: fecha.php');
    exit();
}

// Asociar imágenes a cada profesional
$imagenes_profesionales = [
    'Franco Schroh' => 'img/franco.jpg',
    'Francisco Gomez' => 'img/franciscoS.jpg',
    'Gaston Olgiati' => 'img/GastonO.jpg',
    'Gaston Coto' => 'img/gastonC.jpg',
    'Sebastián Mazzeo' => 'img/sebastian.jpg',
    'Micaela Pérez' => 'img/mica.jpg',
    'Marcos Luis' => 'img/marcos.jpg',
    'Leonel Scolari' => 'img/leonel.jpg',
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
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
    <title>Kaizen - profesionales</title>
    <style>
    
    .profesionalContainerImg{
    width: 84px;
    height: 84px;
    transform: scale(1.05);

    }

.profesional_card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    position: relative;
    bottom: 15px;
}

@media (max-width: 780px) {
    .profesionalContainerImg{
    width: 54px;
    height: 54px;
    transform: scale(1.05);
    }
}
    .falta_profesionalesContainer{
        margin: auto;
        position: relative;
        top: 100px;
        background-color:rgb(190, 182, 182);
        padding: 30px;
        border-radius: 10px;
        max-width: 60%;
        text-align: center;
        z-index: 100;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
    }



    .falta_profesionalesContainer p{
        font-size: 18px;
        font-weight: 500;
    }

    .falta_profesionalesContainer span{
        font-size: 23px;
        font-weight: 500;
    }

    .falta_profesionalesContainer a{
        background-color: #25D366;
        width: 150px;
        height: 40px;
        border-radius: 10px;
        text-align: center;
        padding: 5px;
        margin: auto;
        position: relative;
        top: 15px;
        font-size: 20px;
    }

    .falta_profesionalesContainer a{
        background-color: #25D366;
        width: 150px;
        height: 40px;
        border-radius: 10px;
        text-align: center;
        padding: 5px;
        margin: auto;
        position: relative;
        top: 15px;
        font-size: 20px;
    }

    .falta_profesionalesContainer a:hover {
        background-color:rgb(16, 153, 66);
        transition: .5s ;
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

    <h1 class="servicio_title">NUESTROS PROFESIONALES</h1>
    <?php echo $no_profesionales_msg; ?>
    <hr style="position: relative; top: 40px; width: 60%; margin: auto;">
    <form class="profesionales_container" action="profesionales.php" method="POST">
    <?php foreach ($profesionales as $profesional): ?>
        <button class="profesional_card" type="submit" name="profesional" value="<?php echo $profesional; ?>">
            <div class="profesionalContainerImg">
                <img src="<?php echo $imagenes_profesionales[$profesional] ?? 'img/profesionales/default.jpg'; ?>" alt="<?php echo $profesional; ?>">
            </div>
            <span><?php echo $profesional; ?></span>
        </button>
    <?php endforeach; ?>

    
</form>
</body>
</html>
