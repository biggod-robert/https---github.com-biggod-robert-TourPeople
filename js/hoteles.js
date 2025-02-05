$(document).ready(function(){$.ajax({url:"../controller/hoteles.php",type:"POST",success:function(t){console.log(t);let e=JSON.parse(t),a=$("#contHoteles");e.forEach(t=>{let e="";try{let s=JSON.parse(t.descripcion_hotel);e=s.ops[0].insert.trim().substring(0,60)}catch(l){console.error("Error al parsear la descripci\xf3n:",l),e=t.descripcion_hotel.substring(0,60)}let o="activo"===t.like_status?"active":"none",r=`
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="../upload/hoteles/portadas/${t.foto}" class="card-img-top" alt="${t.nombre}">
                            <div class="card-body">
                                <h5 class="card-title">${t.nombre}</h5>
                                <p class="card-text">${e}</p>
                                <a href="../detalle-hotel/${t.id_hotel}" class="btn btn-primary">Ver hotel</a>
                                <button id="likeBtn-${t.id_hotel}" class="btn ${"active"===o?"btn-success":"btn-outline-secondary"} like-btn" data-id="${t.id_hotel}" data-status="${o}">
                                    ${"active"===o?"no me gusta":"me gusta"}
                                </button>
                                <p class="mt-2">Likes: <span id="likeCount-${t.id_hotel}">${t.total_likes}</span></p>
                            </div>
                        </div>
                    </div>
                `;a.append(r)}),$(".like-btn").on("click",function(){let t=$(this),e=t.data("id"),a=t.data("status"),s="active"===a?"none":"active";$.ajax({url:"../controller/like_hotel.php",type:"POST",data:{id_hotel:e,like_status:s},success:function(a){console.log(a);let l=$(`#likeCount-${e}`),o=parseInt(l.text());"active"===s?(t.removeClass("btn-outline-secondary").addClass("btn-success").text("Liked"),o++):(t.removeClass("btn-success").addClass("btn-outline-secondary").text("Like"),o--),l.text(o),t.data("status",s)},error:function(t){console.error("Error al cambiar el estado de like:",t)}})})},error:function(t){console.error("Error al cargar los hoteles:",t)}})});