<?php

use IgnacioEnriquez\ProductoEnvasado;

require_once("./clases/ProductoEnvasado.php");

try 
{
    echo ProductoEnvasado::MostrarBorradosJSON();
    
} catch (Exception $ex) 
{
    echo "ERROR : " . $ex -> getMessage();    
}

