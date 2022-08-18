"use strict";
var Entidades;
(function (Entidades) {
    class Producto {
        constructor(nombre, origen) {
            this.nombre = nombre;
            this.origen = origen;
        }
        ToString() {
            let retorno = '"nombre":"' + this.nombre + '","origen":"' + this.origen + '"';
            return retorno;
        }
        ToJSON() {
            let retorno = "{" + this.ToString() + "}";
            return retorno;
        }
    }
    Entidades.Producto = Producto;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=Producto.js.map