<?php
// Iniciar la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
include("../model/ReservasManager.php");
include_once("../model/connectionDB.php");

// Crear conexión a la base de datos
$objeto = new Connection();
$conexion = $objeto->Conectar();

// Crear instancia de ReservasManager
$reservasManager = new ReservasManager();

// Función de sanitización
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Validar y sanitizar entradas
$opcion = isset($_POST['opcion']) ? sanitize_input($_POST['opcion']) : "";
$idTipoHabitacion = isset($_POST['idTipoHabitacion']) ? (int)$_POST['idTipoHabitacion'] : 0;
$nombreTipo = isset($_POST['nombreTipo']) ? sanitize_input($_POST['nombreTipo']) : "";
$descripcionTipo = isset($_POST['descripcionTipo']) ? sanitize_input($_POST['descripcionTipo']) : "";
$precioPorNoche = isset($_POST['precioPorNoche']) ? (float)$_POST['precioPorNoche'] : 0.0;
$imgHabitacion = isset($_FILES['imgHabitacion']) ? $_FILES['imgHabitacion'] : "";
$idImagenHabitacion = isset($_POST['idImagenHabitacion']) ? (int)$_POST['idImagenHabitacion'] : 0;

$idReserva = isset($_POST['idReserva']) ? (int)$_POST['idReserva'] : 0;
$idHabitacion = isset($_POST['idHabitacion']) ? (int)$_POST['idHabitacion'] : 0;
$nombreCliente = isset($_POST['nombreCliente']) ? sanitize_input($_POST['nombreCliente']) : "";
$fechaEntrada = isset($_POST['fechaEntrada']) ? sanitize_input($_POST['fechaEntrada']) : "";
$fechaSalida = isset($_POST['fechaSalida']) ? sanitize_input($_POST['fechaSalida']) : "";
$idReservaDelete = isset($_POST['idReservaDelete']) ? (int)$_POST['idReservaDelete'] : 0;

$idComentario = isset($_POST['idComentario']) ? (int)$_POST['idComentario'] : 0;
$idHotel = isset($_POST['idHotel']) ? (int)$_POST['idHotel'] : 0;
$idUsuario = isset($_POST['idUsuario']) ? (int)$_POST['idUsuario'] : 0;
$comentario = isset($_POST['comentario']) ? sanitize_input($_POST['comentario']) : "";

$idFactura = isset($_POST['idFactura']) ? (int)$_POST['idFactura'] : 0;
$datosFactura = isset($_POST['datosFactura']) ? sanitize_input($_POST['datosFactura']) : "";

$filtro = isset($_POST['filtro']) ? sanitize_input($_POST['filtro']) : "";

// Llamar a la función correspondiente según la opción
switch ($opcion) {
    case 1:
    case 2:
    case 3:
        $data = $reservasManager->gestionarTiposHabitaciones($conexion, $opcion, $idTipoHabitacion, $nombreTipo, $descripcionTipo, $precioPorNoche);
        break;
    case 4:
    case 5:
    case 6:
        $data = $reservasManager->gestionarImagenesHabitaciones($conexion, $opcion, $idTipoHabitacion, $imgHabitacion, $idImagenHabitacion);
        break;
    case 7:
    case 8:
    case 9:
    case 10:
        $data = $reservasManager->gestionarReservas($conexion, $opcion, $idReserva, $idHabitacion, $nombreCliente, $fechaEntrada, $fechaSalida, $idReservaDelete);
        break;
    case 11:
    case 12:
    case 13:
    case 14:
        $data = $reservasManager->gestionarComentarios($conexion, $opcion, $idComentario, $idHotel, $idUsuario, $comentario);
        break;
    case 15:
    case 16:
    case 17:
    case 18:
        $data = $reservasManager->gestionarFacturas($conexion, $opcion, $idFactura, $idReserva, $datosFactura);
        break;
    case 19:
        $data = $reservasManager->gestionarVentas($conexion, $opcion, $filtro);
        break;
    case 20:
        $data = $reservasManager->gestionarEstadisticas($conexion, $opcion);
        break;
    default:
        $data = ['codigo' => 0, 'mensaje' => 'Opción no válida'];
        break;
}

// Devolver datos en formato JSON
echo json_encode($data, JSON_UNESCAPED_UNICODE);

// Cerrar la conexión a la base de datos
$conexion = null;
// Nota: En PDO, cerrar la conexión se realiza configurando el objeto de conexión a null. 
// No es necesario llamar a un método específico como en MySQLi, donde se usa $conn->close().


