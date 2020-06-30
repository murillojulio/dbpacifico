<?php
/**
 *
 * Descripcion: Clase que gestiona las politicas publicas
 *
 * @category
 * @package     Models
 */

class Cooperacion extends ActiveRecord {
    
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
        $this->belongs_to('afectacion');
        $this->has_many('cooperacion_municipio');
        $this->has_many('cooperacion_tipo_proyecto_cooperacion');
        $this->has_many('cooperacion_operador_cooperacion');  
        $this->belongs_to('clase_cooperacion');
        $this->belongs_to('tipo_cooperacion');
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
    public static function setCooperacion($method, $data, $optData=null) {        
        $obj = new Cooperacion($data); //Se carga los datos con los de las tablas        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }             
       
        $boolean_result = $obj->$method(); 
        
         if($boolean_result) {
            if($method == 'create')
            {
                DwAudit::create("Se ha registrado el Cooperacion  $obj->nombre_tipo_cooperacion en el sistema");
            }
            elseif ($method == 'update') {
                DwAudit::update("Se ha modificado la información del Cooperacion $obj->nombre_tipo_cooperacion");
            }
           
        }
           
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
    
    
     /**
     * Método para obtener el listado de las politicas publicas
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoCooperacion($estado='todos', $order='', $page=0) {                   
       
        $order = $this->get_order($order, 'nombre_tipo_cooperacion', array(            
            'nombre_tipo_cooperacion' => array(
                'ASC' => 'cooperacion.nombre_tipo_cooperacion ASC, cooperacion.nombre_tipo_cooperacion ASC',
                'DESC' => 'cooperacion.nombre_tipo_cooperacion DESC, cooperacion.nombre_tipo_cooperacion DESC'
            )));
        
        if($page) { 
            
            return $this->paginated_by_sql("SELECT cooperacion.*, 
                tipo_cooperacion.nombre AS tipo_cooperacion_nombre FROM cooperacion
                INNER JOIN tipo_cooperacion ON tipo_cooperacion.id = cooperacion.tipo_cooperacion_id 
                WHERE cooperacion.id IS NOT NULL ORDER BY ".$order, "page: $page");
            /*   
             *             return $this->paginated_by_sql("SELECT cooperacion.*, tipo_cooperacion.nombre_tipo_cooperacion AS tipo_cooperacion FROM cooperacion
                INNER JOIN tipo_cooperacion ON tipo_cooperacion.id = cooperacion.tipo_cooperacion_id                
WHERE cooperacion.id IS NOT NULL AND cooperacion.clase_cooperacion ='".$clase_cooperacion."' GROUP BY cooperacion.nombre_tipo_cooperacion ORDER BY ".$order);
             */
        }
        
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
        
    }
    
     public function getAjaxCooperacion($field, $value, $order='', $page=0) {
        $value = Filter::get($value, 'string');
        if( strlen($value) <= 2 OR ($value=='none') ) {
            return NULL;
        }
        $columns = 'cooperacion.*';
        //$join = self::getInnerEstado();
        $join = '';  
        $conditions =  "cooperacion.id IS NOT NULL";
        
         $order = $this->get_order($order, 'nombre_clase_cooperacion', array(            
            'nombre' => array(
                'ASC' => 'cooperacion.nombre_clase_cooperacion ASC, cooperacion.nombre_clase_cooperacion ASC',
                'DESC' => 'cooperacion.nombre_clase_cooperacion DESC, cooperacion.nombre_clase_cooperacion DESC'
            ),
            'nombre_tipo_cooperacion_empresa' => array(
                'ASC' => 'cooperacion.nombre_tipo_cooperacion_empresa ASC, cooperacion.nombre_tipo_cooperacion_empresa ASC',
                'DESC' => 'cooperacion.nombre_tipo_cooperacion_empresa DESC, cooperacion.nombre_tipo_cooperacion_empresa DESC'
            )));
        
        //Defino los campos habilitados para la búsqueda
        $fields = array('nombre_clase_cooperacion', 'nombre_tipo_cooperacion_empresa');
        if(!in_array($field, $fields)) {
            $field = 'nombre_clase_cooperacion';
        }                
        
        $conditions.= " AND cooperacion.$field LIKE '%$value%'";
        
        if($page) {
            
            return $this->paginated_by_sql("SELECT cooperacion.*, departamento.nombre AS nombre_departamento,
                tipo_cooperacion.nombre AS tipo_cooperacion_nombre FROM cooperacion
                INNER JOIN departamento ON departamento.id = cooperacion.departamento_id
                INNER JOIN tipo_cooperacion ON tipo_cooperacion.id = cooperacion.tipo_cooperacion_id
                WHERE ".$conditions." GROUP BY cooperacion.id ORDER BY ".$order);
        
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
        }  
    }


}
?>