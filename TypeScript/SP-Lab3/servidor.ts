import { json } from "stream/consumers";

const express = require("express");

const app = express();

app.set("puerto", 2022);

app.listen(app.get("puerto"), () => {
  console.log("Servidor corriendo sobre puerto:", app.get("puerto"));
});

app.get("/", (request: any, response: any) => {
  response.send("GET - servidor NodeJS");
});

//AGREGO FILE SYSTEM
const fs = require("fs");

//AGREGO JSON
app.use(express.json());

//AGREGO JWT
const jwt = require("jsonwebtoken");

//SE ESTABLECE LA CLAVE SECRETA PARA EL TOKEN
app.set("key", "cl@ve_secreta");

app.use(express.urlencoded({ extended: false }));

//AGREGO MULTER
const multer = require("multer");

//AGREGO MIME-TYPES
const mime = require("mime-types");

//AGREGO STORAGE
const storage = multer.diskStorage({
  destination: "public/juguetes/fotos/",
});

const upload = multer({
  storage: storage,
});

//AGREGO CORS (por default aplica a http://localhost)
const cors = require("cors");

app.use(cors());

//DIRECTORIO DE ARCHIVOS ESTÁTICOS
app.use(express.static("public"));

//AGREGO MYSQL y EXPRESS-MYCONNECTION
const mysql = require("mysql");
const myconn = require("express-myconnection");
const db_options = {
  host: "localhost",
  port: 3306,
  user: "root",
  password: "",
  database: "jugueteria_bd",
};

//AGREGO CONEXION BD
app.use(myconn(mysql, db_options, "single"));

//------------------------------------------------------------------ Middlewares ------------------------------------------------------------------------------------------------------------------------

const verificar_jwt = express.Router();
const verificar_usuario = express.Router();


verificar_usuario.use((request: any, response: any, next: any) => {

  let obj = request.body;

  request.getConnection((err: any, conn: any) => {
    if (err) throw "Error al conectarse a la base de datos.";

    conn.query(
      "SELECT * FROM usuarios WHERE correo = ? and clave = ? ",
      [obj.correo, obj.clave],
      (err: any, rows: any) => {
        if (err) throw "Error en consulta de base de datos.";

        if (rows.length == 1) {
          response.obj_usuario = rows[0];
          //SE INVOCA AL PRÓXIMO CALLEABLE
          next();
        } else {
          response.status(401).json({
            exito: false,
            mensaje: "Correo y/o Clave incorrectos.",
            jwt: null,
          });
        }
      }
    );
  });
});

verificar_jwt.use((request: any, response: any, next: any) => {

    let obj_retorno = {

        exito: false,
        mensaje: "El JWT no es valido.",
        status: 403
    }

    //SE RECUPERA EL TOKEN DEL ENCABEZADO DE LA PETICIÓN
    let token = request.headers["x-access-token"] || request.headers["authorization"];
  
    if (!token) {
      response.status(obj_retorno.status).send(obj_retorno);
      return;
    }
  
    if (token.startsWith("Bearer ")) {
      token = token.slice(7, token.length);
    }
  
    if (token) {
      //SE VERIFICA EL TOKEN CON LA CLAVE SECRETA
      jwt.verify(token, app.get("key"), (error: any, decoded: any) => {

        if (error) {

          return response.status(obj_retorno.status).json(obj_retorno);

        } else {

          console.log("middleware verificar_jwt");  
          //SE AGREGA EL TOKEN AL OBJETO DE LA RESPUESTA
          response.jwt = decoded;
          //SE INVOCA AL PRÓXIMO CALLEABLE
          next();
        }
      });
    }
  });


//------------------------------------------------------------------ RUTAS JUGUETES BD ------------------------------------------------------------------------------------------------------------------------

app.get("/listarUsuariosBD",verificar_jwt,  (request: any, response: any) => {
  let obj_retorno = {
    exito: false,
    mensaje: "No se encuentran usuarios en la BD",
    dato: {},
    status: 424,
  };

  request.getConnection((err: any, conn: any) => {
    if (err) throw "Error al conectarse a la base de datos.";

    conn.query("select * from usuarios", (err: any, rows: any) => {
      if (err) throw "Error en consulta de base de datos.";

      if (rows.length == 0) {
        response.status(obj_retorno.status).json(obj_retorno);
      } else {
        obj_retorno.exito = true;
        obj_retorno.mensaje = "Listado de Usuarios";
        obj_retorno.dato = rows;
        obj_retorno.status = 200;

        response.status(obj_retorno.status).json(obj_retorno);
      }
    });
  });
});

app.post("/agregarJugueteBD",upload.single("foto"), verificar_jwt, (request: any, response: any) => {
    let obj_retorno = {
      exito: false,
      mensaje: "No se pudo agregar el Juguete a la BD",
    };

    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let juguete_obj = JSON.parse(request.body.juguete_json);
    let path: string = file.destination + juguete_obj.marca + "." + extension;

    console.log(path);

    juguete_obj.path_foto = path.split("public/")[1];

    request.getConnection((err: any, conn: any) => {
      if (err) throw "Error al conectarse a la base de datos.";

      conn.query(
        "INSERT INTO juguetes set ?",
        [juguete_obj],
        (err: any, rows: any) => {
          if (err) {
            console.log(err);
            throw "Error en consulta de base de datos.";
          }

          obj_retorno.exito = true;
          obj_retorno.mensaje = "Se pudo agregar correctamente el Juguete";

          //Guardo la foto cuando se haya guardado el Juguete en la BD
          fs.renameSync(file.path, path);

          response.json(obj_retorno);
        }
      );
    });
  }
);

app.get("/listarJuguetesBD",verificar_jwt, (request: any, response: any) => {
  let obj_retorno = {
    exito: false,
    mensaje: "No se encuentran juguetes en la BD",
    dato: {},
    status: 403,
  };

  request.getConnection((err: any, conn: any) => {
    if (err) throw "Error al conectarse a la base de datos.";

    conn.query("select * from juguetes", (err: any, rows: any) => {
      if (err) throw "Error en consulta de base de datos.";

      if (rows.length == 0) {
        response.status(obj_retorno.status).json(obj_retorno);
      } else {
        obj_retorno.exito = true;
        obj_retorno.mensaje = "Listado de Juguetes";
        obj_retorno.dato = rows;
        obj_retorno.status = 200;

        response.status(obj_retorno.status).json(obj_retorno);
      }
    });
  });
});

app.delete("/toys",verificar_jwt,(request: any, response: any) => {
    let obj_retorno = {
      exito: false,
      mensaje: "No se pudo eliminar el Juguete a la BD",
      status: 418,
    };
  
    let id_juguete = JSON.parse(request.body.id_juguete);
    let path_foto: string = "public/";
  
    request.getConnection((err: any, conn: any) => {
      if (err) throw "Error al conectarse a la base de datos.";
  
      //Obtengo el path de la foto del producto a ser eliminado
      conn.query(
        "select path_foto from juguetes where id = ?",
        [id_juguete],
        (err: any, result: any) => {
          if (err) throw "Error en consulta de base de datos.";
  
          if (result.length != 0) {
            //console.log(result[0].foto);
            path_foto += result[0].path_foto;
          }
        }
      );
    });
  
    request.getConnection((err: any, conn: any) => {
      if (err) throw "Error al conectarse a la base de datos.";
  
      conn.query(
        "DELETE FROM juguetes WHERE id = ?",
        [id_juguete],
        (err: any, rows: any) => {
          if (err) {
            console.log(err);
            throw "Error en consulta de base de datos.";
          }
  
          if (fs.existsSync(path_foto) && path_foto != "public/") {
            fs.unlink(path_foto, (err: any) => {
              if (err) throw err;
              console.log(path_foto + " fue borrado.");
            });
          }
  
          if (rows.affectedRows == 0) {
            response.status(obj_retorno.status).json(obj_retorno);
          } else {
            obj_retorno.exito = true;
            obj_retorno.mensaje = "Juguete Eliminado!";
            obj_retorno.status = 200;
            response.status(obj_retorno.status).json(obj_retorno);
          }
        }
      );
    });
});
  
app.post("/toys",upload.single("foto"),verificar_jwt ,(request: any, response: any) => {
  
      let obj_retorno = {
        exito: false,
        mensaje: "No se pudo eliminar el Juguete a la BD",
        status: 418,
      };
  
      let file = request.file;
      let extension = mime.extension(file.mimetype);
      let juguete_obj = JSON.parse(request.body.juguete);  
      let path: string = file.destination + juguete_obj.marca + "_modificacion." + extension;
  
      juguete_obj.path = path.split("public/")[1];
      
      let obj_modif : any = {};
      //para excluir el ID
      obj_modif.marca = juguete_obj.marca;
      obj_modif.precio = juguete_obj.precio;
      obj_modif.path_foto = juguete_obj.path;
  
      request.getConnection((err:any, conn:any)=>{
  
          if(err) throw("Error al conectarse a la base de datos.");
  
          conn.query("UPDATE juguetes SET ? WHERE id = ?", [obj_modif, juguete_obj.id_juguete], (err:any, rows:any)=>{
  
              if(err) {console.log(err); throw("Error en consulta de base de datos.");}
  
              if (rows.affectedRows > 0)
              {
                  fs.renameSync(file.path, path);  
                  obj_retorno.status = 200;
                  obj_retorno.exito = true;
                  obj_retorno.mensaje = "El juguete fue modificado correctamente";                           
              }          
  
              response.status(obj_retorno.status).json(obj_retorno);           
  
          });
      });
     
});

//------------------------------------------------------------------ RUTAS USUARIOS LOGIN ------------------------------------------------------------------------------------------------------------------------
app.post("/login",verificar_usuario,(request: any, response: any) => {
  let obj_retorno = {
    exito: true,
    mensaje: "JWT Creado",
    jwt: "",
    status: 200,
  };

  const usuario = response.obj_usuario;

  //SE CREA EL PAYLOAD CON LOS ATRIBUTOS QUE NECESITAMOS
  const payload = {
    alumno: "Enriquez Ignacio",
    usuario: {
      id: usuario.id,
      correo: usuario.correo,
      nombre: usuario.nombre,
      apellido: usuario.apellido,
      foto: usuario.foto,
      perfil: usuario.perfil,
    },
    parcial: "Segundo Parcial Laboratorio 3",
  };

  //SE FIRMA EL TOKEN CON EL PAYLOAD Y LA CLAVE SECRETA
  const token = jwt.sign(payload, app.get("key"), {
    expiresIn: "2m",
  });

  obj_retorno.jwt = token;

  response.status(obj_retorno.status).json(obj_retorno);
});

app.get("/login", (request: any, response: any) => {
  let obj_respuesta = {
    exito: false,
    mensaje: "El JWT es requerido!!!",
    payload: null,
    status: 403,
  };

  //SE RECUPERA EL TOKEN DEL ENCABEZADO DE LA PETICIÓN
  let token =
    request.headers["x-access-token"] || request.headers["authorization"];

  if (!token) {
    response.status(obj_respuesta.status).json(obj_respuesta);
  }

  if (token.startsWith("Bearer ")) {
    token = token.slice(7, token.length);
  }

  if (token) {
    //SE VERIFICA EL TOKEN CON LA CLAVE SECRETA
    jwt.verify(token, app.get("key"), (error: any, decoded: any) => {
      if (error) {
        obj_respuesta.mensaje = "El JWT NO es válido!!!";
        response.status(obj_respuesta.status).json(obj_respuesta);
      } else {
        obj_respuesta.exito = true;
        obj_respuesta.mensaje = "El JWT es valido";
        obj_respuesta.payload = decoded;
        obj_respuesta.status = 200;
        response.status(obj_respuesta.status).json(obj_respuesta);
      }
    });
  }
});

