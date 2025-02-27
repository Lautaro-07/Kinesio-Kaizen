<?php
session_start(); // Continuar la sesión

// Si no se ha seleccionado una sede, redirigir a index.php
if (!isset($_SESSION['sede'])) {
    header('Location: index.php');
    exit();
}

// Obtener la sede seleccionada
$sedeSeleccionada = $_SESSION['sede'];

// Servicios disponibles según la sede
$servicios_por_sede = [
    "Kaizen Rodríguez" => [
        ["nombre" => "Rehabilitación", "descripcion" => "Rehabilitación traumatológica o deportiva (3.000$ con obra social).", "imagen" => "img/rehabilitacion.jpg", "precio" => 11000],
        ["nombre" => "Evaluacion kinesica", "descripcion" => "Diseñamos un plan personalizado segun tu estado", "imagen" => "img/evaluacion_kinesica.jpg", "precio" => 15000],
        ["nombre" => "Método Busquet", "descripcion" => "Metodo de evaluación y tratamiento", "imagen" => "img/terapia_manual.jpg", "precio" => 30000],
        ["nombre" => "Terapia Manual", "descripcion" => "Servicio personalizado utilizando tecnicas osteopaticas.", "imagen" => "img/metodo_busquet.jpg", "precio" => 30000],
        ["nombre" => "Disfunción ATM", "descripcion" => "Tratamiento especializado para ATM.", "imagen" => "img/disfuncion_atm.jpg", "precio" => 30000],
        ["nombre" => "Kinefilaxia", "descripcion" => "Programa mensual de cuidado a traves del movimiento corporal.", "imagen" => "img/kinefilaxia.jpg", "precio" => 25000]
    ],
    "Kaizen Darregueira" => [
        ["nombre" => "Rehabilitación", "descripcion" => "Rehabilitación integral y personalizada.", "imagen" => "img/rehabilitacion.jpg", "precio" => 3000],
        ["nombre" => "Evaluación Deportiva Integral", "descripcion" => "Evaluación especializada para atletas.", "imagen" => "img/evaluacion_deportiva.jpg", "precio" => 11000]
    ]
];


$servicios = $servicios_por_sede[$sedeSeleccionada] ?? [];

// Si el formulario es enviado, guardamos el servicio en la sesión y redirigimos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['servicio'] = $_POST['servicio'];
    header('Location: profesionales.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" href="img/ISO_Violeta.png">
    <title>Kaizen - Servicios - <?= htmlspecialchars($sedeSeleccionada); ?></title>
    <style>
        :root {
            --background-dark: #2d3548;
            --text-light: rgba(255, 255, 255, 0.6);
            --text-lighter: rgba(255, 255, 255, 0.9);
            --spacing-s: 8px;
            --spacing-l: 24px;
            --spacing-xl: 32px;
            --spacing-xxl: 64px;
            --width-container: 1200px;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        .hero-section {
            display: flex;
            justify-content: center;
            padding: var(--spacing-xxl) var(--spacing-l);
        }
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-l);
            max-width: var(--width-container);
            width: 80%;
        }
        .card {
            position: relative;
            overflow: hidden;
            border-radius: var(--spacing-l);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            text-decoration: none;
        }
        .card__background {
            background-size: cover;
            background-position: center;
            filter: brightness(0.75);
            transition: transform 0.3s, filter 0.3s;
            height: 200px;
        }
        .card__content {
            padding: var(--spacing-l);
            background: white;
            text-align: left;
            position: relative;
            bottom: 10px;
        }
        .card__category {
            color: var(--text-light);
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .card__heading {
            color: var(--background-dark);
            font-size: 1.3rem;
            margin: var(--spacing-s) 0 0;
        }
        .card:hover .card__background {
            filter: brightness(0.9);
            transform: scale(1.05);
        }
        .card__footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            top: 10px;
        }
        .card__price {
            font-size: 1rem;
            color: #777;
            font-weight: 600;
            position: relative;
            top: 8px;
            right: 10px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="nav_container">
            <div class="logo_container">
                <img src="img/ISO_Violeta.png" alt="Logo" class="logo">
            </div>
        </nav>
    </header>
    <div class="color"></div>

    <h1 class="servicio_title">NUESTROS SERVICIOS</h1>
    <hr style="position: relative; top: 40px; width: 60%; margin: auto;">
    <section class="hero-section">
    <div class="card-grid">
            <?php foreach ($servicios as $servicio): ?>
                <form action="" method="POST" class="card">
                    <div class="card__background" style="background-image: url(<?= htmlspecialchars($servicio['imagen']); ?>);"></div>
                    <div class="card__content">
                        <p class="card__category"><?= htmlspecialchars($sedeSeleccionada); ?></p>
                        <h3 class="card__heading"><?= htmlspecialchars($servicio['nombre']); ?></h3>
                        <p><?= htmlspecialchars($servicio['descripcion']); ?></p>
                        <div class="card__footer">
                            <button type="submit" class="btn_servicios" name="servicio" value="<?= htmlspecialchars($servicio['nombre']); ?>">Seleccionar</button>
                            <span class="card__price">$<?= number_format($servicio['precio'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    </section>
</body>
</html>
