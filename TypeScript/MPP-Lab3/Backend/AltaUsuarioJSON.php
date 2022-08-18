<?php

require_once("./clases/Usuario.php");
$pathArchivos = "./archivos/usuarios.json";

try 
{
    $retorno = new stdClass;
    $retorno -> exito = false;
    $retorno -> mensaje = "";

    $correo = isset($_POST["correo"]) == true && empty($_POST["correo"]) == false ? (string)$_POST["correo"] : throw new Exception("El correo no fue enviado como parametro"); 
    $clave = isset($_POST["clave"]) == true  && empty($_POST["clave"]) == false ? (string)$_POST["clave"] : throw new Exception("La clave no fue enviada como parametro"); 
    $nombre = isset($_POST["nombre"]) == true && empty($_POST["nombre"]) == false ? (string)$_POST["nombre"] : throw new Exception("El nombre no fue enviado como parametro"); 

    $usuario = new Usuario($nombre,$correo,$clave);

    $retornoJson = $usuario -> GuardarEnArchivo($pathArchivos);

    $objRetorno = json_decode($retornoJson);

    if($objRetorno -> exito = true)
    {
        $retorno -> exito = true;
        $retorno -> mensaje = $objRetorno -> mensaje;
    }
    else
    {
        throw new Exception($objRetorno -> mensaje);
    }
    
} catch (Exception $ex) 
{    
    $retorno -> mensaje = "ERROR : " . $ex -> getMessage();    
}
finally
{
    echo json_encode($retorno);
}
