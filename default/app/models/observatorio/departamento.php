<?php
/**
 *
 * Descripcion: Clase que gestiona los departamentos observados
 *
 * @category
 * @package     Models
 */


class Departamento extends ActiveRecord {   
     //protected $logger = true;
    /**
     * Constante para definir un departamento como activo
     */
    const ACTIVO = 1;
    
    /**
     * Constante para definir un departamento como inactivo
     */
    const INACTIVO = 2;
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {

        //$this->has_many('usuario');
        //$this->has_many('recurso_perfil');
    }
    
     public static function getDepartamentoPorNombre($nombre)
    {
        $obj = new Departamento();        
        $obj->find_first("nombre='".$nombre."'");
        
        return $obj;
    }
    
    /**
     * Método para obtener el listado de los departamentos observados
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoDepartamento($estado='todos', $order='', $page=0) {                   
        //$columns = 'departamento.*, (SELECT COUNT(municipio.id) FROM municipio WHERE municipio.departamento_id = departamento.id) AS cant_municipio';        
        $columns = 'departamento.*';
        $join = '';
        $conditions = 'departamento.id IS NOT NULL'; 

        $order = $this->get_order($order, 'nombre', array(            
            'nombre' => array(
                'ASC' => 'departamento.nombre ASC, departamento.nombre ASC',
                'DESC' => 'departamento.nombre DESC, departamento.nombre DESC'
            ),
            'cant_subregion_reg' => array(
                'ASC' => 'departamento.cant_subregion_reg ASC, departamento.cant_subregion_reg ASC',
                'DESC' => 'departamento.cant_subregion_reg DESC, departamento.cant_subregion_reg DESC'
            ),
            'cant_municipio_reg' => array(
                'ASC' => 'departamento.cant_municipio_pacifico ASC, departamento.cant_municipio_pacifico ASC',
                'DESC' => 'departamento.cant_municipio_pacifico DESC, departamento.cant_municipio_pacifico DESC'
            ),
            'cant_territorio_reg' => array(
                'ASC' => 'cant_territorio_reg ASC, cant_territorio_reg ASC',
                'DESC' => 'cant_territorio_reg DESC, cant_territorio_reg DESC'
            ),
            'cant_territorio_comunidad_negra' => array(
                'ASC' => 'cant_territorio_comunidad_negra ASC, cant_territorio_comunidad_negra ASC',
                'DESC' => 'cant_territorio_comunidad_negra DESC, cant_territorio_comunidad_negra DESC'
            ),
            'cant_territorio_indigena' => array(
                'ASC' => 'cant_territorio_indigena ASC, cant_territorio_indigena ASC',
                'DESC' => 'cant_territorio_indigena DESC, cant_territorio_indigena DESC'
            )));
        
        $group = 'departamento.id';
        if($page) {            
            //return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order", "page: $page");
            return $this->paginated_by_sql('SELECT departamento.*, 
                (SELECT COUNT(subregion.id) FROM subregion WHERE subregion.departamento_id = departamento.id) AS cant_subregion_reg,
                (SELECT COUNT(municipio.id) FROM municipio WHERE municipio.departamento_id = departamento.id) AS cant_municipio_reg, 
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id) AS cant_territorio_reg,
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id AND territorio.tipo = \'comunidad_negra\') AS cant_territorio_comunidad_negra,
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id AND territorio.tipo = \'indigena\') AS cant_territorio_indigena
                FROM departamento WHERE departamento.id != 0 AND departamento.id IS NOT NULL GROUP BY departamento.id ORDER BY '.$order, "page: $page");
        }
        
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
        //return $this->find_by_sql('SELECT departamento.*, (SELECT COUNT(municipio.id) FROM municipio WHERE municipio.departamento_id = departamento.id) AS cant_municipio_reg FROM departamento WHERE departamento.id IS NOT NULL GROUP BY departamento.id ORDER BY nombre ASC');
        
    }
    
    /**
     * Método para obtener el listado de los departamentos observados
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoDepartamentoDBS() {                   
        $columns = 'departamento.*';  
        $conditions = 'departamento.id != 0 AND departamento.id IS NOT NULL AND departamento.estado = 1'; 
        $order = 'departamento.nombre ASC';
        
        return $this->find("columns: $columns", "conditions: $conditions", "order: $order");
        
    }
    
    public function getListadoDepartamentoDBSUsuario() {                   
        $columns = 'departamento.*';  
        $conditions = 'departamento.id IS NOT NULL AND departamento.estado = 1'; 
        $order = 'departamento.nombre ASC';
        
        return $this->find("columns: $columns", "conditions: $conditions", "order: $order");
        
    }
    
    /**
     * Método para crear/modificar un objeto de base de datos
     * 
     * @param string $medthod: create, update
     * @param array $data: Data para autocargar el modelo
     * @param array $data_poblacion: Data para autocargar el modelo poblacion
     * @param array $optData: Data adicional para autocargar
     * 
     * return object ActiveRecord
     */
    public static function setDepartamento($method, $data, $optData=null) {        
        $obj = new Departamento($data); //Se carga los datos con los de las tablas          
        $boolean_result = TRUE;
        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }             
        //Verifico que no exista otro perfil, y si se encuentra inactivo lo active
        $conditions = empty($obj->id) ? "nombre = '$obj->nombre'" : "nombre = '$obj->nombre' AND id != '$obj->id'";
        $old = new Departamento();
        if($old->find_first($conditions)) 
            {            
            //Si existe y se intenta crear pero si no se encuentra activo lo activa
            if($method=='create' && $old->estado != Departamento::ACTIVO) {
                $obj->id        = $old->id;
                $obj->estado    = Departamento::ACTIVO;
                $method         = 'update';
            } else {
                Flash::info('Ya existe un departamento registrado bajo ese nombre.');
                return FALSE;
            }
        }
        
        $boolean_result = $obj->$method();   
        if($boolean_result) {
            if($method == 'create')
            {
                DwAudit::create("Se ha registrado el departamento  $obj->nombre en el sistema");
            }
            elseif ($method == 'update') {
                DwAudit::update("Se ha modificado la información del departamento $obj->nombre");
            }
            //($method == 'create') ? DwAudit::create("Se ha registrado el municipio  $obj->nombre en el sistema") : DwAudit::edit("Se ha modificado la información del municipio $obj->nombre");
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
    
    
     public function getAjaxDepartamento($field, $value, $order='', $page=0) {
        $value = Filter::get($value, 'string');
        if( strlen($value) <= 2 OR ($value=='none') ) {
            return NULL;
        }
        $columns = 'departamento.*';
        //$join = self::getInnerEstado();
        $join = '';        
        $conditions = "departamento.id != 0 AND departamento.id IS NOT NULL";
        
         $order = $this->get_order($order, 'nombre', array(            
            'nombre' => array(
                'ASC' => 'departamento.nombre ASC, departamento.nombre ASC',
                'DESC' => 'departamento.nombre DESC, departamento.nombre DESC'
            ),
            'cant_subregion_reg' => array(
                'ASC' => 'departamento.cant_subregion_reg ASC, departamento.cant_subregion_reg ASC',
                'DESC' => 'departamento.cant_subregion_reg DESC, departamento.cant_subregion_reg DESC'
            ),
            'cant_municipio_reg' => array(
                'ASC' => 'departamento.cant_municipio_pacifico ASC, departamento.cant_municipio_pacifico ASC',
                'DESC' => 'departamento.cant_municipio_pacifico DESC, departamento.cant_municipio_pacifico DESC'
            ),
            'cant_territorio_reg' => array(
                'ASC' => 'cant_territorio_reg ASC, cant_territorio_reg ASC',
                'DESC' => 'cant_territorio_reg DESC, cant_territorio_reg DESC'
            ),
            'cant_territorio_comunidad_negra' => array(
                'ASC' => 'cant_territorio_comunidad_negra ASC, cant_territorio_comunidad_negra ASC',
                'DESC' => 'cant_territorio_comunidad_negra DESC, cant_territorio_comunidad_negra DESC'
            ),
            'cant_territorio_indigena' => array(
                'ASC' => 'cant_territorio_indigena ASC, cant_territorio_indigena ASC',
                'DESC' => 'cant_territorio_indigena DESC, cant_territorio_indigena DESC'
            )));
        
        //Defino los campos habilitados para la búsqueda
        $fields = array('nombre', 'cant_municipio_reg');
        if(!in_array($field, $fields)) {
            $field = 'nombre';
        }                
        
        $conditions.= " AND $field LIKE '%$value%'";
        
        if($page) {
            //return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
            return $this->paginated_by_sql('SELECT departamento.*,                 
                (SELECT COUNT(subregion.id) FROM subregion WHERE subregion.departamento_id = departamento.id) AS cant_subregion_reg,
                (SELECT COUNT(municipio.id) FROM municipio WHERE municipio.departamento_id = departamento.id) AS cant_municipio_reg, 
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id) AS cant_territorio_reg,
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id AND territorio.tipo = \'comunidad_negra\') AS cant_territorio_comunidad_negra,
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id AND territorio.tipo = \'indigena\') AS cant_territorio_indigena
                FROM departamento WHERE '.$conditions.' GROUP BY departamento.id ORDER BY '.$order);
        
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
        }  
    }
    
    
     /**
     * Método para obtener el listado de los departamentos observados
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getDepartamentosEstadistica($order) {         
               
        return $this->find_all_by_sql('SELECT departamento.*,
                (SELECT COUNT(subregion.id) FROM subregion WHERE subregion.departamento_id = departamento.id) AS cant_subregion_reg,
                (SELECT COUNT(municipio.id) FROM municipio WHERE municipio.departamento_id = departamento.id) AS cant_municipio_reg, 
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id) AS cant_territorio_reg,
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id AND territorio.tipo = \'comunidad_negra\') AS cant_territorio_comunidad_negra,
                (SELECT COUNT(territorio.id) FROM territorio WHERE territorio.departamento_id = departamento.id AND territorio.tipo = \'indigena\') AS cant_territorio_indigena
                FROM departamento WHERE departamento.id != 0 AND departamento.id IS NOT NULL GROUP BY departamento.id ORDER BY '.$order);
        
       
           
    }


   
    
}
?>

