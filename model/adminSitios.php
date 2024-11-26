<?php
class SitiosManager
{
    /**
     * Constructor de la clase.
     * Este constructor no realiza ninguna operación al crear una nueva instancia.
     */
    public function __construct() {}

    /**
     * Realiza operaciones CRUD para la administración de sitios.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $opcion Define la operación a realizar:
     *                    1: Obtener sitios por usuario.
     *                    2: Insertar nuevo sitio.
     *                    3: Obtener sitio por ID.
     *                    4: Eliminar imagen asociada a un sitio.
     *                    5: Actualizar datos de un sitio existente.
     *                    6: Eliminar un sitio y sus imágenes.
     * @param int $id_documento ID del documento que referencia al usuario.
     * @param string $nombreSitios Nombre del sitio.
     * @param string $descripcion Descripción del sitio.
     * @param array $imgPortada Imagen de portada del sitio.
     * @param string $direccion Dirección del sitio.
     * @param string $enlace_reservas URL para reservas del sitio.
     * @param array $imgSitios Imágenes adicionales del sitio.
     * @param int $idSitios ID del sitio a recuperar o editar.
     * @param int $idImagen ID de la imagen a eliminar.
     * @param int $id_SitiosEdit ID del sitio a actualizar.
     * @param int $idSitiosDelete ID del sitio a eliminar.
     * 
     * @return array Resultado de la operación con códigos y mensajes.
     */
    public function gestionarSitios($conexion, $opcion, $id_documento, $nombreSitio, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgSitio, $idsitio, $idImagen, $id_sitioEdit, $idsitioDelete)
    {
        switch ($opcion) {
            case 1: // Obtener los datos de los sitios para mostrar en la tabla
                return $this->obtener($conexion, $id_documento);
                break;
            case 2: // Guardar nuevo sitio
                return $this->guardarSitios($conexion, $id_documento, $nombreSitio, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgSitio);
                break;
            case 3: // Obtener un sitio por su ID
                return $this->optenerSitio($conexion, $idsitio);
                break;
            case 4: // Eliminar imagen por ID
                return $this->eliminarImg($conexion, $idImagen);
                break;
            case 5: // Editar un sitio existente
                return $this->editarSitios($conexion, $nombreSitio, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgSitio, $id_sitioEdit);
                break;
            case 6: // Eliminar un sitio
                return $this->eliminarSitios($conexion, $idsitioDelete);
                break;
            default:
                return ['codigo' => 0, 'mensaje' => 'Opción no válida'];
                break;
        }
    }

    /**
     * Obtiene una lista de sitios asociados a un usuario específico.
     *
     * Este método recupera todos los registros de sitios vinculados al ID de documento proporcionado.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $id_documento ID del documento que referencia al usuario.
     *
     * @return array Devuelve un arreglo asociativo con los datos de los sitios encontrados:
     *               - Cada elemento contiene las columnas de la tabla `tb_sitios`.
     *               - Si no se encuentran registros, devuelve un arreglo vacío.
     *
     * @throws PDOException Lanza una excepción si ocurre un error durante la ejecución de la consulta.
     */
    public function obtener($conexion, $id_documento)
    {
        $sql = "SELECT * FROM tb_sitios WHERE id_documento = :id_documento";
        $ejecutar = $conexion->prepare($sql);
        $ejecutar->bindParam(':id_documento', $id_documento, PDO::PARAM_INT);
        $ejecutar->execute();
        return $ejecutar->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda un nuevo sitio en la base de datos, incluyendo una imagen de portada y múltiples imágenes adicionales.
     *
     * Este método procesa las imágenes subidas, las almacena en las carpetas correspondientes, y guarda los detalles del sitio en la base de datos.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $id_documento ID del documento que referencia al usuario.
     * @param string $nombreSitio Nombre del sitio.
     * @param string $descripcion Descripción del sitio.
     * @param array $imgPortada Datos del archivo de imagen de portada subido (de $_FILES).
     * @param string $direccion Dirección del sitio.
     * @param string $enlace_reservas URL para reservas del sitio.
     * @param array $imgSitio Datos de los archivos de imágenes adicionales subidos (de $_FILES).
     *
     * @return array Resultado de la operación, que incluye:
     *               - "codigo" => 1 si se guarda exitosamente, 0 en caso de error.
     *               - "mensaje" => Mensaje descriptivo del resultado.
     *
     * @throws PDOException Lanza una excepción si ocurre un error durante las operaciones de la base de datos.
     */
    public function guardarSitios($conexion, $id_documento, $nombreSitio, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgSitio)
    {
        // Procesar primero la imagen de portada
        $imageFileType = strtolower(pathinfo($imgPortada['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid('img_', true) . '.' . $imageFileType;
        $target_dir_portada = "../upload/sitios/portadas/";
        $target_file_portada = $target_dir_portada . $newFileName;

        if (move_uploaded_file($imgPortada['tmp_name'], $target_file_portada)) {
            // Insertar sitio
            $sql = "INSERT INTO `tb_sitios` (`id_sitio`, `nombre`, `descripcion_sitio`, `ubi_sitio`, `enlace_reservas_turs`, `foto`, `id_documento`) 
                 VALUES (NULL, :nombre, :descripcion, :direccion, :enlace_reservas, :imgPortada, :id_documento);";
            $ejecutar = $conexion->prepare($sql);
            $ejecutar->bindParam(':nombre', $nombreSitio, PDO::PARAM_STR);
            $ejecutar->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $ejecutar->bindParam(':imgPortada', $newFileName, PDO::PARAM_STR);
            $ejecutar->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $ejecutar->bindParam(':enlace_reservas', $enlace_reservas, PDO::PARAM_STR);
            $ejecutar->bindParam(':id_documento', $id_documento, PDO::PARAM_INT);
            $ejecutar->execute();

            // Obtener el ID del último sitio insertado
            $id_sitio = $conexion->lastInsertId();

            // Procesar imágenes adicionales
            if (!empty($imgSitio) && isset($imgSitio['name'])) {
                $filesCount = count($imgSitio['name']);
                $target_dir_img = "../upload/sitios/images/";

                for ($i = 0; $i < $filesCount; $i++) {
                    if ($imgSitio['error'][$i] == UPLOAD_ERR_OK) {
                        // Generar un nombre único para cada imagen adicional
                        $imgName = $imgSitio['name'][$i];
                        $imageFileType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
                        $newFileName = uniqid('img_', true) . '.' . $imageFileType;
                        $target_file_img = $target_dir_img . $newFileName;

                        // Mover el archivo a la carpeta de destino para imágenes adicionales
                        if (move_uploaded_file($imgSitio['tmp_name'][$i], $target_file_img)) {
                            // Insertar el nombre de la imagen en la tabla tb_imgSitios
                            $sql = "INSERT INTO tb_imgSitios (img, id_sitio) VALUES (:img, :id_sitio)";
                            $ejecutar = $conexion->prepare($sql);
                            $ejecutar->bindParam(':img', $newFileName, PDO::PARAM_STR);
                            $ejecutar->bindParam(':id_sitio', $id_sitio, PDO::PARAM_INT);
                            $ejecutar->execute();
                        } else {
                            error_log("Error al mover la imagen adicional: " . print_r(error_get_last(), true));
                        }
                    } else {
                        error_log("Error en la subida de imagen adicional: " . $imgSitio['error'][$i]);
                    }
                }
            }

            $salida = array("codigo" => 1, "mensaje" => "Se agregó el sitio correctamente.");
        } else {
            $salida = array("codigo" => 0, "mensaje" => "Error al subir la imagen de portada. Error: " . print_r(error_get_last(), true));
        }
        return $salida;
    }

    /**
     * Obtiene los detalles de un sitio por su ID, incluyendo las imágenes asociadas.
     *
     * Este método recupera la información principal del sitio y, si existe, las imágenes relacionadas en un formato estructurado.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $idsitio ID del sitio a consultar.
     *
     * @return array Resultado de la operación, que incluye:
     *               - "codigo" => 1 si el sitio es encontrado, 0 si no existe.
     *               - "data" => Información del sitio y sus imágenes, o un mensaje de error si no se encuentra.
     *
     * @throws PDOException Lanza una excepción si ocurre un error durante las operaciones de la base de datos.
     */
    public function optenerSitio($conexion, $idsitio)
    {
        // Obtener un sitio por su ID
        $sqlsitio = "SELECT * FROM tb_sitios WHERE id_sitio = :idsitio";
        $ejecutarsitio = $conexion->prepare($sqlsitio);
        $ejecutarsitio->bindParam(':idsitio', $idsitio, PDO::PARAM_INT);
        $ejecutarsitio->execute();
        $sitio = $ejecutarsitio->fetch(PDO::FETCH_ASSOC); // Solo un sitio, no fetchAll()

        if ($sitio) {
            // Si el sitio existe, obtener las imágenes asociadas
            $sqlImagenes = "SELECT id_img, img FROM tb_imgSitios WHERE id_sitio = :idsitio";
            $ejecutarImagenes = $conexion->prepare($sqlImagenes);
            $ejecutarImagenes->bindParam(':idsitio', $idsitio, PDO::PARAM_INT);
            $ejecutarImagenes->execute();
            $imagenes = $ejecutarImagenes->fetchAll(PDO::FETCH_ASSOC); // Traemos todas las imágenes

            // Organizar los resultados en un array
            $sitioConImagenes = array(
                "id_sitio" => $sitio['id_sitio'],
                "nombre" => $sitio['nombre'],
                "descripcion" => $sitio['descripcion_sitio'],
                "imgPortada" => $sitio['foto'],
                "ubi_sitio" => $sitio['ubi_sitio'],
                "enlace_reservas_turs" => $sitio['enlace_reservas_turs'],
                "imagenes" => $imagenes // Las imágenes se almacenan como un subarray
            );

            $salida = array("codigo" => 1, "data" => $sitioConImagenes);
        } else {
            $salida = array("codigo" => 0, "mensaje" => "Sitio no encontrado.");
        }
        return $salida;
    }
    /**
     * Elimina una imagen asociada a un sitio por su ID.
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
            $sqlImg = "SELECT img FROM tb_imgSitios WHERE id_img = :idImagen";
            $ejecutarImg = $conexion->prepare($sqlImg);
            $ejecutarImg->bindParam(':idImagen', $idImagen, PDO::PARAM_INT);
            $ejecutarImg->execute();
            $imagen = $ejecutarImg->fetch(PDO::FETCH_ASSOC);

            if ($imagen) {
                // Ruta de la imagen en el sistema de archivos
                $rutaImagen = "../upload/sitios/images/" . $imagen['img'];

                // Eliminar la imagen de la base de datos
                $sqlDelete = "DELETE FROM tb_imgSitios WHERE id_img = :idImagen";
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
     * Actualiza la información de un sitio existente, incluyendo su imagen de portada y las imágenes adicionales.
     *
     * Este método permite actualizar los detalles del sitio, cargar una nueva imagen de portada si se proporciona, 
     * y añadir nuevas imágenes adicionales asociadas al sitio.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param string $nombreSitio Nombre del sitio a actualizar.
     * @param string $descripcion Descripción del sitio.
     * @param array|null $imgPortada Imagen de portada proporcionada en formato $_FILES. Puede ser null si no se actualiza.
     * @param string $direccion Dirección del sitio.
     * @param string $enlace_reservas Enlace de reservas del sitio.
     * @param array|null $imgSitio Imágenes adicionales proporcionadas en formato $_FILES. Puede ser null si no se cargan nuevas imágenes.
     * @param int $id_sitioEdit ID del sitio a actualizar.
     *
     * @return array Resultado de la operación con los siguientes valores:
     *               - "codigo" => 1 si la actualización se realiza correctamente.
     *               - "codigo" => 0 si ocurre algún error, acompañado de un mensaje descriptivo.
     *
     * @throws PDOException Lanza una excepción si ocurre un error en las consultas a la base de datos.
     */
    public function editarSitios($conexion, $nombreSitio, $descripcion, $imgPortada, $direccion, $enlace_reservas, $imgSitio, $id_sitioEdit)
    {
        // Verificar si se ha proporcionado un ID de sitio válido
        if (!empty($id_sitioEdit)) {
            // Consulta base para la actualización del sitio
            $sql = "UPDATE tb_sitios SET nombre = :nombre, descripcion_sitio = :descripcion, ubi_sitio = :direccion, enlace_reservas_turs = :enlace_reservas";

            // Si se proporciona una nueva imagen de portada, agregarla a la consulta
            if (!empty($imgPortada['name'])) {
                $imageFileType = strtolower(pathinfo($imgPortada['name'], PATHINFO_EXTENSION));
                $newFileName = uniqid('img_', true) . '.' . $imageFileType;
                $target_dir_portada = "../upload/sitios/portadas/";
                $target_file_portada = $target_dir_portada . $newFileName;

                // Intentar mover la nueva imagen de portada al servidor
                if (move_uploaded_file($imgPortada['tmp_name'], $target_file_portada)) {
                    // Si la imagen se mueve correctamente, actualizar el campo de la imagen de portada
                    $sql .= ", foto = :imgPortada";
                } else {
                    // Error al mover la imagen de portada
                    $salida = array("codigo" => 0, "mensaje" => "Error al subir la nueva imagen de portada.");
                    return $salida; // Termina la ejecución si ocurre un error
                }
            }

            // Finalizar la consulta con la condición del ID de sitio
            $sql .= " WHERE id_sitio = :id_sitioEdit";

            // Preparar y ejecutar la consulta
            $ejecutar = $conexion->prepare($sql);
            $ejecutar->bindParam(':nombre', $nombreSitio, PDO::PARAM_STR);
            $ejecutar->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $ejecutar->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $ejecutar->bindParam(':enlace_reservas', $enlace_reservas, PDO::PARAM_STR);

            // Vincular la imagen de portada solo si se proporcionó una
            if (!empty($imgPortada['name'])) {
                $ejecutar->bindParam(':imgPortada', $newFileName, PDO::PARAM_STR);
            }

            $ejecutar->bindParam(':id_sitioEdit', $id_sitioEdit, PDO::PARAM_INT);
            $ejecutar->execute();

            // Procesar imágenes adicionales si se proporcionan
            if (!empty($imgSitio) && isset($imgSitio['name']) && count($imgSitio['name']) > 0) {
                $filesCount = count($imgSitio['name']);
                $target_dir_img = "../upload/sitios/images/";

                for ($i = 0; $i < $filesCount; $i++) {
                    if ($imgSitio['error'][$i] == UPLOAD_ERR_OK) {
                        // Generar un nombre único para cada imagen adicional
                        $imgName = $imgSitio['name'][$i];
                        $imageFileType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
                        $newFileName = uniqid('img_', true) . '.' . $imageFileType;
                        $target_file_img = $target_dir_img . $newFileName;

                        // Mover el archivo de imagen adicional al servidor
                        if (move_uploaded_file($imgSitio['tmp_name'][$i], $target_file_img)) {
                            // Insertar el nombre de la imagen adicional en la base de datos
                            $sql = "INSERT INTO tb_imgSitios (img, id_sitio) VALUES (:img, :id_sitio)";
                            $ejecutar = $conexion->prepare($sql);
                            $ejecutar->bindParam(':img', $newFileName, PDO::PARAM_STR);
                            $ejecutar->bindParam(':id_sitio', $id_sitioEdit, PDO::PARAM_INT);
                            $ejecutar->execute();
                        } else {
                            error_log("Error al mover la imagen adicional: " . print_r(error_get_last(), true));
                        }
                    } else {
                        error_log("Error en la subida de imagen adicional: " . $imgSitio['error'][$i]);
                    }
                }
            }

            // Retornar éxito al finalizar la actualización
            $salida = array("codigo" => 1, "mensaje" => "Sitio actualizado correctamente.");
        } else {
            // Si no se proporciona un ID de sitio válido
            $salida = array("codigo" => 0, "mensaje" => "ID de sitio no proporcionado.");
        }

        return $salida;
    }
    /**
     * Elimina un sitio y todas sus imágenes asociadas de la base de datos y del sistema de archivos.
     *
     * Este método realiza las siguientes acciones:
     * 1. Busca el sitio por su ID y verifica su existencia.
     * 2. Elimina la imagen de portada del sitio del sistema de archivos.
     * 3. Obtiene y elimina todas las imágenes adicionales asociadas al sitio tanto de la base de datos como del sistema de archivos.
     * 4. Elimina el registro del sitio en la base de datos.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     * @param int $idsitioDelete ID del sitio a eliminar.
     *
     * @return array Resultado de la operación con los siguientes valores:
     *               - "codigo" => 1 si la eliminación se realiza correctamente.
     *               - "codigo" => 0 si ocurre algún error, acompañado de un mensaje descriptivo.
     *
     * @throws PDOException Lanza una excepción si ocurre un error en las consultas a la base de datos.
     */
    public function eliminarSitios($conexion, $idsitioDelete)
    {
        // Verificar si se ha proporcionado un ID de sitio válido
        if (!empty($idsitioDelete)) {
            // Obtener el sitio y su imagen de portada
            $sqlsitio = "SELECT foto FROM tb_sitios WHERE id_sitio = :idsitio";
            $ejecutarsitio = $conexion->prepare($sqlsitio);
            $ejecutarsitio->bindParam(':idsitio', $idsitioDelete, PDO::PARAM_INT);
            $ejecutarsitio->execute();
            $sitio = $ejecutarsitio->fetch(PDO::FETCH_ASSOC);

            // Verificar si el sitio existe
            if ($sitio) {
                // Eliminar la imagen de portada del sistema de archivos
                $rutaPortada = "../upload/sitios/portadas/" . $sitio['foto'];
                if (file_exists($rutaPortada)) {
                    unlink($rutaPortada);
                }

                // Obtener todas las imágenes adicionales asociadas al sitio
                $sqlImagenes = "SELECT img FROM tb_imgSitios WHERE id_sitio = :idsitio";
                $ejecutarImagenes = $conexion->prepare($sqlImagenes);
                $ejecutarImagenes->bindParam(':idsitio', $idsitioDelete, PDO::PARAM_INT);
                $ejecutarImagenes->execute();
                $imagenes = $ejecutarImagenes->fetchAll(PDO::FETCH_ASSOC);

                // Eliminar cada imagen adicional del sistema de archivos
                foreach ($imagenes as $img) {
                    $rutaImagen = "../upload/sitios/images/" . $img['img'];
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }

                // Eliminar las imágenes adicionales de la base de datos
                $sqlDeleteImagenes = "DELETE FROM tb_imgSitios WHERE id_sitio = :idsitio";
                $ejecutarDeleteImagenes = $conexion->prepare($sqlDeleteImagenes);
                $ejecutarDeleteImagenes->bindParam(':idsitio', $idsitioDelete, PDO::PARAM_INT);
                $ejecutarDeleteImagenes->execute();

                // Eliminar el sitio de la base de datos
                $sqlDeletesitio = "DELETE FROM tb_sitios WHERE id_sitio = :idsitio";
                $ejecutarDeletesitio = $conexion->prepare($sqlDeletesitio);
                $ejecutarDeletesitio->bindParam(':idsitio', $idsitioDelete, PDO::PARAM_INT);
                $ejecutarDeletesitio->execute();

                // Retornar éxito al finalizar la eliminación
                $salida = array("codigo" => 1, "mensaje" => "Sitio eliminado correctamente.");
            } else {
                // Si el sitio no se encuentra
                $salida = array("codigo" => 0, "mensaje" => "Sitio no encontrado.");
            }
        } else {
            // Si no se proporciona un ID de sitio válido
            $salida = array("codigo" => 0, "mensaje" => "ID de sitio no proporcionado.");
        }

        return $salida;
    }
}
