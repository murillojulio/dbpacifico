<?php
/**
 * Descripcion: Controlador que se encarga de la gestión de las politicas publicas
 *Julio Murillo
 * @category    
 * @package     Controllers  
 */
Load::models('global/fuente', 'opcion/impacto', 'observatorio/territorio', 
        'opcion/tipo_cultivo', 'util/currency', 'opcion/presunto_responsable',
        'afectacion/actor_armado');

class ActorArmadoController extends BackendController {
    
     /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Actores Armados';
        $this->page_title = 'Listado';   
    }
    
    /**
     * Método principal
     */
    public function index() {
    //Redirect::toAction('listar');
    }
    
       /**
     * Método para listar los territorios colectivos de comunidades negras
     */
    public function listar_territorio_cn($order='order.territorio.asc', $page='page.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $territorios = new Territorio();
        $this->territorios = $territorios->getListadoTerritorio('comunidad_negra', $order, $page);        
        $this->order = $order;      
        $this->page = $page;
        $this->page_title = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        $this->page_module = 'Actores Armados';
        Session::set('back', 'afectacion/actor_armado/listar_territorio_cn/'.$order.'/'.$page.'/');
    }
    
    /**
     * Método para listar los territorios colectivos indigenas
     */
    public function listar_territorio_ci($order='order.territorio.asc', $page='page.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $territorios = new Territorio();
        $this->territorios = $territorios->getListadoTerritorio('indigena', $order, $page);        
        $this->order = $order;  
        $this->page = $page;
        $this->page_title = Territorio::TERRITORIO_INDIGENA;
        $this->page_module = 'Actores Armados';
        Session::set('back', 'afectacion/actor_armado/listar_territorio_ci/'.$order.'/'.$page.'/');
    }
    

     /**
     * Método para buscar
     * 
     * @param type $field Nombre del campo a buscar
     * @param type $value Valor del campo
     * @param type $order Método de ordenamiento
     * @param type $page Número de página
     */
    public function buscar_territorio_cn($field='nombre', $value='none', $order='order.id.asc', $page='page.1') {        
        $page       = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field      = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value      = (Input::hasPost('value')) ? Input::post('value') : $value;
        //$tipo       = (Input::hasPost('tipo')) ? Input::post('tipo') : $tipo;
        
        $territorio     = new Territorio();
        $territorios    = $territorio->getAjaxTerritorio($field, $value, $order, $page, $tipo='comunidad_negra');
        if(empty($territorios->items)) {
            Flash::info('No se han encontrado registros');
        }
        $this->territorios  = $territorios;
        $this->order        = $order;
        $this->field        = $field;
        $this->value        = $value;
        $this->page_title   = 'Búsqueda de territorios en el sistema';  
        $this->page_module  = 'Actores Armados';
        Session::set('back', 'afectacion/actor_armado/buscar_territorio_cn/'.$field.'/'.$value.'/'.$order.'/'.$page.'/');
    }
    
    /**
     * Método para buscar
     * 
     * @param type $field Nombre del campo a buscar
     * @param type $value Valor del campo
     * @param type $order Método de ordenamiento
     * @param type $page Número de página
     */
    public function buscar_territorio_ci($field='nombre', $value='none', $order='order.id.asc', $page='page.1') {        
        $page       = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field      = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value      = (Input::hasPost('value')) ? Input::post('value') : $value;
                
        $territorio     = new Territorio();
        $territorios    = $territorio->getAjaxTerritorio($field, $value, $order, $page, $tipo='indigena');
        if(empty($territorios->items)) {
            Flash::info('No se han encontrado registros');
        }
        $this->territorios  = $territorios;
        $this->order        = $order;
        $this->field        = $field;
        $this->value        = $value;
        $this->page_title   = 'Búsqueda de territorios en el sistema';  
        $this->page_module  = 'Actores Armados';
        Session::set('back', 'afectacion/actor_armado/buscar_territorio_ci/'.$field.'/'.$value.'/'.$order.'/'.$page.'/');
    }
    
    
    /**
     * Método para editar
     */
    public function ver($key) { 
             
        if(!$id = Security::getKey($key, 'show_actor_armado', 'int')) {
            return Redirect::toAction('listar/');
        }  
        
        $obj_territorio = new Territorio();        
        if(!$obj_territorio->find($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del territorio');
            return Redirect::toAction('listar_cn');
        } 
        $this->obj_territorio = $obj_territorio;
        $actor_armado = $obj_territorio->getActorArmado();
        foreach ($actor_armado as $value)
        {            
             $this->array_actor_armado[] = $value->presunto_responsable_id;             
        } 
      
        $this->page_module = 'Actores Armados';
        $this->page_title = 'Información Actores Armados del territorio: '.$obj_territorio->nombre;
        $this->key = $key;
        $this->url_redir_back = Session::get('back');
    }
    
    
     
    /**
     * Método para editar
     */
    public function editar($key) { 
             
        if(!$id = Security::getKey($key, 'upd_actor_armado', 'int')) {
            return Redirect::toAction('listar/');
        }  
        
        $obj_territorio = new Territorio();        
        if(!$obj_territorio->find($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del territorio');
            return Redirect::toAction('listar_cn');
        } 
        $this->obj_territorio = $obj_territorio;
        $actor_armado = $obj_territorio->getActorArmado();
        
        foreach ($actor_armado as $value)
        {            
             $this->array_actor_armado[] = $value->presunto_responsable_id;             
        } 
      
        $fuente = new Fuente();
        $this->fuentes = $fuente->getListadoFuente('actor_armado', $obj_territorio->id);
        
        $this->page_module = 'Actores Armados';
        $this->page_title = 'Actualizar Actores Armados del territorio: '.$obj_territorio->nombre;
        $this->key = $key;
        $this->url_redir_back = Session::get('back');
        
         if(Input::hasPost('actor_armado')) {
            $objActorArmado = new ActorArmado();
            $objActorArmado->guardar(Input::post('actor_armado'), $obj_territorio->id);
            Flash::valid('Los actores armados se han actualizado correctamente!');
            return Redirect::to(Session::get('back'));                        
        }       
    }
    
     /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key) {
        if(!$id = Security::getKey($key, $tipo.'_cultivo_ilicito', 'int')) {
            return Redirect::toAction('listar/');
        }               
        $cultivo_ilicito = new ActorArmado();
        if(!$cultivo_ilicito->find_first($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del Actores Armados');            
        } else {
            if($tipo=='inactivar' && $cultivo_ilicito->estado == ActorArmado::INACTIVO) {
                Flash::info('El Actores Armados ya se encuentra inactivo');
            } else if($tipo=='reactivar' && $cultivo_ilicito->estado == ActorArmado::ACTIVO) {
                Flash::info('El Actores Armados ya se encuentra activo');
            } else {
                $estado = ($tipo=='inactivar') ? ActorArmado::INACTIVO : ActorArmado::ACTIVO;
                if(ActorArmado::setActorArmado('update', $cultivo_ilicito->to_array(), array('id'=>$id, 'estado'=>$estado))){
                    ($estado==ActorArmado::ACTIVO) ? Flash::valid('El Actores Armados se ha reactivado correctamente!') : Flash::valid('El Actores Armados se ha bloqueado correctamente!');
                }
            }                
        }
        
        return Redirect::toAction('listar/');
    }
    
    
    
    
    }
?>