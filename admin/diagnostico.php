<?php
session_start([
    'cookie_lifetime' => 0, // La sesión se cierra cuando se cierra el navegador
]);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Verificar si se ha proporcionado un ID de paciente
if (!isset($_GET['id'])) {
    echo "ID de paciente no proporcionado.";
    exit();
}

$id = $_GET['id'];

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'turnos');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del paciente
$sql = "SELECT * FROM turnos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Paciente no encontrado.";
    exit();
}

$paciente = $result->fetch_assoc();

// Manejar el envío del formulario de diagnóstico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_diagnostico'])) {
    $diagnostico = $_POST['diagnostico'];

    $sql_update = "UPDATE turnos SET diagnostico = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param('si', $diagnostico, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Diagnóstico guardado correctamente.'); window.location.href = 'diagnostico.php?id=$id';</script>";
    } else {
        echo "Error al guardar el diagnóstico: " . $conn->error;
    }
}

// Manejar el cambio de estado de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asistencia_id'])) {
    $asistio = isset($_POST['asistio']) ? 1 : 0;
    $asistencia_id = $_POST['asistencia_id'];

    $sql_update = "UPDATE turnos SET asistio = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param('ii', $asistio, $asistencia_id);

    if ($stmt->execute()) {
        echo "<script>alert('Estado de asistencia actualizado correctamente.'); window.location.href = 'diagnostico.php?id=$id';</script>";
    } else {
        echo "Error al actualizar el estado de asistencia: " . $conn->error;
    }
}

// Eliminar paciente si se recibe el ID
if (isset($_GET['eliminar_id'])) {
    $eliminar_id = $_GET['eliminar_id'];
    $sql_delete = "DELETE FROM turnos WHERE id = ?";

    if ($stmt = $conn->prepare($sql_delete)) {
        $stmt->bind_param('i', $eliminar_id);
        if ($stmt->execute()) {
            echo "<script>alert('Paciente eliminado correctamente.'); window.location.href = 'pacientes.php';</script>";
        } else {
            echo "Error al eliminar el paciente.";
        }
    } else {
        echo "Error al preparar la consulta de eliminación.";
    }
    exit();
}

// Actualizar paciente si se recibe el ID, nueva obra social, nueva fecha, nueva hora y nuevo número de sesión
if (isset($_POST['editar_id'])) {
    $editar_id = $_POST['editar_id'];
    $nueva_obra_social = $_POST['nueva_obra_social'];
    $nueva_fecha = $_POST['nueva_fecha'];
    $nueva_hora = $_POST['nueva_hora'];
    $nuevo_numero_sesion = $_POST['nuevo_numero_sesion'];
    $sql_update = "UPDATE turnos SET obra_social = ?, fecha = ?, hora = ?, numero_sesion = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql_update)) {
        $stmt->bind_param('sssii', $nueva_obra_social, $nueva_fecha, $nueva_hora, $nuevo_numero_sesion, $editar_id);
        if ($stmt->execute()) {
            echo "<script>alert('Datos actualizados correctamente.'); window.location.href = 'diagnostico.php?id=$id';</script>";
        } else {
            echo "Error al actualizar los datos.";
        }
    } else {
        echo "Error al preparar la consulta de actualización.";
    }
    exit();
}

// Actualizar nota del paciente
if (isset($_POST['nota_id']) && isset($_POST['nuevo_comentario'])) {
    $nota_id = $_POST['nota_id'];
    $nuevo_comentario = $_POST['nuevo_comentario'];

    $sql_update = "UPDATE turnos SET comentarios = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql_update)) {
        $stmt->bind_param('si', $nuevo_comentario, $nota_id);
        if ($stmt->execute()) {
            echo "<script>alert('Comentario actualizado correctamente.'); window.location.href = 'diagnostico.php?id=$id';</script>";
        } else {
            echo "Error al actualizar el comentario.";
        }
    } else {
        echo "Error al preparar la consulta.";
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../bootstrap-5.1.3-dist/css/bootstrap.css">
    <script src="../bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&family=Noto+Sans&family=Poppins:wght@300&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/989f8affb2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
    <link rel="icon" href="../img/ISO_Violeta.png">
    <title>Diagnóstico del Paciente</title>
    <style>
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

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #333;
            line-height: 1.6;
            box-sizing: border-box;
        }

        h1,
        h2 {
            color: #000;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #000;
        }

        button {
            cursor: pointer;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .content {
            padding: 20px;
            margin: auto;
            width: 90%;
            max-width: 1200px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        form input,
        form select {
            flex: 1;
            min-width: 150px;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            background-color: #000;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #000;
            color: #000;
        }

        td {
            background-color: #f9f9f9;
        }

        .delete-btn {
            background-color: #e63946;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 5px;
        }

        .btn_diagnostico {
            background-color: rgb(21, 85, 168);
            color: #fff;
            border: none;
            padding: 8px 11px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            position: relative;
            margin-top: 2px;
        }

        .delete-btn:hover {
            color: #fff !important;
            text-decoration: none;
            background-color: #b71c1c;
        }

        .edit-btn {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 5px 2px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            width: 75px;
            transition: background-color 0.3s ease;
            margin-top: 6px;
        }

        .edit-btn:hover {
            background-color: rgb(94, 117, 92);
        }

        .nota-btn {
            background-color: rgb(47, 175, 122);
            color: #fff;
            border: none;
            padding: 5px 2px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            width: 75px;
            transition: background-color 0.3s ease;
            margin-top: 2px;
        }

        .nota-btn:hover {
            background-color: rgb(80, 133, 76);
        }

        #formularioEdicion {
            display: none;
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -20%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            max-width: 400px;
            width: 90%;
        }

        #formularioEdicion form input {
            width: calc(100% - 20px);
            margin-bottom: 10px;
        }

        #formularioEdicion button {
            width: 48%;
            margin: 5px 1%;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
            }

            form {
                flex-direction: column;
            }

            form input,
            form select,
            form button {
                width: 100%;
            }

            table {
                font-size: 14px;
                overflow-x: auto;
                display: block;
                max-width: 100%;
                white-space: nowrap;
            }

            table th,
            table td {
                white-space: nowrap;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="nav_container navbar navbar-dark">
            <div class="container-fluid">
                <div class="logo_container">
                    <img class="logo" src="../img/ISO_Violeta.png" alt="Logo">
                </div>
                <div class="" id="navbarNavAltMarkup">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav_link nav-link" href="../index.php">Agendar Paciente</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="color"></div>

    <div class="content">
        <h1>Diagnóstico del Paciente</h1>
        <hr>
        <table class="table">
            <tr>
                <th>Nombre</th>
                <td><?php echo $paciente['nombre']; ?></td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td><?php echo $paciente['telefono']; ?></td>
            </tr>
            <tr>
                <th>Correo</th>
                <td><?php echo $paciente['gmail']; ?></td>
            </tr>
            <tr>
                <th>Sede</th>
                <td><?php echo $paciente['sede']; ?></td>
            </tr>
            <tr>
                <th>Obra Social</th>
                <td><?php echo $paciente['obra_social']; ?></td>
            </tr>
            <tr>
                <th>Servicio</th>
                <td><?php echo $paciente['servicio']; ?></td>
            </tr>
            <tr>
                <th>Profesional</th>
                <td><?php echo $paciente['profesional']; ?></td>
            </tr>
            <tr>
                <th>Fecha</th>
                <td><?php echo $paciente['fecha']; ?></td>
            </tr>
            <tr>
                <th>Hora</th>
                <td><?php echo $paciente['hora']; ?></td>
            </tr>
            <tr>
                <th>Comentarios</th>
                <td><?php echo $paciente['comentarios']; ?></td>
            </tr>
            <tr>
                <th>N° Sesion</th>
                <td><?php echo $paciente['numero_sesion']; ?></td>
            </tr>
            <tr>
                <th>Asistencia</th>
                <td>
                    <?php echo $paciente['asistio'] ? 'El paciente asistió a su sesión' : 'El paciente no asistió a su sesión'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Acciones</th>
                    <td>
                    <form method="POST" class="asistencia_contianer" style="width: 0px; position: relative; right: 37px; top: 5px; cursor: pointer;" action="diagnostico.php?id=<?php echo $id; ?>">
                        <input type="hidden" name="asistencia_id" value="<?php echo $paciente['id']; ?>">
                        <input type="checkbox" name="asistio" <?php echo $paciente['asistio'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                    </form>
                    <a href="?eliminar_id=<?php echo $paciente['id']; ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que quieres eliminar este paciente?');">Eliminar</a>
                    <br>
                    <button class="edit-btn" onclick="abrirFormulario(<?php echo $paciente['id']; ?>, '<?php echo $paciente['obra_social']; ?>', '<?php echo $paciente['fecha']; ?>', '<?php echo $paciente['hora']; ?>', '<?php echo $paciente['numero_sesion']; ?>')">Editar</button>
                    <br>
                    <button class="nota-btn" onclick="notaFormulario('<?php echo $paciente['id']; ?>', '<?php echo addslashes(str_replace(array("\r\n", "\n", "\r"), '', $paciente['comentarios'])); ?>')">Editar nota</button>
                </td>
            </tr>
        </table>

        <h2>Diagnóstico</h2>
        <form method="POST" action="diagnostico.php?id=<?php echo $id; ?>">
            <textarea name="diagnostico" rows="10" style="width:100%;"><?php echo $paciente['diagnostico']; ?></textarea>
            <br><br>
            <button type="submit" name="guardar_diagnostico">Guardar Diagnóstico</button>
        </form>
        <a href="pacientes.php"><button type="submit" name=""> < Volver </button></a>

    </div>

    <div id="formularioEdicion" style="display:none; position:fixed; top:20%; left:50%; transform:translate(-50%, -20%); background-color:white; padding:20px; border-radius:10px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
        <h2>Editar Paciente</h2>
        <form method="POST" action="diagnostico.php?id=<?php echo $id; ?>">
            <input type="hidden" name="editar_id" id="editar_id">
            <label for="nueva_obra_social">Nueva Obra Social:</label>
            <input type="text" name="nueva_obra_social" id="nueva_obra_social">
            <br>
            <label for="nueva_fecha">Nueva Fecha:</label>
            <input type="date" name="nueva_fecha" id="nueva_fecha">
            <br>
            <label for="nueva_hora">Nueva Hora:</label>
            <input type="time" name="nueva_hora" id="nueva_hora">
            <br>
            <label for="nuevo_numero_sesion">Nuevo N° Sesion:</label>
            <input type="number" name="nuevo_numero_sesion" id="nuevo_numero_sesion">
            <br><br>
            <button type="submit">Guardar</button>
            <button type="button" onclick="cerrarFormulario()">Cancelar</button>
        </form>
    </div>

    <div id="formularioNotas" style="display:none; position:fixed; top:20%; left:50%; transform:translate(-50%, -20%); background-color:white; padding:20px; border-radius:10px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
        <h2>Editar Nota</h2>
        <form method="POST" action="diagnostico.php?id=<?php echo $id; ?>">
            <input type="hidden" name="nota_id" id="nota_id">
            <label for="nuevo_comentario">Comentario:</label>
            <textarea name="nuevo_comentario" id="nuevo_comentario" rows="4" style="width:100%; resize: none !important;"></textarea>
            <br><br>
            <button type="submit">Guardar</button>
            <button type="button" onclick="cerrarNotaFormulario()">Cancelar</button>
        </form>
    </div>

    <script>
        function abrirFormulario(id, obraSocial, fecha, hora, numeroSesion) {
            document.getElementById('editar_id').value = id;
            document.getElementById('nueva_obra_social').value = obraSocial;
            document.getElementById('nueva_fecha').value = fecha;
            document.getElementById('nueva_hora').value = hora;
            document.getElementById('nuevo_numero_sesion').value = numeroSesion;
            document.getElementById('formularioEdicion').style.display = 'block';
        }

        function cerrarFormulario() {
            document.getElementById('formularioEdicion').style.display = 'none';
        }

        function notaFormulario(id, comentarioActual) {
            document.getElementById('nota_id').value = id;
            document.getElementById('nuevo_comentario').value = comentarioActual;
            document.getElementById('formularioNotas').style.display = 'block';
        }

        function cerrarNotaFormulario() {
            document.getElementById('formularioNotas').style.display = 'none';
        }
    </script>
</body>

</html>

<?php $conn->close(); ?>