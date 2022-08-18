namespace Entidades
{
    export class Producto
    {
        nombre : string;
        origen : string;

        constructor(nombre : string, origen : string) 
        {
            this.nombre = nombre;
            this. origen = origen;            
        }

        public ToString() : string
        {
            let retorno = '"nombre":"'+ this.nombre +'","origen":"' + this.origen + '"';

            return retorno;      
        }

        public ToJSON() : string
        {
            let retorno = "{" + this.ToString() + "}";

            return retorno;
        }

    }


}