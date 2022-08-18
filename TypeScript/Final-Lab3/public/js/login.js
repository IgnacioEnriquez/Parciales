"use strict";
$(() => {
    $("#btnEnviar").on("click", (e) => {
        e.preventDefault();
        $.ajax({
            type: 'GET',
            url: URL_API + "juguetes",
            dataType: "text",
            async: true
        })
            .done(function (obj_ret) {
            console.log(obj_ret);
            let alerta = "";
            return;
            /*if(obj_ret.exito){
                //GUARDO EN EL LOCALSTORAGE
                localStorage.setItem("jwt", obj_ret.jwt);

                alerta = ArmarAlert(obj_ret.mensaje + " redirigiendo al principal.html...");
    
                setTimeout(() => {
                    $(location).attr('href', URL_BASE + "/principal.html");
                }, 2000);
            }
            else
            {
                alerta = ArmarAlert(obj_ret.mensaje,"danger");
            }

            $("#div_mensaje").html(alerta);*/
        })
            .fail(function (jqXHR, textStatus, errorThrown) {
            //let retorno = JSON.parse(jqXHR.responseText);
            //let alerta:string = ArmarAlert(retorno.mensaje, "danger");
            console.log("Correo y/o Clave incorrectos");
            alert("Correo y/o Clave incorrectos");
            ;
        });
    });
});
//# sourceMappingURL=login.js.map