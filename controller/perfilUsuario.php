<?php
require_once '../model/connectionDB.php';

$objeto = new Connection();
$conexion = $objeto->Conectar();

// Iniciar la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener el ID del usuario desde la sesión
$id_documento = isset($_SESSION['user_tour']['id_documento']) ? (int)$_SESSION['user_tour']['id_documento'] : 0;

// Verificar que el ID del usuario es válido
if ($id_documento > 0) {
    // Consulta para obtener los datos del perfil del usuario
    $sql = "SELECT nombre_p AS nombre, apellido_p AS apellido, correo, telefono, imagen FROM tb_users WHERE id_documento = :id_documento";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_documento', $id_documento, PDO::PARAM_INT);
    $stmt->execute();
    $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retornar los datos del perfil en formato JSON
    echo json_encode($perfil);
} else {
    echo json_encode(['error' => 'Usuario no autenticado']);
}

// Cerrar la conexión a la base de datos
$conexion = null;
