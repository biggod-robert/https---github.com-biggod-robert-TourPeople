$(document).ready(function(){function e(){$.ajax({url:"../controller/perfilUsuario.php",type:"POST",dataType:"json",success:function(e){e.error?$("#perfilUsuario").html(`<p>${e.error}</p>`):$("#perfilUsuario").html(`
    <p><strong>Nombre:</strong> <span id="nombre">${e.nombre}</span></p>
    <p><strong>Apellido:</strong> <span id="apellido">${e.apellido}</span></p>
    <p><strong>Email:</strong> <span id="correo">${e.correo}</span></p>
    <p><strong>Tel\xe9fono:</strong> <span id="telefono">${e.telefono}</span></p>
    <p><strong>Imagen:</strong> <img id="imagen" src="../upload/imgUsers/${e.imagen}" alt="Imagen de perfil" width="100"></p>
    <button id="edit-profile-btn">Editar Perfil</button>
    <div id="edit-profile-form" style="display: none;">
        <h2>Editar Perfil</h2>
        <form id="update-profile-form" enctype="multipart/form-data">
            <label for="edit-nombre">Nombre:</label>
            <input type="text" id="edit-nombre" name="name" value="${e.nombre}" required>
            
            <label for="edit-apellido">Apellido:</label>
            <input type="text" id="edit-apellido" name="apellido" value="${e.apellido}" required>
            
            <label for="edit-correo">Email:</label>
            <input type="email" id="edit-correo" name="email" value="${e.correo}" required>
            
            <label for="edit-telefono">Tel\xe9fono:</label>
            <input type="text" id="edit-telefono" name="telefono" value="${e.telefono}" required>
            
            <label for="edit-imagen">Imagen de Perfil:</label>
            <input type="file" id="edit-imagen" name="image">
            
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
`)},error:function(e,r,o){console.error("Error al cargar el perfil del usuario:",o)}})}e(),$(document).on("click","#edit-profile-btn",function(){$("#edit-profile-form").toggle()}),$(document).on("submit","#update-profile-form",function(r){r.preventDefault();var o=new FormData(this);$.ajax({url:"../controller/update_profile.php",type:"POST",data:o,contentType:!1,processData:!1,success:function(r){Swal.fire({icon:"success",title:"Perfil Actualizado",text:"Tu perfil ha sido actualizado exitosamente."}),e(),$("#edit-profile-form").hide()},error:function(e,r,o){console.error("Error al actualizar el perfil del usuario:",o)}})})});