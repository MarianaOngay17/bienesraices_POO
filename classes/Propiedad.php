<?php

namespace App;

class Propiedad {

    //BD
    protected static $db;
    protected static $columnasDB = ['id','titulo','precio','imagen',
                    'descripcion', 'habitaciones', 'wc',
                    'estacionamiento','creado','vendedores_id'];

    //Errores
    protected static $errores = [];

    public $id;
    public $titulo;
    public $precio;
    public $imagen;
    public $descripcion;
    public $habitaciones;
    public $wc;
    public $estacionamiento;
    public $creado;
    public $vendedores_id;

    public static function setDB($database){
        self::$db = $database;
    }

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? '';
        $this->titulo = $args['titulo'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->habitaciones = $args['habitaciones'] ?? '';
        $this->wc = $args['wc'] ?? '';
        $this->estacionamiento = $args['estacionamiento'] ?? '';
        $this->creado = date('Y/m/d');
        $this->vendedores_id = $args['vendedores_id'] ?? '1';
    }

    public function guardar(){
        if(isset($this->id)){
            //actualizar
            $this->actualizar();
        }else{
            //crear nuevo registro
            $this->crear();
        }
    }

    public function crear(){

        $atributos = $this->sanitizarDatos();

        $query = "INSERT INTO propiedades ( ";
        $query .= join(', ', array_keys($atributos));
        $query .=  " ) VALUES (' ";
        $query .= join("', '", array_values($atributos));
        $query .= " ');";

        $resultado = self::$db->query($query);

        return $resultado;

    }

    public function actualizar(){
        $atributos = $this->sanitizarDatos();

        $valores = [];
        foreach($atributos as $key => $value){
            $valores[] = "{$key}= '{$value}'";
        }

        $query = "UPDATE propiedades SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1; ";

       $resultado = self::$db->query($query);
       
        if($resultado){
            header("Location: /admin?resultado=2");
        }
    }

    public function atributos(){
        $atributos = [];
        foreach(self::$columnasDB as $columna){
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarDatos(){
        $atributos = $this->atributos();
        $sanitizado = [];

        foreach($atributos as $key => $value){
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        
        return $sanitizado;
    }

    //validacion

    public static function getErrores(){
        return self::$errores;
    }

    public function validar(){
        if(!$this->titulo){
            self::$errores[] = "Debes añadir un título";
        }

        if(!$this->precio){
            self::$errores[] = "El Precio es obligatorio";
        }

        if( strlen($this->descripcion) < 50 ){
            self::$errores[] = "La descripción es obligatorio y debe tener al menos 50 caracteres";
        }

        if(!$this->habitaciones){
             self::$errores[] = "El Número de habitaciones es obligatorio";
        }

        if(!$this->wc){
             self::$errores[] = "El Número de Baños es obligatorio";
        }

        if(!$this->estacionamiento){
             self::$errores[] = "El Número de lugares de Estacionamientos es obligatorio";
        }

        if(!$this->vendedores_id){
             self::$errores[] = "Elige un vendedor";
        }

        if(!$this->imagen ) {
            self::$errores[] = 'La Imagen es Obligatoria';
        }

        return  self::$errores;
    }

    public function setImagen($imagen){
        //elimina imagen previa
        if(isset($this->id)){
            //validar si existe archivo
            $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
            
            if($existeArchivo){
                unlink(CARPETA_IMAGENES . $this->imagen);
            }
            
        }

        if($imagen){
            $this->imagen = $imagen;
        }
    }

    //listar todas las propiedades
    public static function all(){
        $query = "SELECT * FROM propiedades;";

        $resultado = self::consultarSQL($query);

        return $resultado;
        
    }

    //buscar registro por id
    public static function find($id){
        $query = "SELECT * FROM propiedades WHERE id=$id ";

        $resultado = self::consultarSQL($query);
       
        return array_shift($resultado);

    }

    public static function consultarSQL($query){
        $resultado = self::$db->query($query);

        $array = [];
        while($registro = $resultado->fetch_assoc()){
            $array[] = self::crearObjeto($registro);
        }

        $resultado->free();

        return $array;

    }
    
    protected static function crearObjeto($registro){
        $objeto = new self;

        foreach($registro as $key => $value){
            if(property_exists($objeto, $key)){
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    //sincronizar el objeto en memoria con los cambios realizados por el usuario
    public function sincronizar($args = []){
        foreach($args as $key => $value){
            if(property_exists($this, $key) && !is_null($value)){
                $this->$key = $value;
            }
        }
    }
}