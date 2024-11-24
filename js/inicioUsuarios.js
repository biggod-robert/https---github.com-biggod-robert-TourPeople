$.ajax({
    url: '../controller/topLikes.php',
    type: 'POST',
    data: {
        id_documento: 1 // Aquí debes poner el id_documento del usuario actual
    },
    success: function(resp) {
        const data = JSON.parse(resp);

        // Verificar que los datos no sean vacíos antes de procesar
        if (data.sitios && Array.isArray(data.sitios)) {
            const contSitios = $('#contSitios');
            data.sitios.forEach(sitio => {
                let descripcion = '';
                try {
                    const descripcionObj = JSON.parse(sitio.descripcion_sitio);
                    descripcion = descripcionObj.ops[0].insert.trim().substring(0, 60);
                } catch (error) {
                    console.error("Error al parsear la descripción:", error);
                    descripcion = sitio.descripcion_sitio.substring(0, 60);
                }

                const likeStatus = sitio.like_status === 'activo' ? 'active' : 'none';
                const likeButtonClass = likeStatus === 'active' ? 'btn-success' : 'btn-outline-secondary';
                const likeText = likeStatus === 'active' ? 'Liked' : 'Like';

                const sitioHtml = `
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="../upload/sitios/portadas/${sitio.foto}" class="card-img-top" alt="${sitio.nombre} ${sitio.ubi_sitio}">
                            <div class="card-body">
                                <h5 class="card-title">${sitio.nombre}</h5>
                                <p class="card-text">${descripcion}</p>
                                <a href="../detalle-sitio/${sitio.id_sitio}" class="btn btn-primary">Ver sitio</a>
                                <button id="likeBtn-${sitio.id_sitio}" class="btn ${likeButtonClass} like-btn" data-id="${sitio.id_sitio}" data-status="${likeStatus}">
                                    ${likeText}
                                </button>
                                <p class="mt-2">Likes: <span id="likeCountsitio-${sitio.id_sitio}">${sitio.total_likes}</span></p>
                            </div>
                        </div>
                    </div>
                `;
                contSitios.append(sitioHtml);
            });
        } else {
            console.error("No se encontraron sitios populares.");
        }

        // Procesar los hoteles
        if (data.hoteles && Array.isArray(data.hoteles)) {
            const contHoteles = $('#contHoteles');
            data.hoteles.forEach(hotel => {
                let descripcion = '';
                try {
                    const descripcionObj = JSON.parse(hotel.descripcion_hotel);
                    descripcion = descripcionObj.ops[0].insert.trim().substring(0, 60);
                } catch (error) {
                    console.error("Error al parsear la descripción:", error);
                    descripcion = hotel.descripcion_hotel.substring(0, 60);
                }

                const likeStatus = hotel.like_status === 'activo' ? 'active' : 'none';
                const likeButtonClass = likeStatus === 'active' ? 'btn-success' : 'btn-outline-secondary';
                const likeText = likeStatus === 'active' ? 'Liked' : 'Like';

                const hotelHtml = `
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="../upload/hoteles/portadas/${hotel.foto}" class="card-img-top" alt="${hotel.nombre} ${hotel.ubicacion_hotel}">
                            <div class="card-body">
                                <h5 class="card-title">${hotel.nombre}</h5>
                                <p class="card-text">${descripcion}</p>
                                <a href="../detalle-hotel/${hotel.id_hotel}" class="btn btn-primary">Ver hotel</a>
                                <button id="likeBtn-${hotel.id_hotel}" class="btn ${likeButtonClass} like-btn" data-id="${hotel.id_hotel}" data-status="${likeStatus}">
                                    ${likeText}
                                </button>
                                <p class="mt-2">Likes: <span id="likeCounthotel-${hotel.id_hotel}">${hotel.total_likes}</span></p>
                            </div>
                        </div>
                    </div>
                `;
                contHoteles.append(hotelHtml);
            });
        } else {
            console.error("No se encontraron hoteles populares.");
        }

        // Evento para los botones de like
        $('.like-btn').on('click', function() {
            const button = $(this);
            const idItem = button.data('id');
            const currentStatus = button.data('status');
            const newStatus = currentStatus === 'active' ? 'none' : 'active';
            const tipo = button.closest('.col-md-4').find('a').attr('href').includes('hotel') ? 'hotel' : 'sitio';
            const url = tipo === 'hotel' ? '../controller/like_hotel.php' : '../controller/like_sitio.php';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id: idItem,
                    like_status: newStatus
                },
                success: function(resp) {
                    const likeCountElement = $(`#likeCount${tipo}-${idItem}`);
                    let likeCount = parseInt(likeCountElement.text());

                    if (newStatus === 'active') {
                        button.removeClass('btn-outline-secondary').addClass('btn-success').text('Liked');
                        likeCount++;
                    } else {
                        button.removeClass('btn-success').addClass('btn-outline-secondary').text('Like');
                        likeCount--;
                    }

                    likeCountElement.text(likeCount);
                    button.data('status', newStatus);
                },
                error: function(err) {
                    console.error('Error al cambiar el estado de like:', err);
                }
            });
        });
    },
    error: function(err) {
        console.error('Error al cargar los datos:', err);
    }
});