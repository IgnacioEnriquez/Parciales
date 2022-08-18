"use strict";
var Entidades;
(function (Entidades) {
    class ProductoEnvasado extends Entidades.Producto {
        constructor(nombre = "Desconocido", origen = "Desconocido", codigoBarra = "Desconocido", precio = 0, pathFoto = null, id = 0) {
            super(nombre, origen);
            this.codigoBarra = codigoBarra;
            this.precio = precio;
            this.pathFoto = pathFoto;
            this.id = id;
        }
        ToJSON() {
            let cadenaCompleta = super.ToJSON() + `,"codigoBarra":"${this.codigoBarra}","precio":"${this.precio}","pathFoto":"${this.pathFoto}",
            "id":"${this.id}"`;
            let retorno = "{" + cadenaCompleta + "}";
            return retorno;
        }
    }
    Entidades.ProductoEnvasado = ProductoEnvasado;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=ProductoEnvasado.js.map