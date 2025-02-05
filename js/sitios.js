$(document).ready(function(){$.ajax({url:"../controller/sitios.php",type:"POST",success:function(t){let s=JSON.parse(t),i=$("#contSitios");s.forEach(t=>{let s="";try{let a=JSON.parse(t.descripcion_sitio);s=a.ops[0].insert.trim().substring(0,60)}catch(e){console.error("Error al parsear la descripci\xf3n:",e),s=t.descripcion_sitio.substring(0,60)}let o="activo"===t.like_status?"active":"none",r=`
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="../upload/sitios/portadas/${t.foto}" class="card-img-top" alt="${t.nombre}">
                            <div class="card-body">
                                <h5 class="card-title">${t.nombre}</h5>
                                <p class="card-text">${s}</p>
                                <a href="../detalle-sitio/${t.id_sitio}" class="btn btn-primary">Ver sitio</a>
                                <button id="likeBtn-${t.id_sitio}" class="btn ${"active"===o?"btn-success":"btn-outline-secondary"} like-btn" data-id="${t.id_sitio}" data-status="${o}">
                                    ${"active"===o?"Liked":"Like"}
                                </button>
                                <p class="mt-2">Likes: <span id="likeCount-${t.id_sitio}">${t.total_likes}</span></p>
                            </div>
                        </div>
                    </div>
                `;i.append(r)}),$(".like-btn").on("click",function(){let t=$(this),s=t.data("id"),i=t.data("status"),a="active"===i?"none":"active";$.ajax({url:"../controller/like_sitio.php",type:"POST",data:{id_sitio:s,like_status:a},success:function(i){console.log(i);let e=$(`#likeCount-${s}`),o=parseInt(e.text());"active"===a?(t.removeClass("btn-outline-secondary").addClass("btn-success").text("Liked"),o++):(t.removeClass("btn-success").addClass("btn-outline-secondary").text("Like"),o--),e.text(o),t.data("status",a)},error:function(t){console.error("Error al cambiar el estado de like:",t)}})})},error:function(t){console.error("Error al cargar los sitios:",t)}})});