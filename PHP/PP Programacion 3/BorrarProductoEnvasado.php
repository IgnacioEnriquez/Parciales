<?php

require_once("./clases/ProductoEnvasado.php");

use IgnacioEnriquez\ProductoEnvasado;



if (isset($_POST["producto_json"])) 
{
    try {

        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "El producto en formato JSON no fue enviado como parametro";

        $producto_json = empty($_POST["producto_json"]) == false ? (string)$_POST["producto_json"] : throw new Exception("El producto en formato JSON no fue enviado como parametro");

        $producto_obj = json_decode($producto_json);

        $id = isset($producto_obj->id) == true ? $producto_obj->id : throw new Exception("El producto no contiene ID");
        $precio = isset($producto_obj->precio) == true ? $producto_obj->precio : throw new Exception("El producto no contiene el Precio");
        $codigoBarra = isset($producto_obj->codigoBarra) == true ? $producto_obj->codigoBarra : throw new Exception("El producto no contiene el Codigo de Barra");
        $nombre = isset($producto_obj->nombre) == true && empty($producto_obj->nombre) == false ? $producto_obj->nombre : throw new Exception("El producto no contiene Nombre");
        $origen = isset($producto_obj->origen) == true && empty($producto_obj->origen) == false ? $producto_obj->origen : throw new Exception("El producto no contiene Origen");
        $pathFoto = isset($producto_obj->pathFoto) == true && empty($producto_obj->pathFoto) == false ? $producto_obj->pathFoto : throw new Exception("El producto no contiene Nombre");

        $productoEnvasado = new ProductoEnvasado($nombre, $origen, $codigoBarra, $precio, $pathFoto, $id);

        if (ProductoEnvasado::Eliminar($productoEnvasado->id) == true) 
        {
            $productoEnvasado->GuardarEnArchivo();
            $retorno->exito = true;
            $retorno->mensaje = "El producto fue eliminado correctamente del sistema";

        } else 
        {
            throw new Exception("No existe ningun producto envasado con el ID pasado");
        }

    } catch (Exception $ex)
     {
        $retorno->mensaje = "ERROR : " . $ex->getMessage();
    } 
    finally 
    {
        echo json_encode($retorno);
    }

} else if (count($_GET) === 0) 
{
    $arrayProductos = ProductoEnvasado::TraerProductosBorrados("./archivos/productos_envasados_borrados.txt");    

    $tablaHTML = '<html>
        <head><title>Listado de Productos Envasados Eliminados</title></head>
        <body>
        
        <h1>Listado de Productos Envasados Eliminados</h1>
        
        <table>
        <tr>
          <th style="padding:0 15px 0 15px;"><strong>ID</strong></th>      
          <th style="padding:0 15px 0 15px;"><strong>NOMBRE </strong></th>
          <th style="padding:0 15px 0 15px;"><strong>ORIGEN </strong></th>
          <th style="padding:0 15px 0 15px;"><strong>CODIGOBARRA </strong></th>
          <th style="padding:0 15px 0 15px;"><strong>PRECIO </strong></th>
          <th style="padding:0 15px 0 15px;"><strong>FOTO </strong></th>
        </tr>
        
        ';

    foreach ($arrayProductos as $Producto) {
        $stringProducto = '<tr>
            <td style="padding:0 15px 0 15px;"><strong>' . $Producto->id . '</strong></td>
            <td style="padding:0 15px 0 15px;"><strong>' . $Producto->nombre . '</strong></td>
            <td style="padding:0 15px 0 15px;"><strong>' . $Producto->origen . '</strong></td>
            <td style="padding:0 15px 0 15px;"><strong>' . $Producto->codigoBarra . '</strong></td>
            <td style="padding:0 15px 0 15px;"><strong>' . $Producto->precio . '</strong></td> 
            <td style="padding:0 15px 0 15px;"><img src="' . $Producto->pathFoto . '" width="100" height="100"></td>
            </tr>
            
            ';

        $tablaHTML .= $stringProducto;
    }

    $tablaHTML .= "</table>
    
        </body>
        </html>";

    echo $tablaHTML;
}
