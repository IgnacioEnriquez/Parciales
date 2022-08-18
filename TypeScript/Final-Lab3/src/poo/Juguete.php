<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class Juguete
{
    public string $marca;
    public int $precio;
    public string $foto;

 

    public function AltaJuguetes(Request $request, Response $response, array $args): Response
    {
        //Retorno Respuesta
        $respuesta = $response;

        //Armo el obj para devolver Json
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "";
        $retorno->status = 418;

        //Obtengo el json pasado por Post
        $arrayParametros = $request->getParsedBody();
        $json_juguete = $arrayParametros["juguete_json"];
        $obj_juguete = json_decode($json_juguete);

        //Obtengo los parametros del Json para crear el juguete
        $marca = $obj_juguete->marca;
        $precio = $obj_juguete->precio;
       
        //Obtengo el path de la foto para pasarle al archivo
        $pathFoto = $_FILES["foto"];            
            
        //Valido que el archivo cumpla las especificaciones                     
        $json_archivo = Juguete::ValidarArchivoFoto($pathFoto);
            
        //Convierto el retorno Json en obj                       
        $obj_archivo = json_decode($json_archivo);
            
        
        if ($obj_archivo->valido == true)                   
        {               
                //Creo el juguete nuevo que se va a agregar a la base con la foto "Null" por si falla la carga
                $juguete_obj = new Juguete();
                $juguete_obj->marca = $marca;
                $juguete_obj->precio = $precio;            
                $juguete_obj->foto = "Null";

                $json_agregar = $juguete_obj->AgregarJuguete($pathFoto);
                $obj_agregar = json_decode($json_agregar);

                //Intengo agregar al usuario a la BD
                if ($obj_agregar -> exito === true) 
                {
                    $pathDestino = $obj_agregar -> mensaje;

                    if (Juguete::GuardarArchivoTemporal($pathDestino)) 
                    {
                        $retorno->exito = true;
                        $retorno->status = 200;
                        $retorno->mensaje = "Se pudo agregar al juguete correctamente.";
                    } 
                    else 
                    {
                        $retorno->mensaje = "Se pudo agregar al jugete, pero no se pudo cargar la foto";
                    }
                } 
                else 
                {
                    $retorno->mensaje = $obj_agregar -> mensaje;
                }
            
            } 
            else 
            {
                //Retorno el error en mensaje si no fue valido
                $retorno->mensaje = $obj_archivo->mensaje;
            }             

        $respuesta = $respuesta->withStatus($retorno->status);
        $respuesta->getBody()->write(json_encode($retorno));

        return $respuesta;
    }    

    public function ListadoJuguetes(Request $request, Response $response, array $args): Response
    {
         //Retorno Respuesta
         $respuesta = $response;

         //Armo el obj para devolver Json
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "";
        $retorno-> dato = "";
        $retorno->status = 424;

        $juguetes_json = Juguete::ObtenerTodos();
        $juguetes_obj = json_decode($juguetes_json);       

        if($juguetes_obj -> exito = true)
        {
            $retorno -> exito = true;
            $retorno -> mensaje = "Se pudo recuperar los juguetes correctamente";
             $retorno -> dato = $juguetes_obj -> mensaje;
            $retorno -> status = 200;                     
        }
        else
        {
            $retorno -> mensaje = $juguetes_obj -> mensaje;
        }

        $respuesta = $respuesta->withStatus($retorno->status);
        $respuesta->getBody()->write(json_encode($retorno));

        return $respuesta;
        
    }    

    private static function ValidarArchivoFoto(array $foto): string
    {
        $retorno = new stdClass();
        $retorno->valido = false;
        $retorno->mensaje = "";

        try {
            if ($foto != NULL) 
            {                               
                $foto_nombre = $foto["name"];
                $extension = pathinfo($foto_nombre, PATHINFO_EXTENSION);                            

                //VERIFICO EL TAMAÑO MAXIMO QUE PERMITO SUBIR

                if ($_FILES["foto"]["size"] > 2000000) {
                    throw new Exception("El archivo es demasiado grande, inserte uno mas chico");
                }

                //OBTIENE EL TAMAÑO DE UNA IMAGEN, SI EL ARCHIVO NO ES UNA		
                //IMAGEN, RETORNA FALSE       

                $esImagen = getimagesize($_FILES["foto"]["tmp_name"]);

                if ($esImagen != false) {
                    if (
                        $extension != "jpg" && $extension != "jpeg" && $extension != "gif"
                        && $extension != "png"
                    ) {
                        throw new Exception("Solo son permitidas imagenes con extension JPG, JPEG, PNG o GIF.");
                    }
                } else {
                    throw new Exception("El archivo no es una imagen,POR FAVOR inserte una imagen");
                }

                $retorno->valido = true;               
            } 
            else 
            {
                throw new Exception("Es necesario cargar una foto para poder realizar el ALTA");
            }
        } catch (Exception $ex) {
            $retorno->mensaje = "Error : " . $ex->getMessage();
        }

        return json_encode($retorno);
    }

    private function AgregarJuguete($pathFoto): string
    {
        $retorno = new stdClass();
        $retorno -> exito = false;
        $retorno -> mensaje = "";

        try 
        {
            $pdo = new PDO('mysql:host=localhost;dbname=jugueteria_bd;charset=utf8', 'root', '');
            $consultaAgregar = $pdo->prepare("INSERT INTO juguetes (marca, precio, path_foto) "
                . "VALUES(:marca, :precio, :foto)");

            $consultaAgregar->bindValue(":marca", $this->marca, PDO::PARAM_STR);
            $consultaAgregar->bindValue(":precio", $this->precio, PDO::PARAM_INT);
            $consultaAgregar->bindValue(":foto", $this->foto, PDO::PARAM_STR);
             
            $consultaAgregar->execute();

            //Obtengo el id que tendra el path  
            $id = Juguete::ObtenerUltimoId() > -1 ? Juguete::ObtenerUltimoId() : throw new Exception("Ocurrio un error al obtener el ID");

            $foto_nombre = $pathFoto["name"];                
            $extension = pathinfo($foto_nombre, PATHINFO_EXTENSION);                
            $nombreArchivo = $this ->marca . "." . $extension;                
            $pathDestino = "../src/fotos/" . $nombreArchivo;

            $consultaActualizarPath = $pdo->prepare("UPDATE juguetes SET path_Foto = :foto WHERE id = :id");

            $consultaActualizarPath->bindValue(":foto",$pathDestino, PDO::PARAM_STR);
            $consultaActualizarPath->bindValue(":id", $id, PDO::PARAM_STR);

            $consultaActualizarPath -> execute();

            $retorno -> exito = true;
            $retorno -> mensaje = $pathDestino;             

        } catch (Exception $ex) 
        {                     
            $retorno -> mensaje = "Error : " . $ex -> getMessage();
        }

        return json_encode($retorno);
    } 

    public function BorrarJuguetePorID(Request $request, Response $response, array $args): Response
    {
        //Retorno Respuesta
        $respuesta = $response;

        $retorno = new stdClass();  
        $retorno->status = 418;            
        $retorno->exito = false;
        $retorno->mensaje = "";

        $token = $request->getHeader("token")[0];
   
        $id_json = $args["id_juguete"];        

        try 
        {  
            //Si el JWT no es valido lanza una exception
            $jwtDecodificado = Usuario::DecodificarJWT($token);

            $json_eliminacion = Juguete::EliminarJugetePorID($id_json);
                
            $obj_eliminacion = json_decode($json_eliminacion);

            if($obj_eliminacion -> exito == true)
            {                                
                $retorno -> status = 200;                    
                $retorno -> exito = true;                    
                $retorno -> mensaje = $obj_eliminacion -> mensaje;                
            }                
            else               
            {                       
                throw new Exception("El usuario no pudo eliminar el juguete. ERROR : " . $obj_eliminacion -> mensaje);                                 
            }
            
            
        } catch (Exception $ex) 
        {            
            $retorno -> mensaje = $ex -> getMessage();
            
        }   


        $respuesta = $respuesta->withStatus($retorno->status);
        $respuesta->getBody()->write(json_encode($retorno));

        return $respuesta;
    }

    public function ModificarJuguetePorID(Request $request, Response $response, array $args): Response
    {
        //Retorno Respuesta
        $respuesta = $response;

        $retorno = new stdClass();  
        $retorno->status = 418;            
        $retorno->exito = false;
        $retorno->mensaje = "";

        try 
        {

        $token = $request->getHeader("token")[0];

        //Si el JWT no es valido lanza una exception
        $jwtDecodificado = Usuario::DecodificarJWT($token);

        //Obtengo el json pasado por Post
        $arrayParametros = $request->getParsedBody();
        $json_juguete = $arrayParametros["juguete"];
        $obj_juguete = json_decode($json_juguete);

        $marca = $obj_juguete->marca;
        $precio = $obj_juguete->precio;
        $id = $obj_juguete -> id_juguete;

        //Obtengo el path de la foto para pasarle al archivo        
        $pathFoto = $_FILES["foto"];    
        
        //Valido que el archivo cumpla las especificaciones                     
        $json_archivo = Juguete::ValidarArchivoFoto($pathFoto);

        //Convierto el retorno Json en obj                       
        $obj_archivo = json_decode($json_archivo);

        if($obj_archivo -> valido == true)
        {
            //Creo el juguete nuevo que se va a agregar a la base con la foto "Null" por si falla la carga
            $juguete_obj = new Juguete();
            $juguete_obj->marca = $marca;
            $juguete_obj->precio = $precio;            
            $juguete_obj->foto = "Null";

            $json_modificar = $juguete_obj -> ModificarJugete($id,$pathFoto);
            $obj_modificar = json_decode($json_modificar);

            if($obj_modificar-> exito == true)            
            {
                $pathDestino = $obj_modificar -> mensaje;

                    if (Juguete::GuardarArchivoTemporal($pathDestino)) 
                    {
                        $retorno->exito = true;
                        $retorno->status = 200;
                        $retorno->mensaje = "Se pudo Modificar al juguete correctamente.";
                    } 
                    else 
                    {
                        $retorno->mensaje = "Se pudo Modificar al jugete, pero no se pudo cargar la foto";
                    }               
            }
            else
            {
                throw new Exception("El usuario no pudo Modificar el juguete. ERROR : " . $obj_modificar -> mensaje); 
            }


        }
        else
        {
            //Retorno el error en mensaje si no fue valido
            throw new Exception("El usuario no pudo eliminar el juguete. ERROR : " . $obj_archivo->mensaje); 
        }
            
        } catch (Exception $ex) 
        {
            $retorno -> mensaje = $ex -> getMessage();            
        }

        $respuesta = $respuesta->withStatus($retorno->status);
        $respuesta->getBody()->write(json_encode($retorno));

        return $respuesta;
    }
    

    public static function ObtenerTodos() : string
    {
        $retorno = new stdClass();
        $retorno -> exito = false;
        $retorno -> mensaje = "";

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=jugueteria_bd;charset=utf8', 'root', '');

            $consultaListado = $pdo->prepare("SELECT * FROM juguetes");          

            $consultaListado->execute();

            if($consultaListado -> rowCount() > 0)
            {
                $arrayJuguetes = $consultaListado-> fetchAll(PDO::FETCH_ASSOC);

                $retorno -> mensaje = json_encode($arrayJuguetes);
                $retorno -> exito = true;
            }
            else
            {
                $retorno -> mensaje = "No hay Juguetes para mostrar!.";
            }          

             
        } catch (Exception $ex) 
        {
            $retorno -> mensaje = "ERROR : " . $ex ->getMessage();
        }
        

        return json_encode($retorno);

    }


    private static function ObtenerUltimoId(): int
    {
        try {

            $pdo = new PDO('mysql:host=localhost;dbname=jugueteria_bd;charset=utf8', 'root', '');          
            $consultaObtenerId = $pdo->prepare("SELECT MAX(id) AS id FROM juguetes");
            $consultaObtenerId->execute();
            $arrayObtenido = $consultaObtenerId->fetch(PDO::FETCH_ASSOC);
            $retorno = $arrayObtenido["id"];

        } catch (Exception $ex) 
        {
            $retorno = -1;            
            //No realizo ninguna accion,solo paso null       
        }

        return $retorno;
    }

    private static function GuardarArchivoTemporal($pathDestino): bool
    {
        try 
        {
            if (isset($_FILES["foto"])) {
                $retorno =  move_uploaded_file($_FILES["foto"]["tmp_name"], $pathDestino);
            }
        } catch (Exception $ex) 
        {
            $retorno = false;
        }

        return $retorno;
    }   

    private static function EliminarJugetePorID(int $id) : string
    {
        $retorno = new stdClass();
        $retorno -> exito = false;
        $retorno -> mensaje = "";

        try 
        {
            $pdo = new PDO('mysql:host=localhost;dbname=jugueteria_bd;charset=utf8', 'root', '');
            $consultaEliminar = $pdo->prepare("DELETE FROM juguetes WHERE id = :id");

            $consultaEliminar -> bindValue(":id",$id,PDO::PARAM_INT);

            $consultaEliminar -> execute();

            if($consultaEliminar -> rowCount() > 0)
            {                
                $retorno -> exito = true;            
                $retorno -> mensaje = "Juguete eliminado con exito";
            }
            else
            {
                $retorno -> mensaje = "No existia ningun juguete con el ID pasado";
            }            
            
        } catch (Exception $ex) 
        {
            $retorno -> mensaje = $ex -> getMessage();            
        }

        return json_encode($retorno);

    }

    private function ModificarJugete(int $id, array $pathFoto) : string
    {
        $retorno = new stdClass();
        $retorno -> exito = false;
        $retorno -> mensaje = "";

        try 
        {
            $pdo = new PDO('mysql:host=localhost;dbname=jugueteria_bd;charset=utf8', 'root', '');
            $consultaModificar = $pdo->prepare("UPDATE juguetes SET marca=:marca, precio =:precio WHERE id = :id");

            $consultaModificar -> bindValue(":id",$id,PDO::PARAM_INT);
            $consultaModificar -> bindValue(":marca",$this -> marca,PDO::PARAM_STR);
            $consultaModificar -> bindValue(":precio",$this -> precio,PDO::PARAM_INT);

            $consultaModificar -> execute();

            if($consultaModificar -> rowCount() > 0)
            {                
                $foto_nombre = $pathFoto["name"];                
                $extension = pathinfo($foto_nombre, PATHINFO_EXTENSION);                
                $nombreArchivo = $this ->marca . "_modificacion" . "." . $extension;                
                $pathDestino = "../src/fotos/" . $nombreArchivo;
    
                $consultaActualizarPath = $pdo->prepare("UPDATE juguetes SET path_Foto = :foto WHERE id = :id");
    
                $consultaActualizarPath->bindValue(":foto",$pathDestino, PDO::PARAM_STR);
                $consultaActualizarPath->bindValue(":id", $id, PDO::PARAM_INT);
    
                $consultaActualizarPath -> execute();
    
                $retorno -> exito = true;
                $retorno -> mensaje = $pathDestino;                         
            }
            else
            {
                $retorno -> mensaje = "No existia ningun juguete con el ID pasado o no se modifico ningun dato";
            }            
            
        } catch (Exception $ex) 
        {
            $retorno -> mensaje = $ex -> getMessage();            
        }

        return json_encode($retorno);

    }

}