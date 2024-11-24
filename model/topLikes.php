<?php

/**
 * Obtiene los 3 sitios y los 3 hoteles más populares según la cantidad de "likes" en los últimos 5 días.
 *
 * @param PDO $conexion Conexión activa a la base de datos.
 * 
 * @return array Devuelve un array estructurado con los resultados de sitios y hoteles:
 *               [
 *                 'sitios' => [
 *                   ['id' => 'ID del sitio', 'nombre' => 'Nombre del sitio', 'likes' => 'Cantidad de likes'],
 *                   // Otros sitios
 *                 ],
 *                 'hoteles' => [
 *                   ['id' => 'ID del hotel', 'nombre' => 'Nombre del hotel', 'likes' => 'Cantidad de likes'],
 *                   // Otros hoteles
 *                 ]
 *               ]
 *               Si ocurre un error, devuelve un array vacío para sitios y hoteles:
 *               ['sitios' => [], 'hoteles' => []]
 */

function getSitiosYHotelesPopulares($conexion, $id_documento)
{
    try {
        // Llamar al procedimiento para obtener los 3 sitios con más "likes" en los últimos 5 días
        $sqlSitios = "CALL obtenerTop3SitiosLikesUltimos5Dias(:id_documento)";
        $ejecutarSitios = $conexion->prepare($sqlSitios);
        $ejecutarSitios->bindParam(':id_documento', $id_documento, PDO::PARAM_INT);
        $ejecutarSitios->execute();
        $sitios = $ejecutarSitios->fetchAll(PDO::FETCH_ASSOC);

        // Llamar a closeCursor() después de la consulta de sitios
        $ejecutarSitios->closeCursor();

        // Llamar al procedimiento para obtener los 3 hoteles con más "likes" en los últimos 5 días
        $sqlHoteles = "CALL obtenerTop3HotelesLikesUltimos5Dias(:id_documento)";
        $ejecutarHoteles = $conexion->prepare($sqlHoteles);
        $ejecutarHoteles->bindParam(':id_documento', $id_documento, PDO::PARAM_INT);
        $ejecutarHoteles->execute();
        $hoteles = $ejecutarHoteles->fetchAll(PDO::FETCH_ASSOC);

        // Llamar a closeCursor() después de la consulta de hoteles
        $ejecutarHoteles->closeCursor();

        // Verificar si los procedimientos retornaron resultados
        if (!$sitios) {
            error_log("No se encontraron sitios populares en los últimos 5 días.");
            $sitios = [];  // Asegurarse de devolver un array vacío en caso de error
        }

        if (!$hoteles) {
            error_log("No se encontraron hoteles populares en los últimos 5 días.");
            $hoteles = [];  // Asegurarse de devolver un array vacío en caso de error
        }

        // Devolver el array estructurado con los resultados de sitios y hoteles
        return [
            'sitios' => $sitios,
            'hoteles' => $hoteles
        ];
    } catch (PDOException $e) {
        error_log("Error en getSitiosYHotelesPopulares: " . $e->getMessage());
        return ['sitios' => [], 'hoteles' => []]; // Manejo de errores, devuelve arrays vacíos
    }
}
