<?php
session_start([
    'cookie_lifetime' => 0, // La sesión se cierra cuando se cierra el navegador
]);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$profesional = $_SESSION['profesional'];
$sede = isset($_SESSION['sede']) ? $_SESSION['sede'] : '';

// Horarios disponibles por profesional y sede
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

$disponibilidadProfesionales = $_SESSION['disponibilidadProfesionales'];

// Inicialización del array de días deshabilitados
if (!isset($_SESSION['diasDeshabilitados'])) {
    $_SESSION['diasDeshabilitados'] = [];
}

// Manejo de deshabilitación de días específicos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deshabilitar_dia'])) {
        $fecha_deshabilitar = $_POST['fecha_deshabilitar'];
        
        // Inicializar array para profesional y sede si no existe
        if (!isset($_SESSION['diasDeshabilitados'][$profesional])) {
            $_SESSION['diasDeshabilitados'][$profesional] = [];
        }
        if (!isset($_SESSION['diasDeshabilitados'][$profesional][$sede])) {
            $_SESSION['diasDeshabilitados'][$profesional][$sede] = [];
        }
        
        // Agregar la fecha al array de días deshabilitados
        if (!in_array($fecha_deshabilitar, $_SESSION['diasDeshabilitados'][$profesional][$sede])) {
            $_SESSION['diasDeshabilitados'][$profesional][$sede][] = $fecha_deshabilitar;
        }
        
        echo "<script>alert('Día deshabilitado correctamente.'); window.location.href='pacientes.php';</script>";
    } elseif (isset($_POST['habilitar_dia'])) {
        $fecha_habilitar = $_POST['fecha_habilitar'];
        
        // Eliminar la fecha del array de días deshabilitados
        if (isset($_SESSION['diasDeshabilitados'][$profesional][$sede])) {
            $key = array_search($fecha_habilitar, $_SESSION['diasDeshabilitados'][$profesional][$sede]);
            if ($key !== false) {
                unset($_SESSION['diasDeshabilitados'][$profesional][$sede][$key]);
                $_SESSION['diasDeshabilitados'][$profesional][$sede] = array_values($_SESSION['diasDeshabilitados'][$profesional][$sede]); // Reindexar
            }
        }
        
        echo "<script>alert('Día habilitado correctamente.'); window.location.href='pacientes.php';</script>";
    }
}

// Período de visualización (día, semana, mes)
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'mes';

// Obtener el mes seleccionado (mes actual por defecto)
$mes_actual = date('m');
$anio_actual = date('Y');
$mes = isset($_GET['mes']) ? $_GET['mes'] : $mes_actual;
$anio = isset($_GET['anio']) ? $_GET['anio'] : $anio_actual;

// Calcular mes anterior y siguiente
function obtenerMesAnterior($mes, $anio) {
    if ($mes == 1) {
        return [12, $anio - 1];
    }
    return [$mes - 1, $anio];
}

function obtenerMesSiguiente($mes, $anio) {
    if ($mes == 12) {
        return [1, $anio + 1];
    }
    return [$mes + 1, $anio];
}

list($mes_anterior, $anio_anterior) = obtenerMesAnterior($mes, $anio);
list($mes_siguiente, $anio_siguiente) = obtenerMesSiguiente($mes, $anio);

// Variables para búsqueda
$busqueda_nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$busqueda_obra_social = isset($_GET['obra_social']) ? $_GET['obra_social'] : '';

// Inicializar la conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'turnos');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Determinar el rango de fechas según el período seleccionado
$fecha_hoy = date('Y-m-d');
switch ($periodo) {
    case 'dia':
        $fecha_ini = $fecha_hoy;
        $fecha_fin = $fecha_hoy;
        break;
    case 'semana':
        $fecha_ini = date('Y-m-d', strtotime('monday this week'));
        $fecha_fin = date('Y-m-d', strtotime('sunday this week'));
        break;
    case 'mes':
    default:
        $fecha_ini = "$anio-$mes-01";
        $fecha_fin = date('Y-m-t', strtotime($fecha_ini));
        break;
}

// Inicializar la consulta para obtener pacientes
$sql = "SELECT * FROM turnos WHERE profesional = ?";
$params = [$profesional];
$param_types = 's';

// Agregar filtros de búsqueda por nombre y obra social
if ($busqueda_nombre != '') {
    $sql .= " AND nombre LIKE ?";
    $params[] = "%$busqueda_nombre%";
    $param_types .= 's';
}

if ($busqueda_obra_social != '') {
    $sql .= " AND obra_social LIKE ?";
    $params[] = "%$busqueda_obra_social%";
    $param_types .= 's';
}

// Filtrar por el rango de fechas según el período
$sql .= " AND fecha BETWEEN ? AND ?";
$params[] = $fecha_ini;
$params[] = $fecha_fin;
$param_types .= 'ss';

$sql .= " ORDER BY fecha, hora";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$pacientes = $stmt->get_result();

// Verificar si la consulta fue exitosa
if ($pacientes === false) {
    echo "Error al obtener los pacientes: " . $conn->error;
    exit();
}

// Obtener días deshabilitados para mostrar
$diasDeshabilitados = isset($_SESSION['diasDeshabilitados'][$profesional][$sede]) 
    ? $_SESSION['diasDeshabilitados'][$profesional][$sede] 
    : [];

// Mapear días de la semana
$dias_semana = [
    0 => 'Domingo',
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado'
];

// Crear un array para organizar los pacientes por fecha y hora
$pacientes_por_fecha_hora = [];
while ($row = $pacientes->fetch_assoc()) {
    $fecha = $row['fecha'];
    $hora = date('H:i', strtotime($row['hora']));
    
    if (!isset($pacientes_por_fecha_hora[$fecha])) {
        $pacientes_por_fecha_hora[$fecha] = [];
    }
    
    if (!isset($pacientes_por_fecha_hora[$fecha][$hora])) {
        $pacientes_por_fecha_hora[$fecha][$hora] = [];
    }
    
    $pacientes_por_fecha_hora[$fecha][$hora][] = $row;
}

// Obtener todas las horas únicas para mostrar en la tabla
$todas_horas = [];
foreach ($pacientes_por_fecha_hora as $fecha => $horas_pacientes) {
    foreach ($horas_pacientes as $hora => $pacientes) {
        if (!in_array($hora, $todas_horas)) {
            $todas_horas[] = $hora;
        }
    }
}

// Adicionar horas de disponibilidad
foreach ($disponibilidadProfesionales[$profesional][$sede] ?? [] as $dia => $horas) {
    foreach ($horas as $hora) {
        if (!in_array($hora, $todas_horas)) {
            $todas_horas[] = $hora;
        }
    }
}

sort($todas_horas);

// Nombres de los meses en español
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../bootstrap-5.1.3-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <script src="../bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/989f8affb2.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&family=Noto+Sans&family=Poppins:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" href="../img/ISO_Violeta.png">
    <title>Kaizen - Pacientes</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        
        header {
            background-color: #8A2BE2;
            color: white;
        }
        
        .navbar {
            padding: 10px 0;
        }
        
        .container-fluid {
            padding: 0 20px;
        }
        
        .color {
            height: 20px;
            background-color: #8A2BE2;
        }
        
        h1, h2, h3, h4 {
            color: #6b2870;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Estilos para el selector de período */
        .periodo-selector {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
        }
        
        .periodo-btn {
            margin: 0 5px;
            padding: 8px 20px;
            background-color: #f1f1f1;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .periodo-btn.active {
            background-color: #8A2BE2;
            color: white;
            border-color: #8A2BE2;
        }
        
        .periodo-btn:hover {
            background-color: #e0e0e0;
        }
        
        .periodo-btn.active:hover {
            background-color: #7B1FA2;
        }
        
        /* Estilos para el filtro de búsqueda */
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .search-form {
            display: flex;
            flex: 1;
            max-width: 600px;
            margin-right: 15px;
        }
        
        .search-form input {
            margin-right: 10px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;            
        }
        
        .search-btn {
            background-color: #8A2BE2;
            color: white;
            border: none;
            padding: 1px 20px;
            height: 40px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .search-btn:hover {
            background-color: #7B1FA2;
        }
        
        /* Estilos para la navegación del mes */
        .month-nav {
            display: flex;
            align-items: center;
        }
        
        .month-nav a {
            padding: 5px 10px;
            background-color: #f1f1f1;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .month-nav a:hover {
            background-color: #e0e0e0;
        }
        
        .current-month {
            font-weight: bold;
            font-size: 16px;
            color: #6b2870;
        }
        
        /* Estilos para la tabla de pacientes */
        .table-container {
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        th {
            background-color: #8A2BE2;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: 500;
        }
        
        td {
            padding: 10px;
            border: 1px solid #e0e0e0;
            text-align: center;
            vertical-align: top;
        }
        
        .hora-col {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 80px;
        }
        
        .fecha-header {
            background-color: #f1f1f1;
            color: #555;
            padding: 5px;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .patient-cell {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 6px;
            margin-bottom: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        /* Estilos para gestión de días deshabilitados */
        .management-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .management-card {
            flex: 1;
            min-width: 300px;
            background-color: white;
            border-radius: 6px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .management-card h3 {
            color: #6b2870;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        
        .form-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        /* Estilos para lista de días deshabilitados */
        .dias-lista {
            margin-top: 30px;
            background-color: white;
            border-radius: 6px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .dias-lista h3 {
            color: #6b2870;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .list-group-item {
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            margin-bottom: 5px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        
        /* Estilos responsive */
        @media (max-width: 768px) {
            .management-container {
                flex-direction: column;
            }
            
            .filter-container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .search-form {
                width: 100%;
                margin-bottom: 15px;
                margin-right: 0;
                flex-wrap: wrap;
            }
            
            .search-form input {
                margin-bottom: 10px;
                width: 100%;
            }
            
            .month-nav {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    

    <div class="content-wrapper">
        <h1 class="mt-4 mb-4">Pacientes de <?php echo $profesional; ?> - <?php echo $sede; ?></h1>
        
        <!-- Selector de período -->
        <div class="periodo-selector">
            <a href="?periodo=dia" class="periodo-btn <?php echo $periodo == 'dia' ? 'active' : ''; ?>">Hoy</a>
            <a href="?periodo=semana" class="periodo-btn <?php echo $periodo == 'semana' ? 'active' : ''; ?>">Esta Semana</a>
            <a href="?periodo=mes&mes=<?php echo $mes; ?>&anio=<?php echo $anio; ?>" class="periodo-btn <?php echo $periodo == 'mes' ? 'active' : ''; ?>">Este Mes</a>
        </div>
        
        <!-- Filtros y navegación de meses -->
        <div class="filter-container m-1">
            <form method="GET" action="pacientes.php" class="search-form">
                <input type="hidden" name="periodo" value="<?php echo $periodo; ?>">
                <input type="hidden" name="mes" value="<?php echo $mes; ?>">
                <input type="hidden" name="anio" value="<?php echo $anio; ?>">
                <input type="text" name="nombre" placeholder="Buscar por nombre" value="<?php echo $busqueda_nombre; ?>">
                <input type="text" name="obra_social" placeholder="Buscar por obra social" value="<?php echo $busqueda_obra_social; ?>">
                <button type="submit" class="search-btn">Buscar</button>
            </form>
            
            <?php if ($periodo == 'mes'): ?>
            <div class="month-nav">
                <a href="?periodo=mes&mes=<?php echo $mes_anterior; ?>&anio=<?php echo $anio_anterior; ?>">
                    &lt; <?php echo $meses[$mes_anterior]; ?>
                </a>
                <span class="current-month"><?php echo $meses[(int)$mes] . ' ' . $anio; ?></span>
                <a href="?periodo=mes&mes=<?php echo $mes_siguiente; ?>&anio=<?php echo $anio_siguiente; ?>">
                    <?php echo $meses[$mes_siguiente]; ?> &gt;
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Tabla de pacientes -->
        <div class="table-container">
            <h2>Pacientes del <?php echo date('d/m/Y', strtotime($fecha_ini)); ?> al <?php echo date('d/m/Y', strtotime($fecha_fin)); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Hora</th>
                        <?php 
                        $fechas_en_rango = [];
                        $fecha_actual = new DateTime($fecha_ini);
                        $fecha_fin_dt = new DateTime($fecha_fin);
                        
                        while ($fecha_actual <= $fecha_fin_dt) {
                            $fecha_str = $fecha_actual->format('Y-m-d');
                            $fechas_en_rango[] = $fecha_str;
                            $dia_semana_num = (int)$fecha_actual->format('w');
                            $dia_mes = $fecha_actual->format('d');
                            echo "<th>" . $dias_semana[$dia_semana_num] . " " . $dia_mes . "</th>";
                            $fecha_actual->modify('+1 day');
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todas_horas as $hora): ?>
                    <tr>
                        <td class="hora-col"><?php echo $hora; ?></td>
                        <?php foreach ($fechas_en_rango as $fecha): ?>
                        <td>
                            <?php if (isset($pacientes_por_fecha_hora[$fecha][$hora])): ?>
                                <div class="fecha-header"><?php echo date('d/m/Y', strtotime($fecha)); ?></div>
                                <?php foreach ($pacientes_por_fecha_hora[$fecha][$hora] as $paciente): ?>
                                <div class="patient-cell">
                                    <div><strong><?php echo $paciente['nombre']; ?></strong></div>
                                    <small><?php echo $paciente['obra_social']; ?></small>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Gestión de días -->
        <div class="management-container">
            <!-- Formulario para deshabilitar días -->
            <div class="management-card">
                <h3>Deshabilitar un día</h3>
                <form method="POST" action="pacientes.php">
                    <label class="form-label" for="fecha_deshabilitar">Seleccione la fecha a deshabilitar:</label>
                    <input type="date" id="fecha_deshabilitar" name="fecha_deshabilitar" class="form-input" required min="<?php echo date('Y-m-d'); ?>">
                    <button type="submit" name="deshabilitar_dia" class="btn-danger">Deshabilitar día</button>
                </form>
            </div>
            
            <!-- Formulario para habilitar días deshabilitados -->
            <div class="management-card">
                <h3>Habilitar un día</h3>
                <?php if (empty($diasDeshabilitados)): ?>
                    <p>No hay días deshabilitados para este profesional en esta sede.</p>
                <?php else: ?>
                    <form method="POST" action="pacientes.php">
                        <label class="form-label" for="fecha_habilitar">Seleccione la fecha a habilitar:</label>
                        <select id="fecha_habilitar" name="fecha_habilitar" class="form-input" required>
                            <?php foreach ($diasDeshabilitados as $fecha_deshabilitada): ?>
                                <option value="<?php echo $fecha_deshabilitada; ?>"><?php echo date('d/m/Y', strtotime($fecha_deshabilitada)); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="habilitar_dia" class="btn-success">Habilitar día</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Lista de días deshabilitados -->
        <div class="dias-lista">
            <h3>Días Deshabilitados</h3>
            <?php if (empty($diasDeshabilitados)): ?>
                <p>No hay días deshabilitados para este profesional en esta sede.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($diasDeshabilitados as $fecha_deshabilitada): ?>
                        <li class="list-group-item"><?php echo date('d/m/Y (l)', strtotime($fecha_deshabilitada)); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar flatpickr para selección de fecha
            flatpickr("#fecha_deshabilitar", {
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
                }
            });
        });
    </script>
</body>
</html>
