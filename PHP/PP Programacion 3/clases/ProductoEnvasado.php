<?php

namespace IgnacioEnriquez;

require_once(__DIR__ . "./Producto.php");
require_once(__DIR__ . "./IParte1.php");
require_once(__DIR__ . "./IParte2.php");
require_once(__DIR__ . "./IParte3.php");
require_once(__DIR__ . "./IParte4.php");

use stdClass;
use Exception;
use PDO;
use PDOException;



class ProductoEnvasado extends Producto implements IParte1, IParte2, IParte3, IParte4
{

    public int $id;
    public int $codigoBarra;
    public int $precio;
    public string | null $pathFoto;

    public function __construct(string $nombre = "No Asignado", string $origen = "No Asignado", int $codigoBarra = 0, int $precio = -1, string $pathFoto = NULL, int $id = 0,)
    {
        parent::__construct($nombre, $origen);
        $this->id = $id;
        $this->codigoBarra = $codigoBarra;
        $this->precio = $precio;
        $this->pathFoto = $pathFoto;
    }

    /**  
     *
     * Convierte los datos del Producto en formato JSON
     *     
     * @return string Datos del producto en formato JSON
     **/     
    public function toJSON(): string
    {
        $retorno = new stdClass();

        $retorno->nombre = $this->nombre;
        $retorno->origen = $this->origen;
        $retorno->id = $this->id;
        $retorno->codigoBarra = $this->codigoBarra;
        $retorno->precio = $this->precio;
        $retorno->pathFoto = $this->pathFoto;

        return json_encode($retorno);
    }

    //--------------------------------------------------------------------- Funciones de la BD ---------------------------------------------------------//


    /**  
     *
     * Agrega el producto a la base de datos
     *     
     * @return bool Retorna true si se pudo agregar el producto, false en caso contrario
     **/
    public function Agregar(): bool
    {
        $retorno = true;

        try {

            $pdo = new PDO('mysql:host=localhost;dbname=productos_bd;charset=utf8', "root", "");

            $consulta = $pdo->prepare('INSERT INTO productos (codigo_barra, nombre, origen, precio, foto)
             VALUES(:codigoBarra, :nombre, :origen, :precio, :foto)');

            $consulta->bindValue(':codigoBarra', $this->codigoBarra, PDO::PARAM_INT);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':origen', $this->origen, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
            $consulta->bindValue(':foto', $this->pathFoto, PDO::PARAM_STR);

            $consulta->execute();

        } catch (Exception $ex) 
        {
            $retorno = false;
        }

        return $retorno;
    }

    /**  
     *
     * Retorna un array con todos los ProductosEnvasados de la Base de Datos
     *     
     * @return array Retorna un array de Productos, si el archivo esta vacio retorna un array vacio.
     **/
    public static function Traer(): array
    {
        $retornoBD = array();
        $retornoArray = array();

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=productos_bd;charset=utf8', "root", "");

            $consulta = $pdo->prepare('SELECT id, codigo_barra AS codigoBarra, nombre AS nombre, origen AS origen,
            precio AS precio, foto AS pathFoto FROM productos ');

            $consulta->execute();

            $retornoBD = $consulta->fetchAll(PDO::FETCH_OBJ);

            foreach ($retornoBD as $productoBD) 
            {
                $idProducto = $productoBD->id;
                $codigoBarraProducto = $productoBD->codigoBarra;
                $nombreProducto = $productoBD->nombre;
                $origenProducto = $productoBD->origen;
                $precioProducto = $productoBD->precio;

                if (isset($productoBD->pathFoto)) {
                    $pathFotoProducto = $productoBD->pathFoto;
                } else {
                    $pathFotoProducto = "NULL";
                }


                $productoConvertido = new ProductoEnvasado($nombreProducto, $origenProducto, $codigoBarraProducto, $precioProducto, $pathFotoProducto, $idProducto);

                array_push($retornoArray, $productoConvertido);
            }
        } catch (Exception $th) 
        {
            $retornoArray = array();
            //En caso de un error retorno un array vacio          
        }

        return $retornoArray;
    }

    /**  
     *
     * Retorna un Producto Envasado con el mismo ID del parametro
     *    
     * @param int $id ID del que se desea obtener el Producto 
     * @return ProductoEnvasado|false Retorna un Producto envasado si el id coincide con alguno de la BD,caso contrario retorna false
     **/
    public static function TraerUno(int $id): ProductoEnvasado | false
    {
        try {

            $pdo = new PDO('mysql:host=localhost;dbname=productos_bd;charset=utf8', "root", "");

            $consulta = $pdo->prepare('SELECT id, codigo_barra AS codigoBarra, nombre AS nombre, origen AS origen,
            precio AS precio, foto AS pathFoto FROM productos WHERE id = :id');

            $consulta->bindValue(':id', $id, PDO::PARAM_INT);

            $consulta->execute();

            if ($consulta->rowCount() > 0) {

                $retornoProducto = $consulta->fetch(PDO::FETCH_OBJ);

                $idProducto = $retornoProducto->id;
                $codigoBarraProducto = $retornoProducto->codigoBarra;
                $nombreProducto = $retornoProducto->nombre;
                $origenProducto = $retornoProducto->origen;
                $precioProducto = $retornoProducto->precio;

                if (isset($retornoProducto->pathFoto)) {
                    $pathFotoProducto = $retornoProducto->pathFoto;
                } else {
                    $pathFotoProducto = "NULL";
                }

                $retorno = new ProductoEnvasado($nombreProducto, $origenProducto, $codigoBarraProducto, $precioProducto, $pathFotoProducto, $idProducto);
            } else {
                throw new Exception("El producto no existe en la BD");
            }
        } catch (Exception $th) {

            $retorno = false;
        }

        return $retorno;
    }

    /**  
     *
     * Elimina un Producto Envasado con el ID pasado por parametro
     *    
     * @param int $id ID del que se desea eliminar el Producto 
     * @return bool Retorna true si el producto fue eliminado de la BD, caso contrario retorna false
     **/
    public static function Eliminar(int $id): bool
    {
        $retorno = true;

        try {

            $pdo = new PDO('mysql:host=localhost;dbname=productos_bd;charset=utf8', "root", "");

            $consulta = $pdo->prepare('DELETE FROM productos WHERE id = :id');

            $consulta->bindValue(':id', $id, PDO::PARAM_INT);

            $consulta->execute();

            if ($consulta->rowCount() == 0) {
                throw new Exception("No se elimino ningun producto con ese ID");
            }
        } catch (Exception $ex) {
            $retorno = false;
        }

        return $retorno;
    }

    /**  
     *
     * Modifica el Producto Envasado que contiene el mismo id
     * 
     * @return bool Retorna true si el producto fue modificado de la BD, caso contrario retorna false
     **/
    public function Modificar(): bool
    {
        $retorno = true;

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=productos_bd;charset=utf8', "root", "");

            $consulta = $pdo->prepare("UPDATE productos SET codigo_barra = :codigoBarra, nombre = :nombre, 
            origen = :origen, precio = :precio, foto = :pathFoto WHERE id = :id");


            $consulta->bindValue(':codigoBarra', $this->codigoBarra, PDO::PARAM_INT);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':origen', $this->origen, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
            $consulta->bindValue(':pathFoto', $this->pathFoto, PDO::PARAM_STR);
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);

            $consulta->execute();

            if ($consulta->rowCount() === 0) 
            {
                throw new Exception("No se modifico ningun producto");
            }
        } catch (Exception $ex) {
            $retorno = false;
        }

        return $retorno;
    }

    //--------------------------------------------------------------------- Funciones de Fotos ---------------------------------------------------------

    /**  
     *
     * Valida que un archivo sea una foto y que no sea demasiado grande.
     * 
     * @return string Retorna un JSON que contiene : 
     *  exito(true|false) = Indicando si la foto es valida y
     *  mensaje(string) = Contiene el error en caso de false y '' en caso de true
     * 
     **/
    public static function ValidarFoto(array $foto, string $nombre, string $origen): string
    {
        try {

            $retorno = new stdClass();
            $retorno->exito = true;
            $retorno->mensaje = "";

            if ($foto != NULL) {
                $foto_nombre = $foto["name"];
                $extension = pathinfo($foto_nombre, PATHINFO_EXTENSION);
                $pathFoto = $nombre . "." . $origen . "." . date("his") . "." . $extension;
                $retorno->mensaje = "./productos/imagenes/" . $pathFoto;

                //VERIFICO EL TAMAÑO MAXIMO QUE PERMITO SUBIR

                if ($foto["size"] > 2000000) {
                    throw new Exception("El archivo es demasiado grande, inserte uno mas chico");
                }

                //OBTIENE EL TAMAÑO DE UNA IMAGEN, SI EL ARCHIVO NO ES UNA		
                //IMAGEN, RETORNA FALSE       

                $esImagen = getimagesize($foto["tmp_name"]);

                if ($esImagen != false) {
                    if (
                        $extension != "jpg" && $extension != "jpeg" && $extension != "gif"
                        && $extension != "png"
                    ) {
                        throw new Exception("Solo son permitidas imagenes con extension JPG, JPEG, PNG o GIF.");
                    }
                } else {
                    throw new Exception("El archivo no es una imagen,POR FAVOR inserte una imagen");
                }
            } else {
                throw new Exception("Es necesario cargar una foto para poder realizar el ALTA");
            }
        } catch (Exception $ex) {

            $retorno->exito = false;
            $retorno->mensaje = "Error : " . $ex->getMessage();
        } finally {
            return json_encode($retorno);
        }
    }

    /**  
     *
     * Guarda una foto en el path deseado
     * 
     * @param array $arrayFoto array foto que se quiere guardar
     * @return bool retorna true si se pudo subir la foto, false caso contrario
     * 
     **/
    public function GuardarFoto(array $arrayFoto): bool
    {
        $retorno = false;

        if (move_uploaded_file($arrayFoto["tmp_name"], $this->pathFoto)) {
            $retorno = true;
        }

        return $retorno;
    }

    //--------------------------------------------------------------------- Funciones de Archivos ---------------------------------------------------------

    /**  
     *
     * Guarda el archivo pasado por el atributo PathFoto en una nueva direccion, guardando sus datos en un txt  
     * 
     **/
    public function GuardarEnArchivo()
    {
        if (isset($this->pathFoto)) 
        {
            $extension = pathinfo($this->pathFoto, PATHINFO_EXTENSION);
            $nombrePath = $this->id . "." . $this->nombre . "." . "borrado" . "." . date("his") . "." . $extension;
            $pathCompleto = "./productosBorrados/" . $nombrePath;

            rename($this->pathFoto, $pathCompleto);

            $this->pathFoto = $pathCompleto;

            //ABRO EL ARCHIVO
            $ar = fopen(__DIR__ . "/../archivos/productos_envasados_borrados.txt", "a"); //A - append

            //ESCRIBO EN EL ARCHIVO
            $cant = fwrite($ar, $this->ToJSON() . ",\r\n");

            //CIERRO EL ARCHIVO
            fclose($ar);
        }
    }

    
    /**  
     *
     * Mueve el archivo a la carpeta productosModificados
     * 
     * @param string $path path del archivo que se quiere mover
     * @param string nombre que se le quiere poner al archivo
     * @param string origen que se le quiere poner al archivo
     * 
     * @return bool retorna true si se pudo mover el archivo, false caso contrario
     * 
     **/
    public static function MoverArchivoModificado(string $path, string $nombre, string $origen): bool
    {
        $retorno = false;
        
        if(file_exists($path))
        {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $nombreArchivo = $nombre . "." . $origen . "." . "modificado" . "." . date("his") . "." . $extension;
    
            $pathOriginal = __DIR__ . '/../' . $path;
            $pathDestino = __DIR__ . "/../productosModificados/" . $nombreArchivo;
    
            if (rename($pathOriginal, $pathDestino)) {
                $retorno = true;
            }
        }       

        return $retorno;
    }

    /**  
     *
     * Muestra las fotos de la carpeta Fotos Modificadas
     * 
     * @return string retorna la tabla html con las imagenes de la carpeta
     * 
     **/
    public static function MostrarFotosModificados(): string
    {

        $rutaImagenes = "./productosModificados/";
        $manejadorArchivos = opendir($rutaImagenes);

        $tablaHTML = '<html>
        <head><title> Imagenes de Productos Modificados </title></head>
        <body>
        
        <h1>Imagenes de Productos Modificados</h1>
        
        <table>
        <tr>          
          <th style="padding:0 15px 0 15px;"><strong>FOTOS</strong></th>
        </tr>
        
        ';

        while ($file = readdir($manejadorArchivos)) {
            if ($file != "." && $file != "..") {
                $rutaCompleta = $rutaImagenes . $file;
                $stringProducto = '<tr>            
            <td style="padding:0 15px 0 15px;"><img src="' . $rutaCompleta . '" width="50" height="50"></td>
            </tr>            
            ';

                $tablaHTML .= $stringProducto;
            }
        }

        $tablaHTML .= "</table>
    
        </body>
        </html>";

        return $tablaHTML;
    }

    //--------------------------------------------------------------------- Funciones de Productos ---------------------------------------------------------

    /**  
     *
     * Chequea si el producto existe en el array productos pasado por parametro
     * 
     * @param array $productos array donde se quiere chequear la existencia
     * @return bool true si existe el producto,caso contrario false
     * 
     **/
    public function Existe(array $productos): bool
    {
        $retorno = false;

        foreach ($productos as $producto) {
            if ($producto->nombre === $this->nombre && $producto->origen === $this->origen) {
                $retorno = true;
                break;
            }
        }

        return $retorno;
    }

    /**  
     *
     * Trae todos los productos borrados del sistema
     * 
     * @param string $path direccion donde se quiere obtener los productos borrados
     * @return array retorna un array de los productos borrados, en caso de no haber ninguna retorna un array vacio
     * 
     **/
    public static function TraerProductosBorrados(string $path): array
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
                $id = $producto->id;
                $codigoBarra = $producto->codigoBarra;
                $precio = $producto->precio;
                $pathFoto = $producto->pathFoto;

                $producto = new ProductoEnvasado($nombre, $origen, $codigoBarra, $precio, $pathFoto, $id);

                array_push($array_productos, $producto);
            }
        }

        return $array_productos;
    }

    /**  
     *
     * Muestra todos los productos borrados del sistema
     * 
     * @return string muestra los datos de los productos borrados del sistema
     * 
     **/
    public static function MostrarBorradosJSON(): string
    {
        $array_productos = ProductoEnvasado::TraerProductosBorrados(__DIR__ . "/../archivos/productos_eliminados.json");

        return json_encode($array_productos);
    }
}
