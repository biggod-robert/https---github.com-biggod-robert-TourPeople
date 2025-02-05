<?php

class ReservasManager
{
    public function __construct()
    {
    }

    // Funciones para gestionar tipos de habitaciones

    public function gestionarTiposHabitaciones($conexion, $opcion, $idTipoHabitacion, $nombreTipo, $descripcionTipo, $precioPorNoche, $imgHabitacion = null)
    {
        switch ($opcion) {
            case 1: // Obtener tipos de habitaciones
                return $this->obtenerTiposHabitaciones($conexion);
                break;
            case 2: // Insertar nuevo tipo de habitación
                return $this->insertarTipoHabitacion($conexion, $nombreTipo, $descripcionTipo, $precioPorNoche, $imgHabitacion);
                break;
            case 3: // Eliminar tipo de habitación
                return $this->eliminarTipoHabitacion($conexion, $idTipoHabitacion);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }

    public function obtenerTiposHabitaciones($conexion)
    {
        $sql = "SELECT * FROM tb_tipos_habitaciones";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->execute();
        return $ejecutar->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarTipoHabitacion($conexion, $nombreTipo, $descripcionTipo, $precioPorNoche, $imgHabitacion)
    {
        try {
            // Iniciar una transacción
            $conexion->beginTransaction();

            // Insertar el tipo de habitación
            $sql = "INSERT INTO tb_tipos_habitaciones (nombre_tipo, descripcion, precio_por_noche) VALUES (:nombre_tipo, :descripcion, :precio_por_noche)";
            $ejecutar = $conexion->prepare($sql);
            $ejecutar->bindParam(':nombre_tipo', $nombreTipo, PDO::PARAM_STR);
            $ejecutar->bindParam(':descripcion', $descripcionTipo, PDO::PARAM_STR);
            $ejecutar->bindParam(':precio_por_noche', $precioPorNoche, PDO::PARAM_STR);
            $ejecutar->execute();

            // Obtener el ID del tipo de habitación insertado
            $idTipoHabitacion = $conexion->lastInsertId();

            // Insertar las imágenes de la habitación
            foreach ($imgHabitacion['tmp_name'] as $key => $tmp_name) {
                $imageFileType = strtolower(pathinfo($imgHabitacion['name'][$key], PATHINFO_EXTENSION));
                $newFileName = uniqid('img_', true) . '.' . $imageFileType;
                $target_dir = "../upload/habitaciones/";
                $target_file = $target_dir . $newFileName;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $sqlImg = "INSERT INTO tb_imagenes_habitaciones (id_tipo_habitacion, img) VALUES (:id_tipo_habitacion, :img)";
                    $ejecutarImg = $conexion->prepare($sqlImg);
                    $ejecutarImg->bindParam(':id_tipo_habitacion', $idTipoHabitacion, PDO::PARAM_INT);
                    $ejecutarImg->bindParam(':img', $newFileName, PDO::PARAM_STR);
                    $ejecutarImg->execute();
                } else {
                    // Si hay un error al subir una imagen, deshacer la transacción
                    $conexion->rollBack();
                    return array("codigo" => 0, "mensaje" => "Error al subir la imagen de la habitación.");
                }
            }

            // Confirmar la transacción
            $conexion->commit();
            return array("codigo" => 1, "mensaje" => "Tipo de habitación registrado exitosamente.");
        } catch (Exception $e) {
            // Si hay una excepción, deshacer la transacción
            $conexion->rollBack();
            return array("codigo" => 0, "mensaje" => "Error al registrar el tipo de habitación: " . $e->getMessage());
        }
    }


    public function eliminarTipoHabitacion($conexion, $idTipoHabitacion)
    {
        $sql = "DELETE FROM tb_tipos_habitaciones WHERE id_tipo_habitacion = :id_tipo_habitacion";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_tipo_habitacion', $idTipoHabitacion, PDO::PARAM_INT);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Tipo de habitación eliminado exitosamente.");
    }

    // Funciones para gestionar imágenes de habitaciones

    public function gestionarImagenesHabitaciones($conexion, $opcion, $idTipoHabitacion, $imgHabitacion, $idImagenHabitacion = null)
    {
        switch ($opcion) {
            case 4: // Obtener imágenes de habitaciones
                return $this->obtenerImagenesHabitaciones($conexion, $idTipoHabitacion);
                break;
            case 5: // Insertar nueva imagen de habitación
                return $this->insertarImagenHabitacion($conexion, $idTipoHabitacion, $imgHabitacion);
                break;
            case 6: // Eliminar imagen de habitación
                return $this->eliminarImagenHabitacion($conexion, $idImagenHabitacion);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }

    public function obtenerImagenesHabitaciones($conexion, $idTipoHabitacion)
    {
        $sql = "SELECT * FROM tb_imagenes_habitaciones WHERE id_tipo_habitacion = :id_tipo_habitacion";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_tipo_habitacion', $idTipoHabitacion, PDO::PARAM_INT);
        $ejecutar->execute();
        return $ejecutar->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarImagenHabitacion($conexion, $idTipoHabitacion, $imgHabitacion)
    {
        $imageFileType = strtolower(pathinfo($imgHabitacion['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid('img_', true) . '.' . $imageFileType;
        $target_dir = "../upload/habitaciones/";
        $target_file = $target_dir . $newFileName;

        if (move_uploaded_file($imgHabitacion['tmp_name'], $target_file)) {
            $sql = "INSERT INTO tb_imagenes_habitaciones (id_tipo_habitacion, img) VALUES (:id_tipo_habitacion, :img)";
            $ejecutar = $conexion->prepare($sql);
            $ejecutar->bindParam(':id_tipo_habitacion', $idTipoHabitacion, PDO::PARAM_INT);
            $ejecutar->bindParam(':img', $newFileName, PDO::PARAM_STR);
            $ejecutar->execute();
            return array("codigo" => 1, "mensaje" => "Imagen de habitación registrada exitosamente.");
        } else {
            return array("codigo" => 0, "mensaje" => "Error al subir la imagen de la habitación.");
        }
    }

    public function eliminarImagenHabitacion($conexion, $idImagenHabitacion)
    {
        // Obtener la ruta de la imagen desde la base de datos
        $sqlImg = "SELECT img FROM tb_imagenes_habitaciones WHERE id_img_habitacion = :id_img_habitacion";
        $ejecutarImg = $conexion->prepare($sqlImg);
        $ejecutarImg->bindParam(':id_img_habitacion', $idImagenHabitacion, PDO::PARAM_INT);
        $ejecutarImg->execute();
        $imagen = $ejecutarImg->fetch(PDO::FETCH_ASSOC);

        if ($imagen) {
            // Ruta de la imagen en el sistema de archivos
            $rutaImagen = "../upload/habitaciones/" . $imagen['img'];

            // Eliminar la imagen de la base de datos
            $sqlDelete = "DELETE FROM tb_imagenes_habitaciones WHERE id_img_habitacion = :id_img_habitacion";
            $ejecutarDelete = $conexion->prepare($sqlDelete);
            $ejecutarDelete->bindParam(':id_img_habitacion', $idImagenHabitacion, PDO::PARAM_INT);
            $ejecutarDelete->execute();

            // Verificar si la imagen fue eliminada de la base de datos
            if ($ejecutarDelete->rowCount() > 0) {
                // Intentar eliminar el archivo físico
                if (file_exists($rutaImagen)) {
                    if (unlink($rutaImagen)) {
                        // Si se eliminó correctamente
                        return array("codigo" => 1, "mensaje" => "Imagen eliminada correctamente.");
                    } else {
                        // Error al eliminar el archivo físico
                        return array("codigo" => 0, "mensaje" => "Error al eliminar el archivo de imagen.");
                    }
                } else {
                    // Archivo no existe en el sistema de archivos
                    return array("codigo" => 0, "mensaje" => "El archivo de imagen no existe.");
                }
            } else {
                // Error al eliminar el registro en la base de datos
                return array("codigo" => 0, "mensaje" => "Error al eliminar la imagen de la base de datos.");
            }
        } else {
            // No se encontró la imagen en la base de datos
            return array("codigo" => 0, "mensaje" => "Imagen no encontrada.");
        }
    }

    // Funciones para gestionar reservas
    public function gestionarReservas($conexion, $opcion, $idHotel, $idReserva, $nombreCliente, $fechaEntrada, $fechaSalida, $idReservaDelete)
    {
        switch ($opcion) {
            case 7: // Obtener reservas por hotel
                return $this->obtenerReservas($conexion, $idHotel);
                break;
            case 8: // Insertar nueva reserva
                return $this->insertarReserva($conexion, $idHotel, $nombreCliente, $fechaEntrada, $fechaSalida);
                break;
            case 9: // Actualizar reserva existente
                return $this->actualizarReserva($conexion, $idReserva, $nombreCliente, $fechaEntrada, $fechaSalida);
                break;
            case 10: // Eliminar reserva
                return $this->eliminarReserva($conexion, $idReservaDelete);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }

    public function obtenerReservas($conexion, $idHotel)
    {
        $sql = "SELECT * FROM tb_reservas WHERE id_hotel = :id_hotel";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_hotel', $idHotel, PDO::PARAM_INT);
        $ejecutar->execute();
        return $ejecutar->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarReserva($conexion, $idHotel, $nombreCliente, $fechaEntrada, $fechaSalida)
    {
        $sql = "INSERT INTO tb_reservas (id_hotel, nombre_cliente, fecha_entrada, fecha_salida) VALUES (:id_hotel, :nombre_cliente, :fecha_entrada, :fecha_salida)";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_hotel', $idHotel, PDO::PARAM_INT);
        $ejecutar->bindParam(':nombre_cliente', $nombreCliente, PDO::PARAM_STR);
        $ejecutar->bindParam(':fecha_entrada', $fechaEntrada, PDO::PARAM_STR);
        $ejecutar->bindParam(':fecha_salida', $fechaSalida, PDO::PARAM_STR);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Reserva registrada exitosamente.");
    }

    public function actualizarReserva($conexion, $idReserva, $nombreCliente, $fechaEntrada, $fechaSalida)
    {
        $sql = "UPDATE tb_reservas SET nombre_cliente = :nombre_cliente, fecha_entrada = :fecha_entrada, fecha_salida = :fecha_salida WHERE id_reserva = :id_reserva";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':nombre_cliente', $nombreCliente, PDO::PARAM_STR);
        $ejecutar->bindParam(':fecha_entrada', $fechaEntrada, PDO::PARAM_STR);
        $ejecutar->bindParam(':fecha_salida', $fechaSalida, PDO::PARAM_STR);
        $ejecutar->bindParam(':id_reserva', $idReserva, PDO::PARAM_INT);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Reserva actualizada exitosamente.");
    }

    public function eliminarReserva($conexion, $idReservaDelete)
    {
        $sql = "DELETE FROM tb_reservas WHERE id_reserva = :id_reserva";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_reserva', $idReservaDelete, PDO::PARAM_INT);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Reserva eliminada exitosamente.");
    }

    // Funciones para gestionar comentarios

    public function gestionarComentarios($conexion, $opcion, $idComentario, $idHotel, $idUsuario, $comentario)
    {
        switch ($opcion) {
            case 11: // Obtener comentarios por hotel
                return $this->obtenerComentarios($conexion, $idHotel);
                break;
            case 12: // Insertar nuevo comentario
                return $this->insertarComentario($conexion, $idHotel, $idUsuario, $comentario);
                break;
            case 13: // Editar comentario
                return $this->editarComentario($conexion, $idComentario, $comentario);
                break;
            case 14: // Eliminar comentario
                return $this->eliminarComentario($conexion, $idComentario);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }

    public function obtenerComentarios($conexion, $idHotel)
    {
        $sql = "SELECT * FROM tb_comentarios WHERE id_hotel = :id_hotel ORDER BY fecha DESC";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_hotel', $idHotel, PDO::PARAM_INT);
        $ejecutar->execute();
        return $ejecutar->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarComentario($conexion, $idHotel, $idUsuario, $comentario)
    {
        $sql = "INSERT INTO tb_comentarios (id_hotel, id_usuario, comentario) VALUES (:id_hotel, :id_usuario, :comentario)";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_hotel', $idHotel, PDO::PARAM_INT);
        $ejecutar->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $ejecutar->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Comentario registrado exitosamente.");
    }
    public function editarComentario($conexion, $idComentario, $comentario)
    {
        $sql = "UPDATE tb_comentarios SET comentario = :comentario WHERE id_comentario = :id_comentario";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $ejecutar->bindParam(':id_comentario', $idComentario, PDO::PARAM_INT);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Comentario actualizado exitosamente.");
    }

    public function eliminarComentario($conexion, $idComentario)
    {
        $sql = "DELETE FROM tb_comentarios WHERE id_comentario = :id_comentario";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_comentario', $idComentario, PDO::PARAM_INT);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Comentario eliminado exitosamente.");
    }
    // Funciones para gestionar facturas

    public function gestionarFacturas($conexion, $opcion, $idFactura, $idReserva, $datosFactura)
    {
        switch ($opcion) {
            case 15: // Obtener facturas
                return $this->obtenerFacturas($conexion);
                break;
            case 16: // Insertar nueva factura
                return $this->insertarFactura($conexion, $idReserva, $datosFactura);
                break;
            case 17: // Editar factura
                return $this->editarFactura($conexion, $idFactura, $datosFactura);
                break;
            case 18: // Eliminar factura
                return $this->eliminarFactura($conexion, $idFactura);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }

    public function obtenerFacturas($conexion)
    {
        $sql = "SELECT * FROM tb_facturas";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->execute();
        return $ejecutar->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarFactura($conexion, $idReserva, $datosFactura)
    {
        $sql = "INSERT INTO tb_facturas (id_reserva, datos_factura) VALUES (:id_reserva, :datos_factura)";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_reserva', $idReserva, PDO::PARAM_INT);
        $ejecutar->bindParam(':datos_factura', $datosFactura, PDO::PARAM_STR);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Factura registrada exitosamente.");
    }

    public function editarFactura($conexion, $idFactura, $datosFactura)
    {
        $sql = "UPDATE tb_facturas SET datos_factura = :datos_factura WHERE id_factura = :id_factura";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':datos_factura', $datosFactura, PDO::PARAM_STR);
        $ejecutar->bindParam(':id_factura', $idFactura, PDO::PARAM_INT);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Factura actualizada exitosamente.");
    }

    public function eliminarFactura($conexion, $idFactura)
    {
        $sql = "DELETE FROM tb_facturas WHERE id_factura = :id_factura";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_factura', $idFactura, PDO::PARAM_INT);
        $ejecutar->execute();
        return array("codigo" => 1, "mensaje" => "Factura eliminada exitosamente.");
    }

    // Funciones para gestionar ventas

    public function gestionarVentas($conexion, $opcion, $filtro)
    {
        switch ($opcion) {
            case 19: // Obtener ventas totales
                return $this->obtenerVentasTotales($conexion, $filtro);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }

    public function obtenerVentasTotales($conexion, $filtro)
    {
        $sql = "SELECT SUM(monto) as total FROM tb_ventas WHERE DATE_FORMAT(fecha, :filtro) = DATE_FORMAT(NOW(), :filtro)";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':filtro', $filtro, PDO::PARAM_STR);
        $ejecutar->execute();
        return $ejecutar->fetch(PDO::FETCH_ASSOC);
    }

    // Funciones para gestionar estadísticas

    public function gestionarEstadisticas($conexion, $opcion)
    {
        switch ($opcion) {
            case 20: // Obtener estadísticas de clics
                return $this->obtenerEstadisticas($conexion);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }

    public function obtenerEstadisticas($conexion)
    {
        $sql = "SELECT tipo, COUNT(*) as total FROM tb_estadisticas GROUP BY tipo";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->execute();
        return $ejecutar->fetchAll(PDO::FETCH_ASSOC);
    }
}
