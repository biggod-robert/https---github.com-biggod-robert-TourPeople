$(document).ready(function(){$.ajax({url:"../controller/restaurantes.php",type:"POST",success:function(t){console.log(t);let a=JSON.parse(t),e=$("#contRestaurantes");a.forEach(t=>{let a="";try{let s=JSON.parse(t.descripcion_restaurante);a=s.ops[0].insert.trim().substring(0,60)}catch(r){console.error("Error al parsear la descripci\xf3n:",r),a=t.descripcion_restaurante.substring(0,60)}let n="activo"===t.like_status?"active":"none",i=`
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="../upload/restaurantes/portadas/${t.foto}" class="card-img-top" alt="${t.nombre}">
                            <div class="card-body">
                                <h5 class="card-title">${t.nombre}</h5>
                                <p class="card-text">${a}</p>
                                <a href="../detalle-restaurante/${t.id_restaurante}" class="btn btn-primary">Ver restaurante</a>
                                <button id="likeBtn-${t.id_restaurante}" class="btn ${"active"===n?"btn-success":"btn-outline-secondary"} like-btn" data-id="${t.id_restaurante}" data-status="${n}">
                                    ${"active"===n?"no me gusta":"me gusta"}
                                </button>
                                <p class="mt-2">Likes: <span id="likeCount-${t.id_restaurante}">${t.total_likes}</span></p>
                            </div>
                        </div>
                    </div>
                `;e.append(i)}),$(".like-btn").on("click",function(){let t=$(this),a=t.data("id"),e=t.data("status"),s="active"===e?"none":"active";$.ajax({url:"../controller/like_restaurante.php",type:"POST",data:{id_restaurante:a,like_status:s},success:function(e){console.log(e);let r=$(`#likeCount-${a}`),n=parseInt(r.text());"active"===s?(t.removeClass("btn-outline-secondary").addClass("btn-success").text("no me gusta"),n++):(t.removeClass("btn-success").addClass("btn-outline-secondary").text("me gusta"),n--),r.text(n),t.data("status",s)},error:function(){console.error("Error al cambiar el estado de like.")}})})},error:function(){console.error("Error al obtener los restaurantes.")}})});