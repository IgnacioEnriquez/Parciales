<?php

require_once "./clases/ProductoEnvasado.php";

use IgnacioEnriquez\Producto;
use IgnacioEnriquez\ProductoEnvasado;

try {

    $retorno = new stdClass();
    $retorno->exito = false;
    $retorno->mensaje = "";

    $producto_json = isset($_POST["producto_json"]) == true && empty($_POST["producto_json"]) == false ? (string)$_POST["producto_json"] : throw new Exception("El producto en formato JSON no fue enviado como parametro");

    $producto_obj = json_decode($producto_json);

    $id = isset($producto_obj->id) == true && empty($producto_obj->id) == false ? $producto_obj->id : throw new Exception("El producto no contiene ID o es 0");
    $codigoBarra = isset($producto_obj->codigoBarra) == true ? $producto_obj->codigoBarra : throw new Exception("El producto no contiene Codigo de barra");
    $precio = isset($producto_obj->precio) == true ? $producto_obj->precio : throw new Exception("El producto no contiene precio");
    $nombre = isset($producto_obj->nombre) == true && empty($producto_obj->nombre) == false ? $producto_obj->nombre : throw new Exception("El producto no contiene Nombre");
    $origen = isset($producto_obj->origen) == true && empty($producto_obj->origen) == false ? $producto_obj->origen : throw new Exception("El producto no contiene Origen");
    $foto = isset($_FILES["foto"]) == true ? (array) $_FILES["foto"] : throw new Exception("La foto no fue enviado como parametro");

    $pathFoto = ProductoEnvasado::ValidarFoto($foto, $nombre, $origen);

    $retornoPath_obj = json_decode($pathFoto);

    if ($retornoPath_obj->exito === true) {

        $productoEnvasadoAnterior = ProductoEnvasado::TraerUno($id);       

        if ($productoEnvasadoAnterior != false) {

            $productoEnvasadoNuevo = new ProductoEnvasado($nombre, $origen, $codigoBarra, $precio, $retornoPath_obj->mensaje, $id);

            if ($productoEnvasadoNuevo->Modificar()) {

                ProductoEnvasado::MoverArchivoModificado($productoEnvasadoAnterior -> pathFoto, $nombre, $origen);

                if ($productoEnvasadoNuevo->GuardarFoto($foto)) {

                    $retorno->exito = true;
                    $retorno->mensaje = "Se modifico correctamente el producto envasado con foto";
                } else {

                    throw new Exception("Se modifico el producto pero no se pudo subir la foto");
                }
            } else {
                throw new Exception("No se pudo modificar el producto envasado");
            }
        } else {

            throw new Exception("El producto envasado no existe en el sistema");
        }
    } else {

        throw new Exception($retornoPath_obj->mensaje);
    }
} catch (Exception $ex) {

    $retorno->mensaje = "ERROR : " . $ex->getMessage();
} finally {

    echo json_encode($retorno);
}
