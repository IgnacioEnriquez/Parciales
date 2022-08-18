<?php

require_once("./clases/ProductoEnvasado.php");

use IgnacioEnriquez\ProductoEnvasado;

try 
{  
    $retorno = new stdClass();
    $retorno -> exito = false;
    $retorno -> mensaje = "";

    $producto_json = isset($_POST["producto_json"]) == true && empty($_POST["producto_json"]) == false ? (string)$_POST["producto_json"] : throw new Exception("El producto en formato JSON no fue enviado como parametro");     

    $producto_obj = json_decode($producto_json);

    $id = isset($producto_obj -> id) == true && empty($producto_obj -> id) == false ? $producto_obj -> id : throw new Exception("El producto no contiene ID");
    $codigoBarra = isset($producto_obj -> codigoBarra) == true && empty($producto_obj -> codigoBarra) == false ? $producto_obj -> codigoBarra : throw new Exception("El producto no contiene codigo de barras");
    $nombre = isset($producto_obj -> nombre) == true && empty($producto_obj -> nombre) == false ? $producto_obj -> nombre : throw new Exception("El producto no contiene nombre");
    $origen = isset($producto_obj -> origen) == true && empty($producto_obj -> origen) == false ? $producto_obj -> origen : throw new Exception("El producto no contiene origen");
    $precio = isset($producto_obj -> precio) == true && empty($producto_obj -> precio) == false ? $producto_obj -> precio : throw new Exception("El producto no contiene precio");  

    $producto = new ProductoEnvasado($nombre,$origen,$codigoBarra,$precio,NULL,$id);

    if($producto -> Modificar())
    {
        $retorno -> exito = true;
        $retorno -> mensaje = "Se modifico correctamente el producto envasado";
    }
    else
    {
        throw new Exception("No se pudo Modificar el producto envasado");
    }          
    
} catch (Exception $ex) 
{
    $retorno -> mensaje = "ERROR : " . $ex -> getMessage();    
}
finally
{
    echo json_encode($retorno);
}