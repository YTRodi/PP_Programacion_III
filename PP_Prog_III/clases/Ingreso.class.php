<?php
include_once __DIR__.'/FileHandler.class.php';

class Ingreso extends FileHandler{ 
    public $_patente;
    public $_fechaIngreso;
    public $_tipo;
    public $_emailUsuario;
    public static $pathAutosJSON = './archivos/autos.json';

    public function __construct($patente,$fechaIngreso,$tipo,$emailUsuario) {
        $this->_patente = $patente;
        $this->_fechaIngreso = $fechaIngreso;
        $this->_tipo = $tipo;
        $this->_emailUsuario = $emailUsuario;
    }

    public function __get($name){ return $this->$name; }
    public function __set($name, $value){ $this->$name = $value; }
    public function __toString(){

        $datos = '';
        $datos .= 'DATOS DEL VEHICULO:</br>';
        $datos .= 'PATENTE: ' . $this->_patente  . '</br>';
        $datos .= 'FECHA INGRESO: ' . $this->_fechaIngreso  . '</br>';
        $datos .= 'TIPO: ' . $this->_tipo  . '</br>';
        $datos .= 'EMAIL USUARIO: ' . $this->_emailUsuario  . '</br>';

        return $datos;
    }

    //----------------------------------------------------------------
    //----------------------------------------------------------------
    //JSON
    public static function SaveIngresoJSON(array $arrayObj = null){
        try {

            echo parent::SaveJSON(Ingreso::$pathAutosJSON,$arrayObj);

        } catch (\Throwable $e) {

            throw new Exception($e->getMessage());
            
        }
    }

    public static function ReadIngresoJSON(){
        try {
            //Pasamanos...
            $listaFromArchivoJSON = parent::ReadJSON(Ingreso::$pathAutosJSON);
            $arrayIngreso = [];

            foreach ($listaFromArchivoJSON as $dato) {

                $nuevoIngreso = new Ingreso($dato->_patente,$dato->_fechaIngreso,$dato->_tipo,$dato->_emailUsuario);

                array_push($arrayIngreso,$nuevoIngreso);

            }

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        
        return $arrayIngreso;
    }
}