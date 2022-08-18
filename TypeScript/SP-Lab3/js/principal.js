"use strict";
$(() => {
    ListarUsuarios();
    ListarJuguetes();
    AltaJuguetes();
});
function ListarUsuarios() {
    $("#listar_usuarios").on("click", () => {
        ObtenerListadoUsuarios();
    });
}
function ObtenerListadoUsuarios() {
    $("#divTablaDer").html("");
    let jwt = localStorage.getItem("jwt");
    $.ajax({
        type: 'GET',
        url: URL_API + "listarUsuariosBD",
        dataType: "json",
        data: {},
        headers: { 'Authorization': 'Bearer ' + jwt },
        async: true
    })
        .done(function (resultado) {
        if (resultado.exito) {
            let tabla = ArmarTablaUsuarios(resultado.dato);
            $("#divTablaDer").html(tabla).show(1000);
        }
        else {
            console.log("Token invalido");
            alert("Token invalido");
            setTimeout(() => {
                $(location).attr("href", "./login.html");
            }, 2000);
        }
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        //let retorno = JSON.parse(jqXHR.responseText);
        //let alerta:string = ArmarAlert(retorno.mensaje, "danger");
        console.log("Token invalido");
        alert("Token invalido");
        setTimeout(() => {
            $(location).attr("href", "./login.html");
        }, 2000);
    });
}
function ArmarTablaUsuarios(usuarios) {
    let tabla = '<table class="table table-success table-striped table-hover">';
    tabla += '<tr><th>CORREO</th><th>NOMBRE</th><th>APELLIDO</th><th>FOTO</th><th>PERFIL</th></tr>';
    if (usuarios.length == 0) {
        tabla += '<tr><td>---</td><td>---</td><td>---</td><td>---</td><th>---</td></tr>';
    }
    else {
        usuarios.forEach((user) => {
            tabla += "<tr>";
            for (const key in user) {
                if (key != "foto" && key != "clave" && key != "id") {
                    tabla += "<td>" + user[key] + "</td>";
                }
                else if (key == "foto") {
                    tabla += "<td><img src='" + URL_API + user.foto + "' width='50px' height='50px'></td>";
                }
            }
            // tabla += "<td><a href='#' class='btn' data-action='modificar-usuario' data-obj_user='"+JSON.stringify(user)+"' title='Modificar'"+
            // " data-toggle='modal' data-target='#ventana_modal_prod' ><span class='fas fa-edit'></span></a>";
            // tabla += "<a href='#' class='btn' data-action='eliminar-usuario' data-obj_user='"+JSON.stringify(user)+"' title='Eliminar'"+
            // " data-toggle='modal' data-target='#ventana_modal_prod' ><span class='fas fa-times'></span></a>";
            // tabla += "</td>";
            tabla += "</tr>";
        });
    }
    tabla += "</table>";
    return tabla;
}
function ListarJuguetes() {
    $("#listar_juguetes").on("click", () => {
        ObtenerListadoJuguetes();
    });
}
function ObtenerListadoJuguetes() {
    $("#divTablaIzq").html("");
    let jwt = localStorage.getItem("jwt");
    $.ajax({
        type: 'GET',
        url: URL_API + "listarJuguetesBD",
        dataType: "json",
        data: {},
        headers: { 'Authorization': 'Bearer ' + jwt },
        async: true
    })
        .done(function (resultado) {
        if (resultado.exito) {
            let tabla = ArmarTablaJuguetes(resultado.dato);
            $("#divTablaIzq").html(tabla).show(1000);
        }
        else {
            console.log("Token invalido");
            alert("Token invalido");
            setTimeout(() => {
                $(location).attr("href", "./login.html");
            }, 2000);
        }
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        //let retorno = JSON.parse(jqXHR.responseText);
        //let alerta:string = ArmarAlert(retorno.mensaje, "danger");
        console.log("Token invalido");
        alert("Token invalido");
        setTimeout(() => {
            $(location).attr("href", "./login.html");
        }, 2000);
    });
}
function ArmarTablaJuguetes(juguetes) {
    let tabla = '<table class="table table-success table-striped table-hover">';
    tabla += '<tr><th>MARCA</th><th>PRECIO</th><th>FOTO</th></tr>';
    if (juguetes.length == 0) {
        tabla += '<tr><td>---</td><td>---</td><td>---</td><td>---</td><th>---</td></tr>';
    }
    else {
        juguetes.forEach((toy) => {
            tabla += "<tr>";
            for (const key in toy) {
                if (key != "path_foto") {
                    tabla += "<td>" + toy[key] + "</td>";
                }
                else if (key == "path_foto") {
                    tabla += "<td><img src='" + URL_API + toy.path_foto + "' width='50px' height='50px'></td>";
                }
            }
            // tabla += "<td><a href='#' class='btn' data-action='modificar-usuario' data-obj_user='"+JSON.stringify(user)+"' title='Modificar'"+
            // " data-toggle='modal' data-target='#ventana_modal_prod' ><span class='fas fa-edit'></span></a>";
            // tabla += "<a href='#' class='btn' data-action='eliminar-usuario' data-obj_user='"+JSON.stringify(user)+"' title='Eliminar'"+
            // " data-toggle='modal' data-target='#ventana_modal_prod' ><span class='fas fa-times'></span></a>";
            // tabla += "</td>";
            tabla += "</tr>";
        });
    }
    tabla += "</table>";
    return tabla;
}
function AltaJuguetes() {
    $("#alta_juguete").on("click", () => {
        ArmarFormularioAlta();
    });
}
function ArmarFormularioAlta() {
    $("#divTablaDer").html("");
    let formulario = MostrarFormAlta();
    $("#divTablaDer").html(formulario).show(1000);
}
function MostrarFormAlta() {
    let form = `<div class="form-bottom" style="background:darkcyan">
        
    <form role="form" action="" method="" class="">
        <br>
        <div class="form-group">                                  
            <div class="input-group m-2">
                <div class="input-group-prepend">
                    <span class="input-group-text fas fa-trademark"></span> 
                    <input type="text" class="form-control" name="marca" id="txtMarca" style="width:248px;" placeholder="Marca" />
                </div>
            </div>
        </div>

        <div class="form-group">    
            <div class="input-group m-2">
                <div class="input-group-prepend">
                    <span class="input-group-text fas fa-dollar-sign"></span> 
                    <input type="text" class="form-control" name="precio" id="txtPrecio" style="width:250px;" placeholder="Precio" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="input-group m-2">
                <div class="input-group-prepend">
                    <span class="input-group-text fas fa-camera"></span> 
                    <input type="file" class="form-control" name="foto" id="txtFoto" style="width:250px;" placeholder="Foto" />
                </div>
            </div>
        </div>

        <div class="row m-2">
            <div class="col-6">
                <button type="button" class="btn btn-success btn-block" id="btnEnviar" onclick="Agregar(event)">Agregar</button>
            </div>
            <div class="col-6">
                <button type="reset" class="btn btn-warning btn-block" id="btnReset">Limpiar</button>
            </div>
        </div>

        

        <br>
    </form>
</div>`;
    return form;
}
function Agregar(e) {
    let jwt = localStorage.getItem("jwt");
    let marca = $("#txtMarca").val();
    let precio = $("#txtPrecio").val();
    let foto = document.getElementById("txtFoto");
    let form = new FormData();
    form.append("juguete_json", JSON.stringify({ "marca": marca, "precio": precio }));
    form.append("foto", foto.files[0]);
    $.ajax({
        type: 'POST',
        url: URL_API + "agregarJugueteBD",
        dataType: "text",
        cache: false,
        contentType: false,
        processData: false,
        data: form,
        headers: { 'Authorization': 'Bearer ' + jwt },
        async: true
    })
        .done(function (resultado) {
        let retorno = JSON.parse(resultado);
        console.log(retorno.mensaje);
        alert(retorno.mensaje);
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Token invalido");
        alert("Token invalido");
        setTimeout(() => {
            $(location).attr("href", "./login.html");
        }, 2000);
    });
}
//# sourceMappingURL=principal.js.map