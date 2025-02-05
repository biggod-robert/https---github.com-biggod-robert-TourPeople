$.ajax({url:"../controller/topLikes.php",type:"POST",data:{id_documento:1},success:function(t){let s=JSON.parse(t);if(s.sitios&&Array.isArray(s.sitios)){let e=$("#contSitios");s.sitios.forEach(t=>{let s="";try{let i=JSON.parse(t.descripcion_sitio);s=i.ops[0].insert.trim().substring(0,60)}catch(a){console.error("Error al parsear la descripci\xf3n:",a),s=t.descripcion_sitio.substring(0,60)}let o="activo"===t.like_status?"active":"none",r=`
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="../upload/sitios/portadas/${t.foto}" class="card-img-top" alt="${t.nombre} ${t.ubi_sitio}">
                            <div class="card-body">
                                <h5 class="card-title">${t.nombre}</h5>
                                <p class="card-text">${s}</p>
                                <a href="../detalle-sitio/${t.id_sitio}" class="btn btn-primary">Ver sitio</a>
                                <button id="likeBtn-${t.id_sitio}" class="btn ${"active"===o?"btn-success":"btn-outline-secondary"} like-btn" data-id="${t.id_sitio}" data-status="${o}">
                                    ${"active"===o?"Liked":"Like"}
                                </button>
                                <p class="mt-2">Likes: <span id="likeCountsitio-${t.id_sitio}">${t.total_likes}</span></p>
                            </div>
                        </div>
                    </div>
                `;e.append(r)})}else console.error("No se encontraron sitios populares.");if(s.hoteles&&Array.isArray(s.hoteles)){let i=$("#contHoteles");s.hoteles.forEach(t=>{let s="";try{let e=JSON.parse(t.descripcion_hotel);s=e.ops[0].insert.trim().substring(0,60)}catch(a){console.error("Error al parsear la descripci\xf3n:",a),s=t.descripcion_hotel.substring(0,60)}let o="activo"===t.like_status?"active":"none",r=`
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="../upload/hoteles/portadas/${t.foto}" class="card-img-top" alt="${t.nombre} ${t.ubicacion_hotel}">
                            <div class="card-body">
                                <h5 class="card-title">${t.nombre}</h5>
                                <p class="card-text">${s}</p>
                                <a href="../detalle-hotel/${t.id_hotel}" class="btn btn-primary">Ver hotel</a>
                                <button id="likeBtn-${t.id_hotel}" class="btn ${"active"===o?"btn-success":"btn-outline-secondary"} like-btn" data-id="${t.id_hotel}" data-status="${o}">
                                    ${"active"===o?"Liked":"Like"}
                                </button>
                                <p class="mt-2">Likes: <span id="likeCounthotel-${t.id_hotel}">${t.total_likes}</span></p>
                            </div>
                        </div>
                    </div>
                `;i.append(r)})}else console.error("No se encontraron hoteles populares.");$(".like-btn").on("click",function(){let t=$(this),s=t.data("id"),e=t.data("status"),i="active"===e?"none":"active",a=t.closest(".col-md-4").find("a").attr("href").includes("hotel")?"hotel":"sitio";$.ajax({url:"hotel"===a?"../controller/like_hotel.php":"../controller/like_sitio.php",type:"POST",data:{id:s,like_status:i},success:function(e){let o=$(`#likeCount${a}-${s}`),r=parseInt(o.text());"active"===i?(t.removeClass("btn-outline-secondary").addClass("btn-success").text("Liked"),r++):(t.removeClass("btn-success").addClass("btn-outline-secondary").text("Like"),r--),o.text(r),t.data("status",i)},error:function(t){console.error("Error al cambiar el estado de like:",t)}})})},error:function(t){console.error("Error al cargar los datos:",t)}});