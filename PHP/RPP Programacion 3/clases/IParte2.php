<?php

namespace Ignacio\Enriquez;

interface IParte2
{
	function Eliminar() : bool; 
    function Modificar() : bool;
    static function GuardarEnArchivo(Ciudad $ciudad) : bool;	

}