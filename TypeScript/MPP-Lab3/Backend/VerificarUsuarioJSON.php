<?php

require_once("./clases/Usuario.php");
$pathArchivos = "./archivos/usuarios.json";

try 
{
    $retorno = new stdClass();
    $retorno->exito = false;
    $retorno->mensaje = "La persona no existe en el JSON";

    $usuario_json = isset($_POST["usuario_json"]) == true ? (string)$_POST["usuario_json"] : throw new Exception("El usuario no fue enviado como parametro");

    $usuario_obj = json_decode($usuario_json);
    
    $correo = isset($usuario_obj -> correo) === true && empty($usuario_obj -> correo) === false ?  $usuario_obj -> correo : throw new Exception("El usuario no posee el parametro correo");
    $clave = isset($usuario_obj -> clave) === true && empty($usuario_obj -> clave) === false ?  $usuario_obj -> clave : throw new Exception("El usuario no posee el parametro clave"); 

    $usuarioEncontrado = Usuario::TraerUno($usuario_json);    

    if (isset($usuarioEncontrado)) 
    {
        $retorno->exito = true;
        $retorno->mensaje = "La persona existe en el JSON";
    }

    echo json_encode($retorno);

} catch (Exception $ex) 
{
    $retorno->mensaje = "Error : " . $ex -> getMessage();
    echo json_encode($retorno);
}
