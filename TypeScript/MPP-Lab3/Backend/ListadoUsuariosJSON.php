<?php

require_once("./clases/Usuario.php");
$pathArchivos = "./archivos/usuarios.json";

try 
{
    $retorno = new stdClass;
    $retorno -> exito = true;
    $retorno -> mensaje = "";

    $arrayUsuarios = Usuario::TraerTodosJSON($pathArchivos);
    $retorno -> mensaje = $jsonUsuarios = json_encode($arrayUsuarios);   
    
} catch (Exception $ex) 
{
    $retorno -> exito = false;
    $retorno -> mensaje =  "ERROR : " . $ex -> getMessage();    
}
finally
{
    echo json_encode($retorno);
}
