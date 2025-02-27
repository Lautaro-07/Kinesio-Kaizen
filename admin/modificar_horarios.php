<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Conectar a la base de datos
$mysqli = new mysqli("localhost", "root", "", "turnos");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$profesional = $_SESSION['profesional'];

// Cuando se envíen los nuevos horarios, guardarlos en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fechas_horas = json_decode($_POST['fechas_horas'], true);

    // Primero, eliminar las entradas existentes para el profesional
    $stmt = $mysqli->prepare("DELETE FROM disponibilidad WHERE profesional = ?");
    $stmt->bind_param("s", $profesional);
    $stmt->execute();

    // Luego, insertar los nuevos horarios
    foreach ($fechas_horas as $fecha_hora) {
        $stmt = $mysqli->prepare("INSERT INTO disponibilidad (profesional, fecha, hora, habilitado) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $profesional, $fecha_hora['fecha'], $fecha_hora['hora']);
        $stmt->execute();
    }

    echo "<script>alert('Horarios actualizados correctamente.'); window.location.href = 'pacientes.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Horarios</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="bootstrap-5.1.3-dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&family=Noto+Sans&family=Poppins:wght@300&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/989f8affb2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
    <link rel="icon" href="img/ISO_Violeta.png">
    <style>
        .fecha {
            position: relative;
            top: 80px;
        }

        .calendar-container {
            max-width: 400px;
            margin: 0px auto;
            position: relative;
            top: 50px;
        }
        .flatpickr-calendar {
            font-size: 16px;
            padding: 10px;
        }
        .time-card {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .time-slot {
            display: inline-block;
            margin: 5px;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            background-color: #f1f1f1;
        }
        .time-slot.disabled {
            background-color: #e0e0e0;
            cursor: not-allowed;
        }
        .time-slot.available:hover {
            background-color: #8A2BE2;
            color: white;
        }
        #modal-content {
            background-color: #fff;
            color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        #confirmarHorario {
            display: none;
            background-color: #8A2BE2;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            bottom: 15px;
        }
        #closeModal {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }
        button:hover {
            background-color: #45a049;
        }
        .flatpickr-clear {
            display: none;
        }
        .servicio_title {
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

    <h1 class="servicio_title">MODIFICAR HORARIOS</h1>
    <hr style="position: relative; top: 40px; width: 60%; margin: auto;">
    <div class="calendar-container">
        <form action="modificar_horarios.php" method="POST" class="fecha_container" id="horarioForm">
            <input type="text" id="fecha" class="fecha" name="fecha" placeholder="Selecciona la fecha" required><br><br>
            <input type="hidden" id="hora" name="hora">
            <button type="submit" id="confirmarHorario" style="display:none;">Confirmar Horarios</button>
        </form>
    </div>

    <!-- Modal de selección de hora -->
    <div id="horaModal" class="time-card">
        <div id="modal-content">
            <h2>Selecciona la Hora</h2>
            <div id="horaContenedor"></div><br><br>
            <button type="button" id="confirmarHorario" onclick="guardarHorario()">Confirmar Horarios</button>
            <br>
            <button id="closeModal">Cerrar</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    let nuevoHorario = [];

    // Inicializar el calendario
    flatpickr("#fecha", {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        minDate: "today",
        inline: true,
        onChange: function(selectedDates, dateStr, instance) {
            const fecha = dateStr;
            document.getElementById('horaModal').style.display = 'flex';

            // Mostrar las horas disponibles (9AM - 6PM)
            const horaContenedor = document.getElementById('horaContenedor');
            horaContenedor.innerHTML = ''; // Limpiar el contenedor de horas

            for (let i = 9; i <= 18; i++) {
                const hora = `${i}:00`;
                const div = document.createElement('div');
                div.classList.add('time-slot');
                div.textContent = hora;

                div.addEventListener('click', function() {
                    nuevoHorario.push({ fecha: fecha, hora: hora });
                    div.classList.add('selected');
                });

                horaContenedor.appendChild(div);
            }

            document.getElementById('confirmarHorario').style.display = 'inline-block';
        }
    });

    function guardarHorario() {
        const form = document.getElementById('horarioForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'fechas_horas';
        input.value = JSON.stringify(nuevoHorario);
        form.appendChild(input);
        form.submit();
    }

    // Cerrar el modal de selección de hora
    document.getElementById("closeModal").addEventListener('click', function() {
        document.getElementById('horaModal').style.display = 'none';
    });

    </script>
</body>
</html>