"use strict";
var Entidades;
(function (Entidades) {
    class Persona {
        constructor(nombre, correo, clave) {
            this.nombre = nombre;
            this.correo = correo;
            this.clave = clave;
        }
        ToString() {
            let retorno = '"nombre":"' + this.nombre + '","correo":"' + this.correo + '","clave":"' + this.clave + '"';
            return retorno;
        }
        ToJSON() {
            let retorno = "{" + this.ToString() + "}";
            return retorno;
        }
    }
    Entidades.Persona = Persona;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=Persona.js.map