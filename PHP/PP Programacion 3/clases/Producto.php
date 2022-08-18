<?php

namespace IgnacioEnriquez;

use stdClass;
use Exception;

class Producto
{

    public string $nombre;
    public string $origen;

    public function __construct(string $nombre, string $origen)
    {
        $this->nombre = $nombre;
        $this->origen = $origen;
    }
    
    /**  
     *
     * Convierte los datos del Producto en formato JSON
     *     
     * @return string Datos del producto en formato JSON
     **/    
    public function ToJSON(): string
    {
        $retorno = new stdClass();
        $retorno->nombre = $this->nombre;
        $retorno->origen = $this->origen;

        return json_encode($retorno);
    }

    /**
     *
     * Guarda los datos del producto en un archivo.
     *
     * @param string $path Direccion del Archivo en donde se desea guardar los datos.
     * @return string Retorna si el guardado fue exitoso y el mensaje resultante en formato JSON.
     **/    
    public function GuardarEnJson(string $path): string
    {
        $retorno = new stdClass();
        $retorno->exito = true;
        $retorno->mensaje = "El producto fue guardado en el archivo correctamente";

        try {
            //ABRO EL ARCHIVO
            $ar = fopen($path, "a"); //A - append

            //ESCRIBO EN EL ARCHIVO
            $cant = fwrite($ar, $this->ToJSON() . ",\r\n");

            if ($cant <= 0) {
                throw new Exception("Ocurrio un error al escribir el archivo y no fue guardado el producto");
            }
        } catch (Exception $ex) {

            $retorno->exito = false;
            $retorno->mensaje = "GuardarEnArchivo : " . $ex->getMessage();
        } finally 
        {
            fclose($ar);
            return json_encode($retorno);
        }
    }

     /**
     *
     * Obtiene los Productos del archivo pasado por parametro
     *
     * @param string $path Direccion del Archivo en donde se desea recuperar los datos.
     * @return array Retorna un array de Productos, si el archivo esta vacio retorna un array vacio.
     **/ 
    public static function TraerJSON(string $path): array
    {
        $array_productos = array();
        $contenido = "";

        //ABRO EL ARCHIVO
        $ar = fopen($path, "r");

        //LEO LINEA X LINEA DEL ARCHIVO 
        while (!feof($ar)) {
            $contenido .= fgets($ar);
        }

        //CIERRO EL ARCHIVO
        fclose($ar);

        $array_contenido = explode(",\r\n", $contenido);

        for ($i = 0; $i < count($array_contenido); $i++) {
            if ($array_contenido[$i] != "") {
                $producto =  json_decode($array_contenido[$i]);
                $nombre = $producto->nombre;
                $origen = $producto->origen;

                $producto = new Producto($nombre, $origen);
                array_push($array_productos, $producto);
            }
        }

        return $array_productos;
    }

     /**
     *
     * Verifica si el producto existe en el archivo "Productos.JSON"
     *
     * @param Producto Producto que se desea verificar si existe
     * @return array Retorna un array de Productos, si el archivo esta vacio retorna un array vacio.
     **/ 
    public static function VerificarProductoJSON(Producto $producto): string
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "";

        $contadorExactIguales = 0;

        $maxNombresIguales = 0;
        $nombreMasRepetido = "";

        $string_nombres = "";
        $string_origenes = "";


        try {
            $array_productos = Producto::TraerJSON(__DIR__ . "/../archivos/productos.json");

            if ($producto != null) {
                if (count($array_productos) != 0) {
                    foreach ($array_productos as $productoArray) {
                        $string_nombres .= $productoArray->nombre;
                        $string_origenes .= $productoArray->origen;
                    }

                    foreach ($array_productos as $productoArray) {
                        if ($productoArray->nombre == $producto->nombre && $productoArray->origen == $producto->origen) {
                            $contadorExactIguales++;
                            $retorno->exito = true;
                        } else {
                            $cantIguales = substr_count($string_nombres, $productoArray->nombre);

                            if ($cantIguales > $maxNombresIguales) {
                                $nombreMasRepetido = $productoArray->nombre;
                                $maxNombresIguales = $cantIguales;
                            }
                        }
                    }

                    if ($retorno->exito == true) {
                        $retorno->mensaje = "El producto existe en el archivo y tiene una cantidad de {$contadorExactIguales} productos iguales";
                    } else {
                        $retorno->mensaje = "El producto no existe en el archivo y el producto mas popular es {$nombreMasRepetido} con una cantidad de {$maxNombresIguales}";
                    }
                } else {
                    $retorno->mensaje = "Se debe agregar minimo 1 producto para verificar si existe";
                }
            } else {
                throw new Exception("El objeto producto pasado es NULL");
            }
        } catch (Exception $ex) {
            $retorno->mensaje = "Error : " . $ex->getMessage();
        }

        return json_encode($retorno);
    }   
}
