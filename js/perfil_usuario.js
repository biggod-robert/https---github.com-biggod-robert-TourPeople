$(document).ready(function() {
    function cargarPerfilUsuario() {
        $.ajax({
            url: '../controller/perfilUsuario.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#perfilUsuario').html(`<p>${response.error}</p>`);
                } else {
                    $('#perfilUsuario').html(`
                        <p><strong>Nombre:</strong> <span id="nombre">${response.nombre}</span></p>
                        <p><strong>Apellido:</strong> <span id="apellido">${response.apellido}</span></p>
                        <p><strong>Email:</strong> <span id="correo">${response.correo}</span></p>
                        <p><strong>Teléfono:</strong> <span id="telefono">${response.telefono}</span></p>
                        <p><strong>Imagen:</strong> <img id="imagen" src="../upload/imgUsers/${response.imagen}" alt="Imagen de perfil" width="100"></p>
                        <button id="edit-profile-btn">Editar Perfil</button>
                        <div id="edit-profile-form" style="display: none;">
                            <h2>Editar Perfil</h2>
                            <form id="update-profile-form" enctype="multipart/form-data">
                                <label for="edit-nombre">Nombre:</label>
                                <input type="text" id="edit-nombre" name="name" value="${response.nombre}" required>
                                
                                <label for="edit-apellido">Apellido:</label>
                                <input type="text" id="edit-apellido" name="apellido" value="${response.apellido}" required>
                                
                                <label for="edit-correo">Email:</label>
                                <input type="email" id="edit-correo" name="email" value="${response.correo}" required>
                                
                                <label for="edit-telefono">Teléfono:</label>
                                <input type="text" id="edit-telefono" name="telefono" value="${response.telefono}" required>
                                
                                <label for="edit-imagen">Imagen de Perfil:</label>
                                <input type="file" id="edit-imagen" name="image">
                                
                                <button type="submit">Guardar Cambios</button>
                            </form>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar el perfil del usuario:', error);
            }
        });
    }

    cargarPerfilUsuario();

    $(document).on('click', '#edit-profile-btn', function() {
        $('#edit-profile-form').toggle();
    });

    $(document).on('submit', '#update-profile-form', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '../controller/update_profile.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Perfil Actualizado',
                    text: 'Tu perfil ha sido actualizado exitosamente.',
                });
                cargarPerfilUsuario();
                $('#edit-profile-form').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error al actualizar el perfil del usuario:', error);
            }
        });
    });
});
