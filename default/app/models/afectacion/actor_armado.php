<?php
/**
 *
 * Descripcion: Clase que gestiona las afectaciones
 *
 * @category
 * @package     Models
 */

class ActorArmado extends ActiveRecord {
    
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

        $this->belongs_to('territorio');     
    }
    
     public function guardar($dataActorArmado, $territorio_id)
    {        
        if ($this->delete_all("territorio_id = $territorio_id")) {
            foreach ($dataActorArmado as $value) {
                $obj_ActorArmado = new ActorArmado();
                $obj_ActorArmado->territorio_id = $territorio_id;
                $obj_ActorArmado->presunto_responsable_id = $value;
                $obj_ActorArmado->save();
            }
        } else {
            throw new KumbiaException('No se pudieron eliminar los actores armados');
        }        
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
