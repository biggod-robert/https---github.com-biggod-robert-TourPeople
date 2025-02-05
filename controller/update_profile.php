<?php
session_start();
require_once '../model/connectionDB.php';

if (!isset($_SESSION['user_tour']['id_documento'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$id_documento = $_SESSION['user_tour']['id_documento'];
$name = $_POST['name'];
$apellido = $_POST['apellido'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];

// Manejar la subida de la imagen
if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image']['name'];
    $target = "../upload/imgUsers/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
} else {
    $image = $_SESSION['user_tour']['imagen'];
}

// Establecer la conexión a la base de datos
$objeto = new Connection();
$conexion = $objeto->Conectar();

$query = "UPDATE tb_users SET nombre_p = :name, apellido_p = :apellido, correo = :email, telefono = :telefono, imagen = :image WHERE id_documento = :id_documento";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':apellido', $apellido);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':image', $image);
$stmt->bindParam(':id_documento', $id_documento);

if ($stmt->execute()) {
    $_SESSION['user_tour']['name'] = $name;
    $_SESSION['user_tour']['apellido'] = $apellido;
    $_SESSION['user_tour']['email'] = $email;
    $_SESSION['user_tour']['telefono'] = $telefono;
    $_SESSION['user_tour']['imagen'] = $image;
    echo json_encode(['success' => 'Perfil actualizado']);
} else {
    echo json_encode(['error' => 'Error al actualizar el perfil']);
}

// Cerrar la conexión a la base de datos
$conexion = null;

