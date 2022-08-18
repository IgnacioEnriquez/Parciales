<?php


require_once("./clases/Ciudad.php");

use Ignacio\Enriquez\Ciudad;

try 
{  
    $retorno = new stdClass();
    $retorno -> exito = false;
    $retorno -> mensaje = "";

    $nombre = isset($_POST["nombre"]) == true && empty($_POST["nombre"]) == false ? (string)$_POST["nombre"] : throw new Exception("El nombre no fue enviado como parametro"); 
    $poblacion = isset($_POST["poblacion"]) == true ? (int)$_POST["poblacion"] : throw new Exception("La poblacion no fue enviado como parametro"); 
    $pais = isset($_POST["pais"]) == true  && empty($_POST["pais"]) == false ? (string)$_POST["pais"] : throw new Exception("El pais no fue enviada como parametro");   

    $ciudad = new Ciudad($nombre,$pais,$poblacion);

    if($ciudad -> Agregar())
    {
        $retorno -> exito = true;
        $retorno -> mensaje = "Se agrego correctamente la ciudad sin foto";
    }
    else
    {
        throw new Exception("No se pudo agregar la ciudad");
    }       
    
} catch (Exception $ex) 
{
    $retorno -> mensaje = "ERROR : " . $ex -> getMessage();    
}
finally
{
    echo json_encode($retorno);
}