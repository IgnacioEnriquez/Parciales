namespace Entidades
{
    export class Empleado extends Usuario
    {        
        sueldo : number;
        foto : string;    

        public constructor(nombre : string, correo : string, clave : string, id : number, id_perfil : number, perfil : string,sueldo : number, foto : string) 
        {
            super(nombre,correo,clave,id,id_perfil,perfil);
            this.sueldo = sueldo;
            this.foto = foto;                     
        }  
    }
}