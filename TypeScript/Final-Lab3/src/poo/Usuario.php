<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Firebase\JWT\JWT;


//require_once __DIR__ . './Autentificadora.php';

class Usuario
{
    public string $correo;
    public string $clave;
    public string $nombre;
    public string $apellido;
    public string $perfil;
    public string $foto;

    private static string $clave_secreta = "ApiParcial123";
    private static array $codificacion = ['HS256'];

    //-------------------------------------------------Funciones DE la API REST-----------------------------------------------------         
        
        public function ListadoUsuarios(Request $request, Response $response, array $args): Response
        {
             //Retorno Respuesta
             $respuesta = $response;
    
             //Armo el obj para devolver Json
            $retorno = new stdClass();
            $retorno->exito = false;
            $retorno->mensaje = "";
            $retorno->dato = null;
            $retorno->status = 424;
    
            $usuarios_json = Usuario::ObtenerTodos();
            $usuarios_obj = json_decode($usuarios_json);       
    
            if($usuarios_obj -> exito === true)
            {
                $retorno -> exito = true;
                $retorno -> mensaje = "Se pudo recuperar los usuarios correctamente";
                $retorno -> dato = $usuarios_obj -> mensaje;
                $retorno -> status = 200;                     
            }
            else
            {
                $retorno -> mensaje = $usuarios_obj -> mensaje;
            }
    
            $respuesta = $respuesta->withStatus($retorno->status);
            $respuesta->getBody()->write(json_encode($retorno));
    
            return $respuesta;            
        }

        public function IniciarSesion(Request $request, Response $response, array $args): Response
    {
        $respuesta = $response;
        $retorno = new stdClass();  
        $retorno->status = 403;            
        $retorno->jwt = null;

        try {

            $arrayDeParametros = $request->getParsedBody();

            $usuario_confirm_obj = json_decode($arrayDeParametros["user"]); 
            $Json_UsuarioValidado = Usuario::ValidarExistenciaUsuario($usuario_confirm_obj);                     
            $Obj_UsuarioValidado = json_decode($Json_UsuarioValidado);

            if($Obj_UsuarioValidado -> exito === true)
            {                      
                $usuario_bd = $Obj_UsuarioValidado -> datos;
                $usuario_bd -> clave = "NULL";

                $retorno->status = 200;
                $retorno->jwt =  Usuario::CrearJWT($usuario_bd);
                $retorno -> exito = true;                        
            }
            else
            {
                throw new Exception("El usuario no existe en la base de datos");                                       
            }

        } catch (Exception $ex) 
        {                 
            $retorno-> exito = false;         
        }

        $respuesta = $respuesta->withStatus($retorno -> status);
        $respuesta->getBody()->write(json_encode($retorno));
        $respuesta->withHeader('Content-Type', 'application/json');


        return $respuesta;
    }

    public function VerificarSesion(Request $request, Response $response, array $args) : Response
    {
        
        $token = $request->getHeader("token")[0];

        $retornoJson = Usuario::ObtenerPayload($token);

        $response->getBody()->write($retornoJson);

        return $response;
    }


        //-------------------------------------------------Funciones Para los metodos de la API REST-----------------------------------------------------//
        public static function ObtenerTodos() : string
        {
            $retorno = new stdClass();
            $retorno -> exito = false;
            $retorno -> mensaje = "";
    
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=jugueteria_bd;charset=utf8', 'root', '');
    
                $consultaListado = $pdo->prepare("SELECT * FROM usuarios");          
    
                $consultaListado->execute();
    
                if($consultaListado -> rowCount() > 0)
                {
                    $arrayUsuarios = $consultaListado-> fetchAll(PDO::FETCH_ASSOC);
    
                    $retorno -> mensaje = json_encode($arrayUsuarios);
                    $retorno -> exito = true;
                }
                else
                {
                    $retorno -> mensaje = "No hay usuarios para mostrar!.";
                }          
    
                 
            } catch (Exception $ex) 
            {
                $retorno -> mensaje = "ERROR : " . $ex ->getMessage();
            }
            
    
            return json_encode($retorno);
    
    
        }

        public static function ValidarExistenciaUsuario(mixed $obj_usuario) : string
    {
        $retorno = new stdClass();
        $retorno -> datos = null;
        $retorno -> exito = false;

        try 
        {
            $pdo = new PDO('mysql:host=localhost;dbname=jugueteria_bd;charset=utf8', "root", "");
            
            $consulta = $pdo->prepare('SELECT * FROM usuarios WHERE correo = :correo AND clave = :clave');
               
            $consulta->bindValue(":correo", $obj_usuario->correo, PDO::PARAM_STR);            
            $consulta->bindValue(":clave", $obj_usuario->clave, PDO::PARAM_STR);
    
            $consulta->execute();
    
            if($consulta->rowCount() > 0)
            {
                $usuario_bd = $consulta -> fetch(PDO::FETCH_OBJ);
                $retorno -> datos = $usuario_bd;
                $retorno -> exito = true;
            }

        } catch (Exception $ex) 
        {
            $retorno -> exito = false;      
        }       

        return json_encode($retorno);
    }
    
    private static function CrearJWT(mixed $usuario,int $exp = (60*2)) : string
    {
        $time = time();

        $token = array(
            'usuario' => $usuario,
            'alumno' => "Nombre :" . $usuario -> nombre . " Apellido : " . $usuario -> apellido,
            'parcial' => "Segundo Parcial Programacion 3",         
            'exp' => $exp + $time,
        );

        return JWT::encode($token,self::$clave_secreta);        
    }

    public static function DecodificarJWT(string $token)
    {
        $retornoObj = null;

        try 
        {
            $retornoObj = JWT::decode
            (
                $token,
                self::$clave_secreta,
                self::$codificacion
            );
            
        } catch (Exception $ex) 
        {
            throw $ex;            
        }  
        
        return $retornoObj;
    }

    private static function ObtenerPayload($token)
    {
        $retornoObj = new stdClass();
        $retornoObj->exito = false;
        $retornoObj->payload = null;

        try 
        {
            $retornoObj->payload = Usuario::DecodificarJWT($token);
            $retornoObj->exito = true;
            
        } catch (Exception $ex) 
        {
            $retornoObj->mensaje = $ex->getMessage();
        }

        return json_encode($retornoObj);
    }


    
}
