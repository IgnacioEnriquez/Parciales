namespace ModeloParcial {
    
  export class Manejadora
  {
    public static AgregarUsuarioJSON() {

      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let correo = (<HTMLInputElement>document.getElementById("correo")).value;
      let clave = (<HTMLInputElement>document.getElementById("clave")).value;
  
      //CREO UNA INSTANCIA DE XMLHTTPREQUEST
      let xhttp: XMLHttpRequest = new XMLHttpRequest();
  
      //METODO; URL; ASINCRONICO?
      xhttp.open("POST", "./BACKEND/AltaUsuarioJSON.php", true);
  
      //INSTANCIO OBJETO FORMDATA
      let form: FormData = new FormData();
  
      //AGREGO PARAMETROS AL FORMDATA:
      form.append("nombre", nombre);
      form.append("correo", correo);
      form.append("clave", clave);
  
      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send(form);
  
      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
  
        if (xhttp.readyState == 4 && xhttp.status == 200) {
  
          let respuesta = JSON.parse(xhttp.responseText);
  
          alert("Exito : " + respuesta.exito + "\n" + "Mensaje : " + respuesta.mensaje);
          console.log("Exito : " + respuesta.exito + "\n" + "Mensaje : " + respuesta.mensaje);       
        }
  
      };        
    }

    public static MostrarUsuariosJSON() {

      let divTabla = (<HTMLDivElement>document.getElementById("divTabla"));      
  
      //CREO UNA INSTANCIA DE XMLHTTPREQUEST
      let xhttp: XMLHttpRequest = new XMLHttpRequest();
  
      //METODO; URL; ASINCRONICO?
      xhttp.open("GET", "./BACKEND/ListadoUsuariosJSON.php", true);  
  
      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send();
  
      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
  
        if (xhttp.readyState == 4 && xhttp.status == 200) {
  
          let response = xhttp.responseText;

          let respuesta = JSON.parse(response);
  
          if(respuesta.exito === true)
          {
            let array_usuarios = JSON.parse(respuesta.mensaje);
            console.log(array_usuarios);
            divTabla.innerHTML = "";
            
            //Armo la tabla
            const tabla : HTMLTableElement = document.createElement("table");

            //Armo el THEAD
            const thead = document.createElement("thead");

            for (const key in array_usuarios[0]) 
            {
              if(key !== "clave")
              {
                const th = document.createElement("th");
                let text = document.createTextNode(key.toUpperCase());
                th.appendChild(text);
                thead.appendChild(th);  
              }

                          
            }

            //Armado del TBODY

            const tbody = document.createElement("tbody");

            array_usuarios.forEach((usuario : any) => {

              const tr = document.createElement("tr");

              for (const key in usuario)
              {
                if(key !== "clave")
                {
                  const td = document.createElement("td");
                  let text = document.createTextNode(usuario[key]);
                  td.appendChild(text);
                  tr.appendChild(td);
                }
               

              } 

              tbody.appendChild(tr);                       
            });

            tabla.appendChild(thead);
            tabla.appendChild(tbody);
            divTabla.appendChild(tabla);

          }
          else
          {
            alert(respuesta.mensaje);
            console.log(respuesta.mensaje);
          }
        }
  
      };        
    }

    public static VerificarUsuarioJSON()
    {
       let retorno : any = {};
       retorno.correo = (<HTMLInputElement>document.getElementById("correo")).value;
       retorno.clave = (<HTMLInputElement>document.getElementById("clave")).value;

       let usuario_json = JSON.stringify(retorno);
  
      //CREO UNA INSTANCIA DE XMLHTTPREQUEST
      let xhttp: XMLHttpRequest = new XMLHttpRequest();
  
      //METODO; URL; ASINCRONICO?
      xhttp.open("POST", "./BACKEND/VerificarUsuarioJSON.php", true);
  
      //INSTANCIO OBJETO FORMDATA
      let form: FormData = new FormData();
  
      //AGREGO PARAMETROS AL FORMDATA:
      form.append("usuario_json", usuario_json);
  
      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send(form);
  
      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
  
        if (xhttp.readyState == 4 && xhttp.status == 200) {
  
          let respuesta = JSON.parse(xhttp.responseText);
  
          alert("Exito : " + respuesta.exito + "\n" + "Mensaje : " + respuesta.mensaje);
          console.log("Exito : " + respuesta.exito + "\n" + "Mensaje : " + respuesta.mensaje);       
        }
  
      };        

    }

    public static AgregarUsuario()
    {
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let correo = (<HTMLInputElement>document.getElementById("correo")).value;
      let clave = (<HTMLInputElement>document.getElementById("clave")).value;
      let id_perfil = (<HTMLSelectElement>document.getElementById("cboPerfiles")).value;

  
      //CREO UNA INSTANCIA DE XMLHTTPREQUEST
      let xhttp: XMLHttpRequest = new XMLHttpRequest();
  
      //METODO; URL; ASINCRONICO?
      xhttp.open("POST", "./BACKEND/AltaUsuario.php", true);
  
      //INSTANCIO OBJETO FORMDATA
      let form: FormData = new FormData();
  
      //AGREGO PARAMETROS AL FORMDATA:
      form.append("nombre", nombre);
      form.append("correo", correo);
      form.append("clave", clave);
      form.append("id_perfil", id_perfil);

  
      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send(form);
  
      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
  
        if (xhttp.readyState == 4 && xhttp.status == 200) {
  
          let respuesta = JSON.parse(xhttp.responseText);
  
          alert("Exito : " + respuesta.exito + "\n" + "Mensaje : " + respuesta.mensaje);
          console.log("Exito : " + respuesta.exito + "\n" + "Mensaje : " + respuesta.mensaje);       
        }
  
      };        

    }

    public static MostrarUsuarios()
    {
      let divTabla = (<HTMLDivElement>document.getElementById("divTabla"));      
  
      //CREO UNA INSTANCIA DE XMLHTTPREQUEST
      let xhttp: XMLHttpRequest = new XMLHttpRequest();
  
      //METODO; URL; ASINCRONICO?
      xhttp.open("GET", "./BACKEND/ListadoUsuarios.php", true);  
  
      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send();
  
      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
  
        if (xhttp.readyState == 4 && xhttp.status == 200) {
  
          let response = xhttp.responseText;

          divTabla.innerHTML = response;           
        }
  
      };        

    }

    public static ModificarUsuario()
    {
      
    }

  }    

}
