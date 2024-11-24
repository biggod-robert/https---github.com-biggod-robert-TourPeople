<?php

class HotelesManager
{
    /**
     * Constructor de la clase.
     */
    public function __construct() {}
    /**
     * Realiza operaciones CRUD para la administración de hoteles.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $opcion Define la operación a realizar:
     *                    1: Obtener hoteles por usuario.
     *                    2: Insertar nuevo hotel.
     *                    3: Obtener hotel por ID.
     *                    4: Eliminar imagen asociada a un hotel.
     *                    5: Actualizar datos de un hotel existente.
     *                    6: Eliminar un hotel y sus imágenes.
     * @param int $id_documento ID del documento que referencia al usuario.
     * @param string $nombrehotel Nombre del hotel.
     * @param string $descripcion Descripción del hotel.
     * @param array $imgPortada Imagen de portada del hotel.
     * @param string $direccion Dirección del hotel.
     * @param string $enlace_reservas URL para reservas del hotel.
     * @param array $imghotel Imágenes adicionales del hotel.
     * @param int $idhotel ID del hotel a recuperar o editar.
     * @param int $idImagen ID de la imagen a eliminar.
     * @param int $id_hotelEdit ID del hotel a actualizar.
     * @param int $idhotelDelete ID del hotel a eliminar.
     * 
     * @return array Resultado de la operación con códigos y mensajes.
     */
    public function gestionarHoteles($conexion, $opcion, $id_documento, $nombreHotel, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgHotel, $idHotel, $idImagen, $id_hotelEdit, $idHotelDelete)
    {
        switch ($opcion) {
            case 1: // obtiene los datos para mostrar en la tabla
                return $this->obtener($conexion, $id_documento);
                break;
            case 2: // Guardar nuevo hotel
                return $this->guardarHotel($conexion, $id_documento, $nombreHotel, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgHotel);
                break;
            case 3: // optener un hotel pos su id
                return $this->optenerHotel($conexion, $idHotel);
                break;
            case 4: // eliminar imagen por id
                return $this->eliminarImg($conexion, $idImagen);
                break;
            case 5: // editar un hotel
                return $this->editarHotel($conexion, $nombreHotel, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgHotel, $id_hotelEdit);
                break;
            case 6: // eliminar un hotel
                return $this->eliminarHotel($conexion, $idHotelDelete);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }
    /**
     * Obtiene una lista de hoteles asociados a un usuario específico.
     *
     * Este método recupera todos los registros de hoteles vinculados al ID de documento proporcionado.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $id_documento ID del documento que referencia al usuario.
     *
     * @return array Devuelve un arreglo asociativo con los datos de los hoteles encontrados:
     *               - Cada elemento contiene las columnas de la tabla `tb_hoteles`.
     *               - Si no se encuentran registros, devuelve un arreglo vacío.
     *
     * @throws PDOException Lanza una excepción si ocurre un error durante la ejecución de la consulta.
     */

    public function obtener($conexion, $id_documento)
    {
        $sql = "SELECT * FROM tb_hoteles WHERE id_documento = :id_documento";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_documento', $id_documento, PDO::PARAM_INT);
        $ejecutar->execute();
        return $ejecutar->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Guarda un nuevo hotel en la base de datos, incluyendo una imagen de portada y múltiples imágenes adicionales.
     *
     * Este método procesa las imágenes subidas, las almacena en las carpetas correspondientes, y guarda los detalles del hotel en la base de datos.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $id_documento ID del documento que referencia al usuario.
     * @param string $nombreHotel Nombre del hotel.
     * @param string $descripcion Descripción del hotel.
     * @param array $imgPortada Datos del archivo de imagen de portada subido (de $_FILES).
     * @param string $direccion Dirección del hotel.
     * @param string $enlace_reservas URL para reservas del hotel.
     * @param array $imgHotel Datos de los archivos de imágenes adicionales subidos (de $_FILES).
     *
     * @return array Resultado de la operación, que incluye:
     *               - "codigo" => 1 si se guarda exitosamente, 0 en caso de error.
     *               - "mensaje" => Mensaje descriptivo del resultado.
     *
     * @throws PDOException Lanza una excepción si ocurre un error durante las operaciones de la base de datos.
     */

    public function guardarHotel($conexion, $id_documento, $nombreHotel, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgHotel)
    {
        // Procesar primero la imagen de portada
        $imageFileType = strtolower(pathinfo($imgPortada['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid('img_', true) . '.' . $imageFileType;
        $target_dir_portada = "../upload/hoteles/portadas/";
        $target_file_portada = $target_dir_portada . $newFileName;

        if (move_uploaded_file($imgPortada['tmp_name'], $target_file_portada)) {
            // Insertar sitio
            $sql = "INSERT INTO `tb_hoteles` (`id_hotel`, `nombre`, `descripcion_hotel`, `ubicacion_hotel`, `enlace_reservas`, `foto`, `id_documento`) 
                VALUES (NULL, :nombre, :descripcion, :direccion, :enlace_reservas, :imgPortada, :id_documento);";
            $ejecutar = $conexion->prepare($sql);
            $ejecutar->bindParam(':nombre', $nombreHotel, PDO::PARAM_STR);
            $ejecutar->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $ejecutar->bindParam(':imgPortada', $newFileName, PDO::PARAM_STR);
            $ejecutar->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $ejecutar->bindParam(':enlace_reservas', $enlace_reservas, PDO::PARAM_STR);
            $ejecutar->bindParam(':id_documento', $id_documento, PDO::PARAM_INT);
            $ejecutar->execute();


            // Obtener el ID de la última sitio insertado
            $id_hotel = $conexion->lastInsertId();

            // Procesar imágenes adicionales
            if (!empty($imgHotel) && isset($imgHotel['name'])) {
                $filesCount = count($imgHotel['name']);
                $target_dir_img = "../upload/hoteles/images/";

                for ($i = 0; $i < $filesCount; $i++) {
                    if ($imgHotel['error'][$i] == UPLOAD_ERR_OK) {
                        // Generar un nombre único para cada imagen adicional
                        $imgName = $imgHotel['name'][$i];
                        $imageFileType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
                        $newFileName = uniqid('img_', true) . '.' . $imageFileType;
                        $target_file_img = $target_dir_img . $newFileName;

                        // Mover el archivo a la carpeta de destino para imágenes adicionales
                        if (move_uploaded_file($imgHotel['tmp_name'][$i], $target_file_img)) {
                            // Insertar el nombre de la imagen en la tabla tb_imghoteles
                            $sql = "INSERT INTO tb_imghoteles (img, id_hotel) VALUES (:img, :id_hotel)";
                            $ejecutar = $conexion->prepare($sql);
                            $ejecutar->bindParam(':img', $newFileName, PDO::PARAM_STR);
                            $ejecutar->bindParam(':id_hotel', $id_hotel, PDO::PARAM_INT);
                            $ejecutar->execute();
                        } else {
                            error_log("Error al mover la imagen adicional: " . print_r(error_get_last(), true));
                        }
                    } else {
                        error_log("Error en la subida de imagen adicional: " . $imgHotel['error'][$i]);
                    }
                }
            }

            $salida = array("codigo" => 1, "mensaje" => "Se agrego el sitio correctamente.");
        } else {
            $salida = array("codigo" => 0, "mensaje" => "Error al subir la imagen de portada. Error: " . print_r(error_get_last(), true));
        }
        return $salida;
    }
    /**
     * Obtiene los detalles de un hotel por su ID, incluyendo las imágenes asociadas.
     *
     * Este método recupera la información principal del hotel y, si existe, las imágenes relacionadas en un formato estructurado.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $idHotel ID del hotel a consultar.
     *
     * @return array Resultado de la operación, que incluye:
     *               - "codigo" => 1 si el hotel es encontrado, 0 si no existe.
     *               - "data" => Información del hotel y sus imágenes, o un mensaje de error si no se encuentra.
     *
     * @throws PDOException Lanza una excepción si ocurre un error durante las operaciones de la base de datos.
     */

    public function optenerHotel($conexion, $idHotel)
    {
        // Obtener un hotel por su ID
        $sqlhotel = "SELECT * FROM tb_hoteles WHERE id_hotel = :idHotel";
        $ejecutarhotel = $conexion->prepare($sqlhotel);
        $ejecutarhotel->bindParam(':idHotel', $idHotel, PDO::PARAM_INT);
        $ejecutarhotel->execute();
        $hotel = $ejecutarhotel->fetch(PDO::FETCH_ASSOC); // Solo una hotel, no fetchAll()

        if ($hotel) {
            // Si la hotel existe, obtener las imágenes asociadas
            $sqlImagenes = "SELECT id_img, img FROM tb_imghoteles WHERE id_hotel = :idHotel";
            $ejecutarImagenes = $conexion->prepare($sqlImagenes);
            $ejecutarImagenes->bindParam(':idHotel', $idHotel, PDO::PARAM_INT);
            $ejecutarImagenes->execute();
            $imagenes = $ejecutarImagenes->fetchAll(PDO::FETCH_ASSOC); // Traemos todas las imágenes

            // Organizar los resultados en un array
            $hotelConImagenes = array(
                "id_hotel" => $hotel['id_hotel'],
                "nombre" => $hotel['nombre'],
                "descripcion" => $hotel['descripcion_hotel'],
                "imgPortada" => $hotel['foto'],
                "ubi_hotel" => $hotel['ubicacion_hotel'],
                "enlace_reservas_turs" => $hotel['enlace_reservas'],
                "imagenes" => $imagenes // Las imágenes se almacenan como un subarray
            );

            $salida = array("codigo" => 1, "data" => $hotelConImagenes);
        } else {
            $salida = array("codigo" => 0, "mensaje" => "hotel no encontrada.");
        }
        return $salida;
    }
    /**
     * Elimina una imagen asociada a un hotel por su ID.
     *
     * Este método elimina el registro de la imagen en la base de datos y elimina físicamente el archivo del sistema de almacenamiento.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $idImagen ID de la imagen a eliminar.
     *
     * @return array Resultado de la operación con los siguientes valores:
     *               - "codigo" => 1 si la imagen se elimina correctamente.
     *               - "codigo" => 0 si ocurre algún error, acompañado de un mensaje descriptivo.
     *
     * @throws PDOException Lanza una excepción si ocurre un error en las consultas a la base de datos.
     */

    public function eliminarImg($conexion, $idImagen)
    {
        // Eliminar imagen por ID
        if ($idImagen !== null) {
            // Primero, obtener la ruta de la imagen desde la base de datos
            $sqlImg = "SELECT img FROM tb_imghoteles WHERE id_img = :idImagen";
            $ejecutarImg = $conexion->prepare($sqlImg);
            $ejecutarImg->bindParam(':idImagen', $idImagen, PDO::PARAM_INT);
            $ejecutarImg->execute();
            $imagen = $ejecutarImg->fetch(PDO::FETCH_ASSOC);

            if ($imagen) {
                // Ruta de la imagen en el sistema de archivos
                $rutaImagen = "../upload/hoteles/images/" . $imagen['img'];

                // Eliminar la imagen de la base de datos
                $sqlDelete = "DELETE FROM tb_imghoteles WHERE id_img = :idImagen";
                $ejecutarDelete = $conexion->prepare($sqlDelete);
                $ejecutarDelete->bindParam(':idImagen', $idImagen, PDO::PARAM_INT);
                $ejecutarDelete->execute();

                // Verificar si la imagen fue eliminada de la base de datos
                if ($ejecutarDelete->rowCount() > 0) {
                    // Intentar eliminar el archivo físico
                    if (file_exists($rutaImagen)) {
                        if (unlink($rutaImagen)) {
                            // Si se eliminó correctamente
                            $salida = array("codigo" => 1, "mensaje" => "Imagen eliminada correctamente.");
                        } else {
                            // Error al eliminar el archivo físico
                            $salida = array("codigo" => 0, "mensaje" => "Error al eliminar el archivo de imagen.");
                        }
                    } else {
                        // Archivo no existe en el sistema de archivos
                        $salida = array("codigo" => 0, "mensaje" => "El archivo de imagen no existe.");
                    }
                } else {
                    // Error al eliminar el registro en la base de datos
                    $salida = array("codigo" => 0, "mensaje" => "Error al eliminar la imagen de la base de datos.");
                }
            } else {
                // No se encontró la imagen en la base de datos
                $salida = array("codigo" => 0, "mensaje" => "Imagen no encontrada.");
            }
        } else {
            // Si no se recibe un ID de imagen válido
            $salida = array("codigo" => 0, "mensaje" => "ID de imagen no proporcionado.");
        }
        return $salida;
    }
    /**
     * Actualiza la información de un hotel existente, incluyendo su imagen de portada y las imágenes adicionales.
     *
     * Este método permite actualizar los detalles del hotel, cargar una nueva imagen de portada si se proporciona, 
     * y añadir nuevas imágenes adicionales asociadas al hotel.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param string $nombreHotel Nombre del hotel a actualizar.
     * @param string $descripcion Descripción del hotel.
     * @param array|null $imgPortada Imagen de portada proporcionada en formato $_FILES. Puede ser null si no se actualiza.
     * @param string $direccion Dirección del hotel.
     * @param string $enlace_reservas Enlace de reservas del hotel.
     * @param array|null $imgHotel Imágenes adicionales proporcionadas en formato $_FILES. Puede ser null si no se cargan nuevas imágenes.
     * @param int $id_hotelEdit ID del hotel a actualizar.
     *
     * @return array Resultado de la operación con los siguientes valores:
     *               - "codigo" => 1 si la actualización se realiza correctamente.
     *               - "codigo" => 0 si ocurre algún error, acompañado de un mensaje descriptivo.
     *
     * @throws PDOException Lanza una excepción si ocurre un error en las consultas a la base de datos.
     */

    public function editarHotel($conexion, $nombreHotel, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgHotel, $id_hotelEdit)
    {
        // Actualizar sitio existente
        if (!empty($id_hotelEdit)) {
            // Consulta base para la actualización del sitio
            $sql = "UPDATE tb_hoteles SET nombre = :nombre, descripcion_hotel = :descripcion, ubicacion_hotel = :direccion, enlace_reservas = :enlace_reservas";

            // Si hay una nueva imagen de portada, agregarla a la consulta
            if (!empty($imgPortada['name'])) {
                $imageFileType = strtolower(pathinfo($imgPortada['name'], PATHINFO_EXTENSION));
                $newFileName = uniqid('img_', true) . '.' . $imageFileType;
                $target_dir_portada = "../upload/hoteles/portadas/";
                $target_file_portada = $target_dir_portada . $newFileName;

                // Intentar mover la nueva imagen de portada
                if (move_uploaded_file($imgPortada['tmp_name'], $target_file_portada)) {
                    // Agregar el campo de la imagen de portada a la consulta SQL
                    $sql .= ", foto = :imgPortada";
                } else {
                    // Si no se puede mover la imagen, retornar error
                    $salida = array("codigo" => 0, "mensaje" => "Error al subir la nueva imagen de portada.");
                    return;
                }
            }

            // Finalizar la consulta con la condición del ID de la sitio
            $sql .= " WHERE id_hotel = :id_hotelEdit";

            // Preparar y ejecutar la consulta
            $ejecutar = $conexion->prepare($sql);
            $ejecutar->bindParam(':nombre', $nombreHotel, PDO::PARAM_STR);
            $ejecutar->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $ejecutar->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $ejecutar->bindParam(':enlace_reservas', $enlace_reservas, PDO::PARAM_STR);

            // Vincular :imgPortada solo si se definió una nueva imagen
            if (!empty($imgPortada['name'])) {
                $ejecutar->bindParam(':imgPortada', $newFileName, PDO::PARAM_STR);
            }

            $ejecutar->bindParam(':id_hotelEdit', $id_hotelEdit, PDO::PARAM_INT);
            $ejecutar->execute();


            // Procesar imágenes adicionales si se cargaron nuevas
            if (!empty($imgHotel) && isset($imgHotel['name']) && count($imgHotel['name']) > 0) {
                $filesCount = count($imgHotel['name']);
                $target_dir_img = "../upload/hoteles/images/";

                for ($i = 0; $i < $filesCount; $i++) {
                    if ($imgHotel['error'][$i] == UPLOAD_ERR_OK) {
                        // Generar un nombre único para cada imagen adicional
                        $imgName = $imgHotel['name'][$i];
                        $imageFileType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
                        $newFileName = uniqid('img_', true) . '.' . $imageFileType;
                        $target_file_img = $target_dir_img . $newFileName;

                        // Mover el archivo a la carpeta de destino para imágenes adicionales
                        if (move_uploaded_file($imgHotel['tmp_name'][$i], $target_file_img)) {
                            // Insertar el nombre de la imagen en la tabla tb_imghoteles
                            $sql = "INSERT INTO tb_imghoteles (img, id_hotel) VALUES (:img, :id_hotel)";
                            $ejecutar = $conexion->prepare($sql);
                            $ejecutar->bindParam(':img', $newFileName, PDO::PARAM_STR);
                            $ejecutar->bindParam(':id_hotel', $id_hotelEdit, PDO::PARAM_INT);
                            $ejecutar->execute();
                        } else {
                            error_log("Error al mover la imagen adicional: " . print_r(error_get_last(), true));
                        }
                    } else {
                        error_log("Error en la subida de imagen adicional: " . $imgHotel['error'][$i]);
                    }
                }
            }

            // Retornar éxito al finalizar la actualización
            $salida = array("codigo" => 1, "mensaje" => "sitio actualizada correctamente.");
        } else {
            // Si no se proporciona un ID de sitio válido
            $salida = array("codigo" => 0, "mensaje" => "ID de sitio no proporcionado.");
        }
        return $salida;
    }
    /**
     * Elimina un hotel y todas sus imágenes asociadas de la base de datos y del sistema de archivos.
     *
     * Este método realiza las siguientes acciones:
     * 1. Busca el hotel por su ID y verifica su existencia.
     * 2. Elimina la imagen de portada del hotel del sistema de archivos.
     * 3. Obtiene y elimina todas las imágenes adicionales asociadas al hotel tanto de la base de datos como del sistema de archivos.
     * 4. Elimina el registro del hotel en la base de datos.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $idHotelDelete ID del hotel a eliminar.
     *
     * @return array Resultado de la operación con los siguientes valores:
     *               - "codigo" => 1 si la eliminación se realiza correctamente.
     *               - "codigo" => 0 si ocurre algún error, acompañado de un mensaje descriptivo.
     *
     * @throws PDOException Lanza una excepción si ocurre un error en las consultas a la base de datos.
     */


    public function eliminarHotel($conexion, $idHotelDelete)
    {
        // Eliminar sitio por ID
        if (!empty($idHotelDelete)) {
            // Primero, obtener elsitio y sus imágenes asociadas
            $sqlsitio = "SELECT foto FROM tb_hoteles WHERE id_hotel = :idHotel";
            $ejecutarsitio = $conexion->prepare($sqlsitio);
            $ejecutarsitio->bindParam(':idHotel', $idHotelDelete, PDO::PARAM_INT);
            $ejecutarsitio->execute();
            $sitio = $ejecutarsitio->fetch(PDO::FETCH_ASSOC);

            if ($sitio) {
                // Eliminar la imagen de portada
                $rutaPortada = "../upload/hoteles/portadas/" . $sitio['foto'];
                if (file_exists($rutaPortada)) {
                    unlink($rutaPortada);
                }

                // Obtener imágenes adicionales
                $sqlImagenes = "SELECT img FROM tb_imghoteles WHERE id_hotel = :idHotel";
                $ejecutarImagenes = $conexion->prepare($sqlImagenes);
                $ejecutarImagenes->bindParam(':idHotel', $idHotelDelete, PDO::PARAM_INT);
                $ejecutarImagenes->execute();
                $imagenes = $ejecutarImagenes->fetchAll(PDO::FETCH_ASSOC);

                // Eliminar imágenes adicionales del sistema de archivos
                foreach ($imagenes as $img) {
                    $rutaImagen = "../upload/hoteles/images/" . $img['img'];
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }
                // Eliminar imágenes adicionales de la base de datos
                $sqlDeleteImagenes = "DELETE FROM tb_imghoteles WHERE id_hotel = :idHotel";
                $ejecutarDeleteImagenes = $conexion->prepare($sqlDeleteImagenes);
                $ejecutarDeleteImagenes->bindParam(':idHotel', $idHotelDelete, PDO::PARAM_INT);
                $ejecutarDeleteImagenes->execute();
                // Eliminar la sitio de la base de datos
                $sqlDeletesitio = "DELETE FROM tb_hoteles WHERE id_hotel = :idHotel";
                $ejecutarDeletesitio = $conexion->prepare($sqlDeletesitio);
                $ejecutarDeletesitio->bindParam(':idHotel', $idHotelDelete, PDO::PARAM_INT);
                $ejecutarDeletesitio->execute();



                $salida = array("codigo" => 1, "mensaje" => "sitio eliminada correctamente.");
            } else {
                $salida = array("codigo" => 0, "mensaje" => "sitio no encontrada.");
            }
        } else {
            $salida = array("codigo" => 0, "mensaje" => "ID de sitio no proporcionado.");
        }
        return $salida;
    }
}
