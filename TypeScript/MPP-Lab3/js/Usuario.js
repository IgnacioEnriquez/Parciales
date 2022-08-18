"use strict";
var Entidades;
(function (Entidades) {
    class Usuario extends Entidades.Persona {
        constructor(nombre, correo, clave, id, id_perfil, perfil) {
            super(nombre, correo, clave);
            this.id = id;
            this.id_perfil = id_perfil;
            this.perfil = perfil;
        }
        ToString() {
            let retorno = '"nombre":"' + this.nombre + '","correo":"' + this.correo + '","clave":"' + this.clave + '"';
            return retorno;
        }
        ToJSON() {
            let datosUsuario = this.ToString() + ',"id":' + this.id + ',"id_perfil":' + this.id_perfil + ',"perfil":"' + this.perfil + '"';
            let retorno = "{" + datosUsuario + "}";
            return retorno;
        }
    }
    Entidades.Usuario = Usuario;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=Usuario.js.map