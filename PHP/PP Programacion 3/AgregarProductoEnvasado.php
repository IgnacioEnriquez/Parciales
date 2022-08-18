<?php

require_once "./clases/ProductoEnvasado.php";

use IgnacioEnriquez\ProductoEnvasado;

try {

    $retorno = new stdClass();
    $retorno->exito = false;
    $retorno->mensaje = "";

    $codigoBarra = isset($_POST["codigoBarra"]) == true ? (int) $_POST["codigoBarra"] : throw new Exception("El Codigo de barras no fue enviado como parametro");
    $precio = isset($_POST["precio"]) == true ? (int) $_POST["precio"] : throw new Exception("El Precio no fue enviado como parametro");
    $nombre = isset($_POST["nombre"]) == true && empty($_POST["nombre"]) == false ? (string) $_POST["nombre"] : throw new Exception("El Nombre no fue enviado como parametro");
    $origen = isset($_POST["origen"]) == true && empty($_POST["origen"]) == false ? (string) $_POST["origen"] : throw new Exception("El Origen no fue enviado como parametro");
    $foto = isset($_FILES["foto"]) == true ? (array) $_FILES["foto"] : throw new Exception("La foto no fue enviado como parametro");

    $pathFoto = ProductoEnvasado::ValidarFoto($foto, $nombre, $origen);

    $retornoPath_obj = json_decode($pathFoto);

    if ($retornoPath_obj->exito === true) {

        $productoEnvasado = new ProductoEnvasado($nombre, $origen, $codigoBarra, $precio, $retornoPath_obj->mensaje);
        $arrayProductos = ProductoEnvasado::Traer();

        $retorno_existencia = $productoEnvasado->Existe($arrayProductos);

        if ($retorno_existencia == false) {
            if ($productoEnvasado->Agregar()) 
            {
                if ($productoEnvasado->GuardarFoto($foto)) {
                    $retorno->exito = true;
                    $retorno->mensaje = "Se agrego correctamente el producto envasado con foto";
                } else {
                    throw new Exception("Se agrego el producto pero no se pudo subir la foto");
                }
            } else {
                throw new Exception("No se pudo agregar el producto envasado");
            }
        } else {
            throw new Exception("El producto envasado ya existe en el sistema");
        }
    } else {

        throw new Exception($retornoPath_obj->mensaje);
    }
} catch (Exception $ex) {
    $retorno->mensaje = "ERROR : " . $ex->getMessage();
} finally {
    echo json_encode($retorno);
}
