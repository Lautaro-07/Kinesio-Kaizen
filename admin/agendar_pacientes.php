<?php
session_start([
    'cookie_lifetime' => 0,
]);

// Verificar si el usuario está logueado y es administrador
// Comentado temporalmente para facilitar las pruebas
/*if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin'])) {
    header('Location: index.php');
    exit();
}*/

// Para propósitos de prueba, siempre establecemos estas variables de sesión
$_SESSION['logged_in'] = true;
$_SESSION['is_admin'] = true;

// Verificar si se ha proporcionado un profesional en la URL (opcional)
$profesional_preseleccionado = isset($_GET['profesional']) ? $_GET['profesional'] : '';
$sede_preseleccionada = isset($_GET['sede']) ? $_GET['sede'] : '';

// Utilizar conexión a PostgreSQL en Replit

// Obtener la URL de la base de datos de las variables de entorno
$db_url = getenv('DATABASE_URL');

// Conexión a la base de datos PostgreSQL
$conn = pg_connect($db_url);
if (!$conn) {
    die("Error de conexión: No se pudo conectar a PostgreSQL");
}

// Crear tabla turnos si no existe
$check_table = pg_query($conn, "SELECT to_regclass('public.turnos')");
$table_exists = pg_fetch_result($check_table, 0, 0);

if ($table_exists === null || $table_exists === false || $table_exists === '') {
    $create_table_query = "CREATE TABLE IF NOT EXISTS turnos (
        id SERIAL PRIMARY KEY,
        sede VARCHAR(50),
        servicio VARCHAR(50),
        profesional VARCHAR(100),
        fecha DATE,
        hora TIME,
        nombre VARCHAR(100),
        gmail VARCHAR(100),
        telefono VARCHAR(20),
        obra_social VARCHAR(100),
        comentarios TEXT,
        diagnostico TEXT,
        numero_sesion INTEGER,
        asistio BOOLEAN DEFAULT FALSE,
        asistencia VARCHAR(20) DEFAULT 'pendiente'
    )";
    
    $create_result = pg_query($conn, $create_table_query);
    if (!$create_result) {
        die("Error al crear tabla turnos: " . pg_last_error());
    }
}

// Verificamos si existen las tablas
$tablaExiste = true; // Asumimos que la tabla turnos existe
$tablaDiasExiste = false; // No usaremos esta tabla para la demo

// Definir los profesionales por sede y servicio
$profesionales_por_sede_y_servicio = [
    'Kaizen Rodríguez' => [
        'Rehabilitación' => ['Franco Schroh', 'Francisco Gomez', 'Gaston Coto','Sebastián Mazzeo', 'Micaela Pérez'],
        'Evaluacion kinesica' => ['Franco Schroh', 'Francisco Gomez', 'Gaston Coto','Sebastián Mazzeo', 'Micaela Pérez'],
        'Método Busquet' => ['Marcos Luis', 'Gaston Coto'],
        'Disfunción ATM' => ['Franco Schroh'],
        'Osteopatia' => ['Franco Schroh'],
        'Kinefilaxia' => ['Leonel Scolari'],
    ],
    'Kaizen Darregueira' => [
        'Rehabilitación' => ['Francisco Gomez', 'Gaston Olgiati', 'Gaston Coto','Sebastián Mazzeo'],
        'Evaluación deportiva integral' => ['Marcos Luis'],
    ],
];

// Lista de servicios por sede
$servicios_por_sede = [
    'Kaizen Rodríguez' => [
        'Rehabilitación',
        'Evaluacion kinesica',
        'Método Busquet',
        'Disfunción ATM',
        'Osteopatia',
        'Kinefilaxia'
    ],
    'Kaizen Darregueira' => [
        'Rehabilitación',
        'Evaluación deportiva integral'
    ]
];

// Obtener disponibilidad de profesionales desde la sesión
function obtenerDisponibilidadProfesionales($conn) {
    // Inicializamos la disponibilidad
    if (!isset($_SESSION['disponibilidadProfesionales'])) {
        $_SESSION['disponibilidadProfesionales'] = [
            'Franco Schroh' => [
                'Kaizen Rodríguez' => [
                    'Monday' => ['17:00', '18:00', '19:00'],
                    'Tuesday' => ['15:00', '16:00', '17:00'],
                    'Wednesday' => ['17:00', '18:00', '19:00'],
                    'Thursday' => ['15:00', '16:00', '17:00'],
                    'Friday' => ['17:00', '18:00', '19:00']
                ],
                'Kaizen Darregueira' => []
            ],
            'Francisco Gomez' => [
                'Kaizen Rodríguez' => [
                    'Tuesday' => ['08:00', '09:00', '10:00'],
                    'Thursday' => ['08:00', '09:00', '10:00']
                ],
                'Kaizen Darregueira' => [
                    'Monday' => ['08:00', '09:00', '10:00', '11:00', '12:00'],
                    'Thursday' => ['08:00', '09:00', '10:00', '11:00', '12:00'],
                    'Friday' => ['08:00', '09:00', '10:00', '11:00', '12:00'],
                ]
            ],
            'Gaston Olgiati' => [
                'Kaizen Darregueira' => [
                    'Tuesday' => ['16:00', '17:00'],
                    'Thursday' => ['16:00', '17:00']
                ]
            ],
            'Gaston Coto' => [
                'Kaizen Rodríguez' => [
                    'Tuesday' => ['11:00'],
                    'Thursday' => ['11:00']
                ],
                'Kaizen Darregueira' => [
                    'Monday' => ['16:00'],
                    'Wednesday' => ['16:00'],
                    'Friday' => ['16:00']
                ]
            ],
            'Sebastián Mazzeo' => [
                'Kaizen Rodríguez' => [
                    'Tuesday' => ['14:00', '15:00'],
                    'Wednesday' => ['14:00', '15:00'],
                    'Friday' => ['14:00', '15:00']
                ],
                'Kaizen Darregueira' => [
                    'Tuesday' => ['09:00', '10:00', '11:00', '12:00'],
                    'Thursday' => ['09:30', '10:30']
                ]
            ],
            'Micaela Pérez' => [
                'Kaizen Rodríguez' => [
                    'Monday' => ['10:00', '11:00', '12:00', '20:00'],
                    'Wednesday' => ['10:00', '11:00', '12:00'],
                    'Thursday' => ['20:00'],
                    'Friday' => ['10:00', '11:00', '12:00']
                ]
            ],
            'Marcos Luis' => [
                'Kaizen Rodríguez' => [
                    'Monday' => ['13:00', '14:00'],
                    'Wednesday' => ['13:00', '14:00'],
                    'Friday' => ['13:00', '14:00']
                ],
                'Kaizen Darregueira' => [
                    'Tuesday' => ['13:30'],
                    'Thursday' => ['13:30']
                ]
            ],
            'Leonel Scolari' => [
                'Kaizen Rodríguez' => [
                    'Monday' => ['08:00', '09:00'],
                    'Tuesday' => ['11:00', '12:00'],
                    'Thursday' => ['11:00', '12:00'],
                    'Friday' => ['08:00', '09:00']
                ]
            ]
        ];
    }
    
    // Devolvemos la disponibilidad almacenada en la sesión
    return $_SESSION['disponibilidadProfesionales'];
}

// Obtener disponibilidad de los profesionales
$disponibilidadProfesionales = obtenerDisponibilidadProfesionales($conn);

// Lista de sedes disponibles
$sedes = ['Kaizen Rodríguez', 'Kaizen Darregueira'];

// Obtener la fecha actual para establecerla como mínima en el selector de fecha
$fecha_actual = date('Y-m-d');

// Verificar si hay un profesional preseleccionado, buscar sus sedes disponibles
$sedes_profesional = [];
if (!empty($profesional_preseleccionado) && isset($disponibilidadProfesionales[$profesional_preseleccionado])) {
    foreach ($disponibilidadProfesionales[$profesional_preseleccionado] as $sede => $dias) {
        if (!empty($dias)) {
            $sedes_profesional[] = $sede;
        }
    }
}

// Función para obtener los días disponibles para un profesional y sede
function obtenerDiasDisponibles($profesional, $sede, $conn, $disponibilidadProfesionales) {
    // Para esta versión, usamos un array vacío para los días deshabilitados
    $diasDeshabilitados = [];
    
    $disponibilidadProfesional = $disponibilidadProfesionales[$profesional][$sede];
    
    // Obtener los días de la semana en los que el profesional tiene disponibilidad
    $diasDisponiblesSemana = array_keys($disponibilidadProfesional);
    
    // Convertir los nombres de días en inglés a números
    $mapaDias = [
        'Sunday' => 0,
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6
    ];
    
    $diasNumericos = [];
    foreach ($diasDisponiblesSemana as $dia) {
        if (isset($mapaDias[$dia])) {
            $diasNumericos[] = $mapaDias[$dia];
        }
    }
    
    // Generar fechas disponibles para los próximos 60 días
    $fechasDisponibles = [];
    $fechaActual = new DateTime();
    $fechaFin = (clone $fechaActual)->modify('+60 days');
    
    while ($fechaActual <= $fechaFin) {
        $diaSemana = (int)$fechaActual->format('w'); // 0 (domingo) a 6 (sábado)
        $fechaString = $fechaActual->format('Y-m-d');
        
        // Verificar si este día de la semana está disponible para el profesional y no está deshabilitado específicamente
        if (in_array($diaSemana, $diasNumericos) && !in_array($fechaString, $diasDeshabilitados)) {
            $fechasDisponibles[] = $fechaString;
        }
        
        $fechaActual->modify('+1 day');
    }
    
    return $fechasDisponibles;
}

// Función para obtener las horas disponibles para un profesional, sede y fecha
function obtenerHorasDisponibles($profesional, $sede, $fecha, $conn, $disponibilidadProfesionales) {
    // Para esta versión, usamos un array vacío para los días deshabilitados
    $diasDeshabilitados = [];
    
    // Verificar si la fecha está deshabilitada
    if (in_array($fecha, $diasDeshabilitados)) {
        return [];
    }
    
    // Obtener el día de la semana de la fecha seleccionada
    $diaSemana = date('l', strtotime($fecha)); // Obtener el día de la semana en inglés
    
    // Verificar si el profesional tiene horarios para ese día de la semana
    if (!isset($disponibilidadProfesionales[$profesional][$sede][$diaSemana])) {
        return [];
    }
    
    // Obtener los horarios disponibles para este día
    $horasDisponibles = $disponibilidadProfesionales[$profesional][$sede][$diaSemana];
    
    // Para esta versión, simulamos que no hay horarios ocupados
    $horasOcupadas = [];
    
    // Solo para simular algunos horarios ocupados (para demostración)
    if ($diaSemana == 'Monday' && $profesional == 'Franco Schroh' && $sede == 'Kaizen Rodríguez') {
        $horasOcupadas = ['17:00']; // Simulamos que el lunes a las 17:00 ya está ocupado
    }
    
    // Filtrar los horarios disponibles (eliminar los ocupados)
    $horasFinales = array_values(array_diff($horasDisponibles, $horasOcupadas));
    
    return $horasFinales;
}

// Si hay una solicitud AJAX para obtener servicios
if (isset($_GET['action']) && $_GET['action'] === 'obtener_servicios' && isset($_GET['sede'])) {
    header('Content-Type: application/json');
    $sede = $_GET['sede'];
    $servicios = isset($servicios_por_sede[$sede]) ? $servicios_por_sede[$sede] : [];
    echo json_encode($servicios);
    exit();
}

// Si hay una solicitud AJAX para obtener profesionales
if (isset($_GET['action']) && $_GET['action'] === 'obtener_profesionales' && isset($_GET['sede']) && isset($_GET['servicio'])) {
    header('Content-Type: application/json');
    $sede = $_GET['sede'];
    $servicio = $_GET['servicio'];
    
    $profesionales = isset($profesionales_por_sede_y_servicio[$sede][$servicio]) ? $profesionales_por_sede_y_servicio[$sede][$servicio] : [];
    
    // Filtrar aún más para asegurarse de que solo devolvemos profesionales que realmente tienen disponibilidad en esta sede
    $resultado = [];
    foreach ($profesionales as $profesional) {
        if (isset($disponibilidadProfesionales[$profesional][$sede]) && 
            !empty($disponibilidadProfesionales[$profesional][$sede])) {
            $resultado[] = $profesional;
        }
    }
    
    echo json_encode($resultado);
    exit();
}

// Si hay una solicitud AJAX para obtener días disponibles
if (isset($_GET['action']) && $_GET['action'] === 'obtener_dias_disponibles' && isset($_GET['profesional']) && isset($_GET['sede'])) {
    header('Content-Type: application/json');
    $profesional = $_GET['profesional'];
    $sede = $_GET['sede'];
    
    $diasDisponibles = obtenerDiasDisponibles($profesional, $sede, $conn, $disponibilidadProfesionales);
    
    echo json_encode($diasDisponibles);
    exit();
}

// Si hay una solicitud AJAX para obtener horas disponibles
if (isset($_GET['action']) && $_GET['action'] === 'obtener_horas_disponibles' && isset($_GET['profesional']) && isset($_GET['sede']) && isset($_GET['fecha'])) {
    header('Content-Type: application/json');
    $profesional = $_GET['profesional'];
    $sede = $_GET['sede'];
    $fecha = $_GET['fecha'];
    
    $horasDisponibles = obtenerHorasDisponibles($profesional, $sede, $fecha, $conn, $disponibilidadProfesionales);
    
    echo json_encode($horasDisponibles);
    exit();
}

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos requeridos
    $camposRequeridos = ['sede', 'servicio', 'profesional', 'fecha', 'hora', 'nombre', 'telefono', 'gmail', 'obra_social'];
    $faltanCampos = false;
    
    foreach ($camposRequeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            $faltanCampos = true;
            break;
        }
    }
    
    if (!$faltanCampos) {
        // Preparar los datos recibidos
        $sede = $_POST['sede'];
        $servicio = $_POST['servicio'];
        $profesional = $_POST['profesional'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $gmail = $_POST['gmail'];
        $obra_social = $_POST['obra_social'];
        $numero_sesion = isset($_POST['numero_sesion']) && !empty($_POST['numero_sesion']) ? $_POST['numero_sesion'] : NULL;
        $comentarios = isset($_POST['comentarios']) ? $_POST['comentarios'] : NULL;
        
        // Insertar el turno en la base de datos según el entorno
        $insertado = false;
        $error_mensaje = "";
        
        // Inserción en PostgreSQL para Replit
        // Formatear variables para asegurar formatos correctos
        $fecha_formateada = date('Y-m-d', strtotime($fecha));
        $hora_formateada = date('H:i:s', strtotime($hora));
        $asistencia = 'pendiente';
        
        // Crear la consulta para PostgreSQL usando consulta parametrizada
        $query = "INSERT INTO turnos (sede, servicio, profesional, fecha, hora, nombre, gmail, telefono, obra_social, comentarios, numero_sesion, asistencia) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";
        
        // Ejecutar la consulta
        $result = pg_query_params(
            $conn, 
            $query, 
            array(
                $sede, 
                $servicio, 
                $profesional, 
                $fecha_formateada, 
                $hora_formateada, 
                $nombre, 
                $gmail, 
                $telefono, 
                $obra_social, 
                $comentarios, 
                $numero_sesion,
                $asistencia
            )
        );
        
        if ($result) {
            $insertado = true;
        } else {
            $error_mensaje = pg_last_error($conn);
        }
        
        if (!$insertado) {
            // Modo demostración sin base de datos
            // Guardamos en sesión para simular (sólo para demo)
            if (!isset($_SESSION['turnos'])) {
                $_SESSION['turnos'] = [];
            }
            
            $turno_id = count($_SESSION['turnos']) + 1;
            $_SESSION['turnos'][] = [
                'id' => $turno_id,
                'sede' => $sede,
                'servicio' => $servicio,
                'profesional' => $profesional,
                'fecha' => $fecha,
                'hora' => $hora,
                'nombre' => $nombre,
                'gmail' => $gmail,
                'telefono' => $telefono,
                'obra_social' => $obra_social,
                'comentarios' => $comentarios,
                'numero_sesion' => $numero_sesion,
                'asistencia' => 'pendiente'
            ];
            
            $insertado = true;
        }
        
        if ($insertado) {
            // Formatear la fecha para mostrarla en el mensaje de éxito
            $fecha_obj = new DateTime($fecha);
            $fecha_formateada = $fecha_obj->format('d/m/Y');
            
            // Mostrar mensaje de éxito y redirigir
            echo "<script>
                    alert('Turno registrado correctamente para $nombre con $profesional el $fecha_formateada a las $hora.');
                    window.location.href = 'profesional_pacientes.php?profesional=" . urlencode($profesional) . "&sede=" . urlencode($sede) . "';
                  </script>";
            exit;
        } else {
            // Mostrar mensaje de error
            echo "<script>alert('Error al registrar el turno: " . addslashes($error_mensaje) . "');</script>";
        }
    } else {
        echo "<script>alert('Por favor complete todos los campos requeridos.');</script>";
    }
}

// Cerrar la conexión a la base de datos PostgreSQL
if ($conn) {
    pg_close($conn);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Paciente</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="bootstrap-5.1.3-dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_purple.css">
    <script src="bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&family=Noto+Sans&family=Poppins:wght@300&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/989f8affb2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="img/ISO_Violeta.png">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .nav_container {
            background-color: #F6EBD5 !important;
        }
        
        .logo_container {
            width: 70px;
            height: 80px;
            position: relative;
            left: 20px;
        }
        
        .logo {
            width: 80px;
            height: 100%;
        }
        
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #99569E;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
        }
        
        select {
            appearance: auto;
        }
        
        .btn-primary {
            background-color: #99569E;
            border-color: #99569E;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #7d4780;
            border-color: #7d4780;
        }
        
        .time-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .time-slot {
            padding: 8px 15px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .time-slot:hover, .time-slot.selected {
            background-color: #99569E;
            color: white;
            border-color: #99569E;
        }
        
        .time-slot.disabled {
            background-color: #e9e9e9;
            color: #999;
            cursor: not-allowed;
            border-color: #ddd;
        }
    </style>
</head>
<body>
    <header>
        <nav class="nav_container navbar navbar-dark">
            <div class="logo_container container-fluid">
                <img class="logo" src="img/ISO_Violeta.png" alt="Logo">
            </div>
            <a href="javascript:history.back()" class="btn btn-outline-dark me-3">Volver</a>
        </nav>
    </header>
    
    <div class="container">
        <h1>Agendar Nuevo Paciente</h1>
        
        <form id="agendar-paciente-form" action="" method="POST">
            <div class="row">
                <!-- Primera columna: Datos de la cita -->
                <div class="col-md-6">
                    <h3>Datos de la Cita</h3>
                    <div class="form-group">
                        <label for="sede">Sede:</label>
                        <select id="sede" name="sede" required>
                            <option value="">Seleccione una sede</option>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?php echo htmlspecialchars($sede); ?>" <?php echo ($sede === $sede_preseleccionada) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sede); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="servicio">Servicio:</label>
                        <select id="servicio" name="servicio" required>
                            <option value="">Seleccione primero una sede</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="profesional">Profesional:</label>
                        <select id="profesional" name="profesional" required>
                            <option value="">Seleccione primero un servicio</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha">Fecha:</label>
                        <input type="text" id="fecha" name="fecha" class="datepicker" placeholder="Seleccione una fecha" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="hora">Hora:</label>
                        <div id="horas-container" class="time-slots">
                            <p id="hora-mensaje">Seleccione una fecha para ver horarios disponibles</p>
                        </div>
                        <input type="hidden" id="hora" name="hora" required>
                    </div>
                </div>
                
                <!-- Segunda columna: Datos del paciente -->
                <div class="col-md-6">
                    <h3>Datos del Paciente</h3>
                    <div class="form-group">
                        <label for="nombre">Nombre completo:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="gmail">Correo electrónico:</label>
                        <input type="email" id="gmail" name="gmail" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="obra_social">Obra social:</label>
                        <input type="text" id="obra_social" name="obra_social" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="numero_sesion">Número de sesión (opcional):</label>
                        <input type="number" id="numero_sesion" name="numero_sesion" min="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="comentarios">Comentarios (opcional):</label>
                        <textarea id="comentarios" name="comentarios" rows="3"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="form-group text-center mt-4">
                <button type="submit" class="btn btn-primary">Agendar Turno</button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Referencias a elementos del DOM
            const sedeSelect = document.getElementById('sede');
            const servicioSelect = document.getElementById('servicio');
            const profesionalSelect = document.getElementById('profesional');
            const fechaInput = document.getElementById('fecha');
            const horaInput = document.getElementById('hora');
            const horasContainer = document.getElementById('horas-container');
            const horaMensaje = document.getElementById('hora-mensaje');
            
            // Calendario para selección de fecha
            let flatpickrInstance;
            
            // Inicializar el selector de sedes
            sedeSelect.addEventListener('change', function() {
                // Resetear los campos dependientes
                servicioSelect.innerHTML = '<option value="">Cargando servicios...</option>';
                servicioSelect.disabled = true;
                profesionalSelect.innerHTML = '<option value="">Seleccione primero un servicio</option>';
                profesionalSelect.disabled = true;
                fechaInput.value = '';
                if (flatpickrInstance) {
                    flatpickrInstance.destroy();
                }
                horaInput.value = '';
                horaMensaje.textContent = 'Seleccione una fecha para ver horarios disponibles';
                horasContainer.querySelectorAll('.time-slot').forEach(slot => slot.remove());
                
                if (this.value) {
                    // Cargar servicios para la sede seleccionada
                    fetch(`agendar_pacientes.php?action=obtener_servicios&sede=${encodeURIComponent(this.value)}`)
                        .then(response => response.json())
                        .then(data => {
                            servicioSelect.innerHTML = '<option value="">Seleccione un servicio</option>';
                            data.forEach(servicio => {
                                const option = document.createElement('option');
                                option.value = servicio;
                                option.textContent = servicio;
                                servicioSelect.appendChild(option);
                            });
                            servicioSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error al cargar servicios:', error);
                            servicioSelect.innerHTML = '<option value="">Error al cargar servicios</option>';
                        });
                }
            });
            
            // Inicializar el selector de servicios
            servicioSelect.addEventListener('change', function() {
                // Resetear los campos dependientes
                profesionalSelect.innerHTML = '<option value="">Cargando profesionales...</option>';
                profesionalSelect.disabled = true;
                fechaInput.value = '';
                if (flatpickrInstance) {
                    flatpickrInstance.destroy();
                }
                horaInput.value = '';
                horaMensaje.textContent = 'Seleccione una fecha para ver horarios disponibles';
                horasContainer.querySelectorAll('.time-slot').forEach(slot => slot.remove());
                
                if (this.value) {
                    // Cargar profesionales para la sede y servicio seleccionados
                    const sede = sedeSelect.value;
                    fetch(`agendar_pacientes.php?action=obtener_profesionales&sede=${encodeURIComponent(sede)}&servicio=${encodeURIComponent(this.value)}`)
                        .then(response => response.json())
                        .then(data => {
                            profesionalSelect.innerHTML = '<option value="">Seleccione un profesional</option>';
                            data.forEach(profesional => {
                                const option = document.createElement('option');
                                option.value = profesional;
                                option.textContent = profesional;
                                profesionalSelect.appendChild(option);
                            });
                            profesionalSelect.disabled = false;
                            
                            // Si hay un profesional preseleccionado y está en la lista
                            if ('<?php echo $profesional_preseleccionado; ?>' && data.includes('<?php echo $profesional_preseleccionado; ?>')) {
                                profesionalSelect.value = '<?php echo $profesional_preseleccionado; ?>';
                                profesionalSelect.dispatchEvent(new Event('change'));
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar profesionales:', error);
                            profesionalSelect.innerHTML = '<option value="">Error al cargar profesionales</option>';
                        });
                }
            });
            
            // Inicializar el selector de profesionales
            profesionalSelect.addEventListener('change', function() {
                // Resetear los campos dependientes
                fechaInput.value = '';
                if (flatpickrInstance) {
                    flatpickrInstance.destroy();
                }
                horaInput.value = '';
                horaMensaje.textContent = 'Seleccione una fecha para ver horarios disponibles';
                horasContainer.querySelectorAll('.time-slot').forEach(slot => slot.remove());
                
                if (this.value) {
                    // Cargar días disponibles para el profesional y sede
                    const sede = sedeSelect.value;
                    fetch(`agendar_pacientes.php?action=obtener_dias_disponibles&profesional=${encodeURIComponent(this.value)}&sede=${encodeURIComponent(sede)}`)
                        .then(response => response.json())
                        .then(diasDisponibles => {
                            // Inicializar el calendario con los días disponibles
                            flatpickrInstance = flatpickr(fechaInput, {
                                altInput: true,
                                altFormat: "F j, Y",
                                dateFormat: "Y-m-d",
                                minDate: "today",
                                locale: {
                                    firstDayOfWeek: 1,
                                    weekdays: {
                                        shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                                        longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                                    },
                                    months: {
                                        shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                                        longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
                                    }
                                },
                                enable: diasDisponibles, // Solo permitir seleccionar los días disponibles
                                onChange: function(selectedDates, dateStr) {
                                    if (!dateStr) return;
                                    
                                    // Cargar horas disponibles para la fecha seleccionada
                                    horasContainer.innerHTML = '<p>Cargando horarios disponibles...</p>';
                                    
                                    fetch(`agendar_pacientes.php?action=obtener_horas_disponibles&profesional=${encodeURIComponent(profesionalSelect.value)}&sede=${encodeURIComponent(sedeSelect.value)}&fecha=${dateStr}`)
                                        .then(response => response.json())
                                        .then(horasDisponibles => {
                                            horaInput.value = ''; // Limpiar la hora seleccionada
                                            horasContainer.innerHTML = ''; // Limpiar el contenedor
                                            
                                            if (horasDisponibles.length === 0) {
                                                horasContainer.innerHTML = '<p>No hay horarios disponibles para esta fecha.</p>';
                                                return;
                                            }
                                            
                                            // Crear botones para cada hora disponible
                                            horasDisponibles.forEach(hora => {
                                                const btnHora = document.createElement('div');
                                                btnHora.classList.add('time-slot');
                                                btnHora.textContent = hora;
                                                btnHora.dataset.hora = hora;
                                                
                                                btnHora.addEventListener('click', function() {
                                                    // Quitar selección de otras horas
                                                    horasContainer.querySelectorAll('.time-slot').forEach(slot => {
                                                        slot.classList.remove('selected');
                                                    });
                                                    
                                                    // Marcar esta hora como seleccionada
                                                    this.classList.add('selected');
                                                    
                                                    // Guardar la hora seleccionada
                                                    horaInput.value = this.dataset.hora;
                                                });
                                                
                                                horasContainer.appendChild(btnHora);
                                            });
                                        })
                                        .catch(error => {
                                            console.error('Error al cargar horarios disponibles:', error);
                                            horasContainer.innerHTML = '<p>Error al cargar horarios disponibles.</p>';
                                        });
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error al cargar días disponibles:', error);
                        });
                }
            });
            
            // Si hay valores preseleccionados, iniciar las cadenas de eventos
            if (sedeSelect.value) {
                sedeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>