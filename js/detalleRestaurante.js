$(document).ready(function() {
    // Inicializar Quill en un editor oculto (solo para generar HTML desde Delta)
    var quill = new Quill('#quill-editor', {
        theme: 'snow' // Tema para un editor completo
    });

    // Obtener el ID del restaurante desde la URL
    function obtenerIdRestaurante() {
        var url = window.location.href;
        return url.substring(url.lastIndexOf('/') + 1);
    }

    var id_restaurante = obtenerIdRestaurante();

    // Cargar el restaurante y sus imágenes
    function cargarRestaurante() {
        $.ajax({
            url: '../controller/detalleRestaurante.php',
            type: 'POST',
            dataType: 'json',
            data: { id_restaurante: id_restaurante },
            success: function(response) {
                console.log(response);
                // Cargar título, ubicación y enlace de reservas
                $('#titulo').html(response.nombre);
                $('#ubicacio').html(response.ubi_restaurante);
                $('#enlaceReservas').attr('href', response.enlace_reservas_rest);

                // Verificar que la descripción no esté vacía
                if (response.descripcion_restaurante) {

                    // Convertir la cadena Delta en un objeto
                    var deltaDescripcion = typeof response.descripcion_restaurante === "string" ?
                        JSON.parse(response.descripcion_restaurante) :
                        response.descripcion_restaurante;

                    // Cargar el Delta en Quill
                    quill.setContents(deltaDescripcion);

                    // Obtener el HTML generado por Quill
                    var htmlDescripcion = $('#quill-editor .ql-editor').html();

                    // Colocar el HTML en el contenedor de descripción
                    $('#descripcion').html(htmlDescripcion);
                } else {
                    console.warn("El Delta de la descripción está vacío o no es válido.");
                }

                // Cargar las imágenes del carrusel
                var imagenes = response.imagenes;
                var carouselInner = $('.carousel-inner');
                carouselInner.empty(); // Asegurarse de que esté vacío al inicio
                var isActive = true;

                imagenes.forEach(function(img) {
                    var itemClass = isActive ? 'carousel-item active' : 'carousel-item';
                    carouselInner.append(`
                        <div class="${itemClass}">
                            <img src="../upload/restaurantes/images/${img}" class="d-block w-100" alt="Imagen de restaurante">
                        </div>
                    `);
                    isActive = false; // Solo la primera imagen será activa
                });

                // Inicializar y empezar el ciclo automático del carrusel
                $('#carouselImages').carousel('cycle');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar el restaurante:', error);
            }
        });
    }

    cargarRestaurante();
    $('#carouselImages').carousel('cycle');
});
