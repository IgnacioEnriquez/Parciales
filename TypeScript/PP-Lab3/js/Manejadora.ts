namespace PrimerParcial {
  export class Manejadora implements Iparte2 {
    public static AgregarProductoJSON() {
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let origen = (<HTMLInputElement>document.getElementById("cboOrigen"))
        .value;

      let xhttp: XMLHttpRequest = new XMLHttpRequest();

      //METODO; URL; ASINCRONICO?
      xhttp.open("POST", "BACKEND/AltaProductoJSON.php", true);

      //INSTANCIO OBJETO FORMDATA
      let form: FormData = new FormData();

      //AGREGO PARAMETROS AL FORMDATA:
      form.append("nombre", nombre);
      form.append("origen", origen);

      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send(form);

      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          let respuesta = xhttp.responseText;

          let respuesta_obj = JSON.parse(respuesta);

          alert(
            "Exito : " +
              respuesta_obj.exito +
              "\n Mensaje : " +
              respuesta_obj.mensaje
          );
          console.log(respuesta);
        }
      };
    }

    public static MostrarProductosJSON() {
      let xhttp: XMLHttpRequest = new XMLHttpRequest();
      xhttp.open("GET", "./BACKEND/ListadoProductosJSON.php", true);

      xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          let respuesta: string = xhttp.responseText;
          let productosJson = JSON.parse(respuesta);
          console.log(productosJson);

          const contenerdorTabla: HTMLDivElement = <HTMLDivElement>(
            document.getElementById("divTabla")
          );
          contenerdorTabla.innerHTML = "";

          // ARMADO DE TABLA
          const tabla: HTMLTableElement = document.createElement("table");

          // Armado de thead
          const thead = document.createElement("thead");
          for (const key in productosJson[0]) {
            const th = document.createElement("th");
            let text = document.createTextNode(key.toUpperCase());
            th.appendChild(text);
            thead.appendChild(th);
          }
          // Armado de tbody
          const tbody = document.createElement("tbody");
          productosJson.forEach((producto: any) => {
            const tr = document.createElement("tr");
            for (const key in producto) {
              const td = document.createElement("td");
              let text = document.createTextNode(producto[key]);
              td.appendChild(text);
              tr.appendChild(td);
            }
            tbody.appendChild(tr);
          });
          tabla.appendChild(thead);
          tabla.appendChild(tbody);
          contenerdorTabla.appendChild(tabla); // se inyecta toda la tabla en el contenedor
        }
      };
      xhttp.send();
    }

    public static VerificarProductoJSON() {
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let origen = (<HTMLInputElement>document.getElementById("cboOrigen"))
        .value;

      let xhttp: XMLHttpRequest = new XMLHttpRequest();

      //METODO; URL; ASINCRONICO?
      xhttp.open("POST", "BACKEND/VerificarProductoJSON.php", true);

      //INSTANCIO OBJETO FORMDATA
      let form: FormData = new FormData();

      //AGREGO PARAMETROS AL FORMDATA:
      form.append("nombre", nombre);
      form.append("origen", origen);

      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send(form);

      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          let respuesta = xhttp.responseText;

          let respuesta_obj = JSON.parse(respuesta);

          alert(
            "Exito : " +
              respuesta_obj.exito +
              "\n Mensaje : " +
              respuesta_obj.mensaje
          );
          console.log(respuesta);
        }
      };
    }

    public static MostrarInfoCookie() {
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let origen = (<HTMLInputElement>document.getElementById("cboOrigen"))
        .value;

      let xhttp: XMLHttpRequest = new XMLHttpRequest();

      //METODO; URL; ASINCRONICO?
      xhttp.open(
        "GET",
        "BACKEND/MostrarCookie.php?nombre=" + nombre + "&origen=" + origen,
        true
      );

      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send();

      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          let respuesta = xhttp.responseText;

          let respuesta_obj = JSON.parse(respuesta);

          alert(
            "Exito : " +
              respuesta_obj.exito +
              "\n Mensaje : " +
              respuesta_obj.mensaje
          );
          console.log(respuesta);
        }
      };
    }

    public static AgregarProductoSinFoto() {
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let codigoBarra = (<HTMLInputElement>(
        document.getElementById("codigoBarra")
      )).value;
      let origen = (<HTMLInputElement>document.getElementById("cboOrigen"))
        .value;
      let precio = parseInt(
        (<HTMLInputElement>document.getElementById("precio")).value
      );

      let producto_json: any = {};
      producto_json.nombre = nombre;
      producto_json.codigoBarra = codigoBarra;
      producto_json.origen = origen;
      producto_json.precio = precio;

      let xhttp: XMLHttpRequest = new XMLHttpRequest();

      //METODO; URL; ASINCRONICO?
      xhttp.open("POST", "BACKEND/AgregarProductoSinFoto.php", true);

      //INSTANCIO OBJETO FORMDATA
      let form: FormData = new FormData();

      //AGREGO PARAMETROS AL FORMDATA:
      form.append("producto_json", JSON.stringify(producto_json));

      //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
      xhttp.send(form);

      //FUNCION CALLBACK
      xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          let respuesta = xhttp.responseText;

          let respuesta_obj = JSON.parse(respuesta);

          alert(
            "Exito : " +
              respuesta_obj.exito +
              "\n Mensaje : " +
              respuesta_obj.mensaje
          );
          console.log(respuesta);
        }
      };
    }

    public static MostrarProductosEnvasados() {
      let xhttp: XMLHttpRequest = new XMLHttpRequest();

      //METODO; URL; ASINCRONICO?
      xhttp.open("POST", "BACKEND/ListadoProductosEnvasados.php", true);

      //INSTANCIO OBJETO FORMDATA
      let form: FormData = new FormData();

      //AGREGO PARAMETROS AL FORMDATA:
      form.append("tabla", "json");

      xhttp.send();

      xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

          let divListado: HTMLDivElement = <HTMLDivElement>(document.getElementById("divTabla"));

          let respuesta: string = xhttp.responseText;
          let array_productos = JSON.parse(respuesta);
          console.log(array_productos);

          let tablaHTML =
            "<html><head><title>Listado de Productos Envasados</title></head>";

          tablaHTML += "<body> <h1>Listado de cursos</h1>";

          tablaHTML += "<table> <tr>";

          tablaHTML +=
            '<th ><strong>ID </strong></th>';
          tablaHTML +=
            '<th ><strong>NOMBRE </strong></th>';
          tablaHTML +=
            '<th ><strong>ORIGEN </strong></th>';
          tablaHTML +=
            '<th ><strong>CODIGOBARRA </strong></th>';
          tablaHTML +=
            '<th ><strong>PRECIO </strong></th>';
          tablaHTML +=
            '<th ><strong>FOTO </strong></th>';
          tablaHTML +=
            '<th ><strong>ACCIONES</strong></th></tr>';

          array_productos.forEach((producto: any) => {
            tablaHTML +=
              '<tr><td ><strong>' +
              producto.id +
              "</strong></td>";
            tablaHTML +=
              '<td ><strong>' +
              producto.nombre +
              "</strong></td>";
            tablaHTML +=
              '<td ><strong>' +
              producto.origen +
              "</strong></td>";
            tablaHTML +=
              '<td ><strong>' +
              producto.codigoBarra +
              "</strong></td>";
            tablaHTML +=
              '<td ><strong>' +
              producto.precio +
              "</strong></td>";

            if (producto.pathFoto != "NULL") {
              tablaHTML +=
                '<td ><img src="' +
                producto.pathFoto +
                '" width="100" height="100"></td>';
            } else {
              tablaHTML +=
                '<td ><img src="./BACKEND/img/producto_default.jpg" width="100" height="100"></td>';
            }

            let producto_json = JSON.stringify(producto);
            tablaHTML +=
                `<td >  <input type="button" value="Eliminar" class="btn btn-danger" onclick=PrimerParcial.Manejadora.EliminarProducto(${producto_json})> </td> `;
            
            tablaHTML +=
                `<td >  <input type="button" value="Modificar" class="btn btn-light" onclick=PrimerParcial.Manejadora.BtnModificarProducto(${producto_json})> </td> `;

          });

          tablaHTML += "</tr></table></body></html>";

          divListado.innerHTML = tablaHTML;
        }
      };
    }

    public EliminarProducto(obj_json: any): void {}
    public static EliminarProducto(obj_json: any): void {

      console.log(obj_json);

      let confirmacion = confirm(
        `Desea eliminar el siguiente producto envasado? :\n Nombre : ${obj_json.nombre} \n Origen: ${obj_json.origen}`
      );

      if (confirmacion === true) {
        let xhttp: XMLHttpRequest = new XMLHttpRequest();

        //METODO; URL; ASINCRONICO?
        xhttp.open("POST", "BACKEND/EliminarProductoEnvasado.php", true);

        //INSTANCIO OBJETO FORMDATA
        let form: FormData = new FormData();

        //AGREGO PARAMETROS AL FORMDATA:
        form.append(
          "producto_json",
          `{"id":"${obj_json.id}","nombre":"${obj_json.nombre}","origen":"${obj_json.origen}"}`
        );

        //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
        xhttp.send(form);

        //FUNCION CALLBACK
        xhttp.onreadystatechange = () => {
          if (xhttp.readyState == 4 && xhttp.status == 200) {

            let respuesta = xhttp.responseText;

            let respuesta_obj = JSON.parse(respuesta);

            alert(
              "Exito : " +
                respuesta_obj.exito +
                "\n Mensaje : " +
                respuesta_obj.mensaje
            );
            console.log(respuesta);

            Manejadora.MostrarProductosEnvasados();

          }
        };
      }
    }

    public ModificarProducto(): void {}
    public static ModificarProducto(): void 
    {
      let idProducto = (<HTMLInputElement>document.getElementById("idProducto")).value;
      let codigoBarra =(<HTMLInputElement>document.getElementById("codigoBarra")).value;
      let origen =(<HTMLInputElement>document.getElementById("cboOrigen")).value;
      let precio =(<HTMLInputElement>document.getElementById("precio")).value;
      let nombre =(<HTMLInputElement>document.getElementById("nombre")).value;

      let xhttp: XMLHttpRequest = new XMLHttpRequest();

        //METODO; URL; ASINCRONICO?
        xhttp.open("POST", "BACKEND/ModificarProductoEnvasado.php", true);

        //INSTANCIO OBJETO FORMDATA
        let form: FormData = new FormData();

        //AGREGO PARAMETROS AL FORMDATA:
        form.append(
          "producto_json",
          `{"id":"${idProducto}","nombre":"${nombre}","origen":"${origen}","codigoBarra":"${codigoBarra}","precio":"${precio}"}`
        );

        //ENVIO DE LA PETICION CON LOS PARAMETROS FORMDATA
        xhttp.send(form);
      
        xhttp.onreadystatechange = () => {

          if (xhttp.readyState == 4 && xhttp.status == 200) {

            let respuesta = xhttp.responseText;

            let respuesta_obj = JSON.parse(respuesta);

            alert(
              "Exito : " +
                respuesta_obj.exito +
                "\n Mensaje : " +
                respuesta_obj.mensaje
            );
            console.log(respuesta);

            Manejadora.MostrarProductosEnvasados();
          }
        };
    }

    public static BtnModificarProducto(obj: any) : void
    {
      (<HTMLInputElement>document.getElementById("idProducto")).value = obj.id;
      (<HTMLInputElement>document.getElementById("codigoBarra")).value = obj.codigoBarra;
      (<HTMLInputElement>document.getElementById("cboOrigen")).value = obj.origen;
      (<HTMLInputElement>document.getElementById("precio")).value = obj.precio;
      (<HTMLInputElement>document.getElementById("nombre")).value = obj.nombre;
      (<HTMLInputElement>document.getElementById("idProducto")).disabled = true;

    }

  }
}
