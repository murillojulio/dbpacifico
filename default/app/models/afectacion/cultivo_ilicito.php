<?php
/**
 *
 * Descripcion: Clase que gestiona las politicas publicas
 *
 * @category
 * @package     Models
 */

class CultivoIlicito extends ActiveRecord {
    
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
        $this->belongs_to('tipo_cultivo');
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
    public static function setCultivoIlicito($method, $data, $optData=null) {        
        $obj = new CultivoIlicito($data); //Se carga los datos con los de las tablas        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }             
        
        $obj->area = Currency::comaApunto($obj->area);
        $obj->area_erradicacion = Currency::comaApunto($obj->area_erradicacion);
       
        $boolean_result = $obj->$method(); 
        
         if($boolean_result) {
            if($method == 'create')
            {
                $nombre_cultivo = $obj->getTipoCultivo()->nombre;
                DwAudit::create("Se ha registrado un Cultivo Ilícito de $nombre_cultivo en el sistema");
            }
            elseif ($method == 'update') {
                $nombre_cultivo = $obj->getTipoCultivo()->nombre;
                DwAudit::update("Se ha modificado la información del Cultivo Ilícito $nombre_cultivo");
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
    public function getListadoCultivoIlicito($estado='todos', $order='', $page=0) {                   
       
        $order = $this->get_order($order, 'nombre', array(            
            'nombre' => array(
                'ASC' => 'tipo_cultivo ASC, tipo_cultivo ASC',
                'DESC' => 'tipo_cultivo DESC, tipo_cultivo DESC'
            )));
        
        if($page) { 
            
            return $this->paginated_by_sql("SELECT cultivo_ilicito.*, tipo_cultivo.nombre AS tipo_cultivo FROM cultivo_ilicito 
                INNER JOIN tipo_cultivo ON tipo_cultivo.id = cultivo_ilicito.tipo_cultivo_id WHERE cultivo_ilicito.id IS NOT NULL ORDER BY ".$order, "page: $page");
            /*   
             *             return $this->paginated_by_sql("SELECT cultivo_ilicito.*, tipo_tipo_cultivo AS tipo_cultivo_ilicito FROM cultivo_ilicito
                INNER JOIN tipo_desarrollo_normativo ON tipo_desarrollo_normativo.id = desarrollo_normativo.tipo_desarrollo_normativo_id                
WHERE desarrollo_normativo.id IS NOT NULL AND desarrollo_normativo.clase_desarrollo_normativo ='".$clase_desarrollo_normativo."' GROUP BY desarrollo_normativo.nombre ORDER BY ".$order);
             */
        }
        
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
        
    }
    
     public function getAjaxCultivoIlicito($field, $value, $order='', $page=0) {
        $value = Filter::get($value, 'string');
        if( strlen($value) <= 2 OR ($value=='none') ) {
            return NULL;
        }
        $columns = 'cultivo_ilicito.*';
        //$join = self::getInnerEstado();
        $join = '';  
        $conditions =  "cultivo_ilicito.id IS NOT NULL";
        
         $order = $this->get_order($order, 'nombre', array(            
            'nombre' => array(
                'ASC' => 'tipo_cultivo ASC, tipo_cultivo ASC',
                'DESC' => 'tipo_cultivo DESC, tipo_cultivo DESC'
            ),
            'nombre_empresa' => array(
                'ASC' => 'tipo_cultivo_empresa ASC, tipo_cultivo_empresa ASC',
                'DESC' => 'tipo_cultivo_empresa DESC, tipo_cultivo_empresa DESC'
            )));
        
        //Defino los campos habilitados para la búsqueda
        $fields = array('nombre', 'nombre_empresa');
        if(!in_array($field, $fields)) {
            $field = 'nombre';
        }                
        
        $conditions.= " AND cultivo_ilicito.$field LIKE '%$value%'";
        
        if($page) {
            
            return $this->paginated_by_sql("SELECT cultivo_ilicito.*, tipo_cultivo.nombre AS tipo_cultivo
                FROM cultivo_ilicito INNER JOIN tipo_cultivo ON tipo_cultivo.id = cultivo_ilicito.tipo_cultivo_id
                WHERE ".$conditions." GROUP BY cultivo_ilicito.id ORDER BY ".$order);
        
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
        }  
    }


}
?>