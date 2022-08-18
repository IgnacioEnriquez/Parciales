<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as ResponseMW;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Firebase\JWT\JWT;

class MW
{
    public static function ValidarVacioCorreoYClave(Request $request, RequestHandler $handler): ResponseMW
    {
        $respuesta = new ResponseMW();

        $retorno = new stdClass(); 
        
        try 
        {                 
            $arrayDeParametros = $request->getParsedBody();

            $user_json  = $arrayDeParametros["user"];
            $user_obj = json_decode($user_json);

            $correo = $user_obj -> correo != "" && $user_obj -> correo != " " ?  (string)$user_obj -> correo : throw new Exception("El parametro correo esta vacio");
            $clave = $user_obj -> clave != "" && $user_obj -> clave != " "  ? (string)$user_obj -> clave : throw new Exception("El parametro clave esta vacio");

            $contenidoApi = $handler -> handle($request);
            $retorno -> status = $contenidoApi ->getStatusCode();   
            $retorno -> resultado = (string)$contenidoApi -> getBody(); 
            $respuesta -> getBody() -> write($retorno -> resultado);

            
        } catch (Exception $ex) 
        {
            $retorno -> status = 409;
            $retorno -> mensaje = $ex -> getMessage();        
            $respuesta -> getBody() -> write(json_encode($retorno));
            
        }               

        $respuesta = $respuesta -> withStatus($retorno -> status);

        return $respuesta; 
    }

    public function VerificarCorreoYClaveBD(Request $request, RequestHandler $handler): ResponseMW
    {
        $respuesta = new ResponseMW();

        $retorno = new stdClass(); 

        try 
        {
            $arrayDeParametros = $request->getParsedBody();

            $user_json  = $arrayDeParametros["user"];
            $user_obj = json_decode($user_json);

            $json_validacionUsuario = Usuario::ValidarExistenciaUsuario($user_obj);
            $obj_validacionUsuario = json_decode($json_validacionUsuario);

            if($obj_validacionUsuario -> exito == true )
            {
                $contenidoApi = $handler -> handle($request);
                $retorno -> status = $contenidoApi ->getStatusCode();  
    
                $retorno -> resultado = (string)$contenidoApi -> getBody(); 
                $respuesta -> getBody() -> write($retorno -> resultado); 
            }
            else
            {
                throw new Exception("El usuario no se encuentra en la base de datos.");
            }                      

        } catch (Exception $ex) 
        {
            $retorno -> status = 403;
            $retorno -> mensaje = $ex -> getMessage();
            $respuesta -> getBody() -> write(json_encode($retorno));                      
        }        

        $respuesta = $respuesta -> withStatus($retorno -> status);    
        return $respuesta; 
    }

    public function VerificarTokenValido(Request $request, RequestHandler $handler): ResponseMW
    {
        $respuesta = new ResponseMW();

        $retorno = new stdClass(); 

        try 
        {
            $token = $request->getHeader("token")[0];

            $json_resultado = MW::VerificarJWT($token);
            $obj_resultado = json_decode($json_resultado);

            if($obj_resultado -> verificado == true)
            {
                $contenidoApi = $handler -> handle($request);
                $retorno -> status = $contenidoApi ->getStatusCode();  
    
                $retorno -> resultado = (string)$contenidoApi -> getBody(); 
                $respuesta -> getBody() -> write($retorno -> resultado); 
            }
            else
            {
                throw new Exception($obj_resultado -> mensaje);
            }
           
            
        } catch (Exception $ex) 
        {
            $retorno -> status = 403;
            $retorno -> mensaje = $ex -> getMessage();
            $respuesta -> getBody() -> write(json_encode($retorno));                   
        }

        $respuesta = $respuesta -> withStatus($retorno -> status);    
        return $respuesta; 
    }

    private static function verificarJWT(string $token) : string
    {
        $datos = new stdClass();
        $datos->verificado = FALSE;
        $datos->mensaje = "";
        try 
        {
            if( ! isset($token))
            {
               $datos->mensaje = "El token se encuentra vacío!";
            }
            else
            {          
                //Lanza exception si tuvo algun problema
                $decode = Usuario::DecodificarJWT($token);                

                $datos->verificado = TRUE;                                                
            }          
        } 
        catch (Exception $e) 
        {
            $datos->mensaje = "Token inválido! - " . $e->getMessage();
        }
    
        return json_encode($datos);
    }








}