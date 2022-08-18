"use strict";
var Entidades;
(function (Entidades) {
    class Empleado extends Entidades.Usuario {
        constructor(nombre, correo, clave, id, id_perfil, perfil, sueldo, foto) {
            super(nombre, correo, clave, id, id_perfil, perfil);
            this.sueldo = sueldo;
            this.foto = foto;
        }
    }
    Entidades.Empleado = Empleado;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=Empleado.js.map