namespace Entidades
{
    export class ProductoEnvasado extends Producto
    {
        
        codigoBarra : string;
        precio : number;
        pathFoto : string | null;
        id : number;

        constructor(nombre = "Desconocido" , origen = "Desconocido",codigoBarra = "Desconocido", precio = 0, pathFoto = null, id = 0) 
        {
            super(nombre,origen);
            this.codigoBarra = codigoBarra;
            this.precio = precio;
            this.pathFoto = pathFoto;
            this.id = id;            
        }

        public ToJSON() : string
        {
            let cadenaCompleta = super.ToJSON() + `,"codigoBarra":"${this.codigoBarra}","precio":"${this.precio}","pathFoto":"${this.pathFoto}",
            "id":"${this.id}"`; 
            
            let retorno = "{" + cadenaCompleta + "}";

            return retorno;
        }

    }


}