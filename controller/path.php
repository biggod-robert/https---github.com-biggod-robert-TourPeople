<?php

/**
 * class path
 * esta se encarga de dar la ruta del contenido que se va a incluir en la platilla principal 
 */
class path
{

    /**
     * esta funcion se encarga de recibir la bariable seccion la cual representa un numero
     * depeniendo el numero asi mismo la funcion retorna un nombre de ruta el cual se va a mostrar al usuario
     * 
     * @param           num         numero de seccion
     * @return          text        retorna el nombre se seccion
     */

    function search_path($seccion)
    {
        $result = '';

        switch ($seccion) {
            case 1:
                $result = 'adminSitios.phtml';
                break;
            case 2:
                $result = 'adminHoteles.phtml';
                break;
            case 3:
                $result = 'sitios.phtml';
                break;
            case 4:
                $result = 'verSitio.phtml';
                break;
            case 5:
                $result = 'hoteles.phtml';
                break;
            case 6:
                $result = 'verHotel.phtml';
                break;
            case 7:
                $result = 'homeUsers.phtml';
                break;
            case 8:
                $result = 'adminRestaurantes.phtml';
                break;
            case 9:
                $result = 'restaurantes.phtml';
                break;
            case 10:
                $result = 'ver_restaurantes.phtml';
                break;
            case 11:
                $result = 'perfilUsuario.phtml';
                break;
            case 12:
                $result = 'admin_reservas.phtml';
                break;
            default:
                # code...
                break;
        }

        return $result;
    }
}
