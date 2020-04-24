<?php
/**
 *
 * Descripcion: Clase que gestiona las ubicaciones
 *
 * @category
 * @package     Models
 */

class Ubicacion extends ActiveRecord {
    
    //Se desabilita el logger para no llenar el archivo de "basura"
    //public $logger = FALSE;
       
    /**
     * Constante para definir un perfil como activo
     */
    const ACTIVO = 1;
    
    /**
     * Constante para definir un perfil como inactivo
     */
    const INACTIVO = 2;
    
    
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->belongs_to('departamento');
        $this->belongs_to('municipio');
        $this->belongs_to('territorio');  
    }
    
   
    /**
     * Método para crear/modificar un objeto de base de datos
     * 
     * @param string $medthod: create, update
     * @param array $data: Data para autocargar el modelo
     * @param array $optData: Data adicional para autocargar
     * 
     * return object ActiveRecord
     */
    public static function setUbicacion($method, $data, $optData=null) {        
        $obj = new Ubicacion($data); //Se carga los datos con los de las tablas        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }             
       
        $boolean_result = $obj->$method(); 
           
        return ($boolean_result) ? $obj : FALSE;
    }
    
    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
       
    }
    
    /**
     * Callback que se ejecuta después de guardar/modificar un perfil
     */
    protected function after_save() {
        
    }


}
?>
