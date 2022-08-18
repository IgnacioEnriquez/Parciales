namespace Entidades
{
    export class Usuario extends Persona
    {
        id : number;
        id_perfil : number;
        perfil : string;       

        public constructor(nombre : string, correo : string, clave : string, id : number, id_perfil : number, perfil : string) 
        {
            super(nombre,correo,clave);
            this.id = id;
            this.id_perfil = id_perfil;
            this.perfil = perfil;                     
        }

        public ToString() : string
        {
            let retorno : string = '"nombre":"' + this.nombre +'","correo":"' + this.correo +'","clave":"' + this.clave +'"';

            return retorno;
        }

        public ToJSON() : string
        {
            let datosUsuario = this.ToString() + ',"id":' + this.id +',"id_perfil":' + this.id_perfil +',"perfil":"' + this.perfil +'"'
            let retorno = "{" + datosUsuario + "}";

            return retorno;
        }       
    }
}