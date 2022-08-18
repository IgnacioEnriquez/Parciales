<?php

use Ignacio\Enriquez\Ciudadano;

require_once("./clases/Ciudadano.php");
$pathArchivos = "./archivos/ciudadanos.json";


try 
{
    $array_ciudadanos = Ciudadano::TraerTodos($pathArchivos);
    $json_ciudadanos = json_encode($array_ciudadanos);

    echo $json_ciudadanos;   
    
} catch (Exception $ex) 
{
    echo "ERROR : " . $ex -> getMessage();    
}
