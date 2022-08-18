namespace Entidades
{
    export class Persona
    {
        nombre : string;
        correo : string;
        clave : string;

        public constructor(nombre : string, correo : string, clave : string) 
        {
            this.nombre = nombre;
            this.correo = correo;
            this.clave = clave;                     
        }

        public ToString() : string
        {
            let retorno : string = '"nombre":"' + this.nombre +'","correo":"' + this.correo +'","clave":"' + this.clave +'"';

            return retorno;
        }

        public ToJSON() : string
        {
            let retorno = "{" + this.ToString() + "}";

            return retorno;
        }       
    }
}