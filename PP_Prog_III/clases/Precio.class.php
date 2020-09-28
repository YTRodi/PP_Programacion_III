<?php
include_once __DIR__.'/FileHandler.class.php';

class Precio extends FileHandler{ 
    public $_precioHora;
    public $_precioEstadia;
    public $_precioMensual;
    public static $pathPrecioJSON = './archivos/precios.json';

    public function __construct($precioHora,$precioEstadia,$precioMensual) {
        $this->_precioHora = $precioHora;
        $this->_precioEstadia = $precioEstadia;
        $this->_precioMensual = $precioMensual;
    }

    public function __get($name){ return $this->$name; }
    public function __set($name, $value){ $this->$name = $value; }
    public function __toString(){
        return $this->_precioHora . '*' . $this->_precioEstadia . '*' . $this->_precioMensual;
    }

    //----------------------------------------------------------------
    //----------------------------------------------------------------
    //JSON
    public static function SavePrecioJSON(array $arrayObj = null){
        try {

            echo parent::SaveJSON(Precio::$pathPrecioJSON,$arrayObj);

        } catch (\Throwable $e) {

            throw new Exception($e->getMessage());
            
        }
    }

    public static function ReadPrecioJSON(){
        try {
            //Pasamanos...
            $listaFromArchivoJSON = parent::ReadJSON(Precio::$pathPrecioJSON);
            $arrayPrecios = [];

            foreach ($listaFromArchivoJSON as $dato) {

                $nuevoPrecio = new Precio($dato->_precioHora,$dato->_precioEstadia,$dato->_precioMensual);

                array_push($arrayPrecios,$nuevoPrecio);

            }

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        
        return $arrayPrecios;
    }

    // public static function autoID(array $array = null){
    //     if($array !== null){
    //         $id = 0;
    //         foreach ($array as $item) {
    //             if($item->_id > $id){
    //                 $id = $item->_id;
    //             }
    //         }
    //     }
    //     return $id + 1;
    // }
}