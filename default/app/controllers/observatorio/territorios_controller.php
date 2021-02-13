<?php

/**
 * Descripcion: Controlador que se encarga de la gestión de los territorios del observatorio
 *
 * @category    
 * @package     Controllers  
 */
Load::models(
    'observatorio/territorio',
    'observatorio/departamento',
    'observatorio/municipio',
    'observatorio/poblacion',
    'observatorio/titulado_si',
    'observatorio/titulado_no',
    'observatorio/territorio_municipio',
    'observatorio/comunidad',
    'observatorio/conflicto',
    'observatorio/fuente',
    'util/currency',
    'util/pdf'
);
class TerritoriosController extends BackendController
{

    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter()
    {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Territorio';
    }

    /*
     * Método principal
     */
    public function index()
    {
        Redirect::toAction('listar');
    }

    /**
     * Método para listar los territorios colectivos que pertenecen a un municipio
     */
    public function listar_territorio($municipio_id, $municipio_nombre, $order = 'order.territorio.asc', $page = 'page.1')
    {
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $territorios = new TerritorioMunicipio();
        $this->territorios = $territorios->getTerritoriosByMunicipioId($municipio_id, $order, $page);
        $this->order = $order;
        $this->page = $page;
        $this->municipio_id = $municipio_id;
        $this->municipio_nombre = $municipio_nombre;
        $this->page_title = 'Territorios que pertenecen al municipio: ' . $municipio_nombre;
        $this->page_module = 'Territorios Colectivos';
    }

    /**
     * Método para listar los territorios colectivos de comunidades negras
     */
    public function listar_territorio_cn($order = 'order.territorio.asc', $page = 'page.1')
    {

        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $territorios = new Territorio();
        $this->territorios = $territorios->getListadoTerritorio('comunidad_negra', $order, $page);
        $this->order = $order;
        $this->page = $page;
        $this->page_title = 'Listado de territorios monitoriados';
        $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        Session::set('url_back', 'observatorio/territorios/listar_territorio_cn/' . $order . '/page.' . $page . '/');
    }

    /**
     * Método para listar los territorios colectivos indigenas
     */
    public function listar_territorio_ci($order = 'order.territorio.asc', $page = 'page.1')
    {
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $territorios = new Territorio();
        $this->territorios = $territorios->getListadoTerritorio('indigena', $order, $page);
        $this->order = $order;
        $this->page = $page;
        $this->page_title = 'Listado de territorios monitoriados';
        $this->page_module = Territorio::TERRITORIO_INDIGENA;
        Session::set('url_back', 'observatorio/territorios/listar_territorio_ci/' . $order . '/page.' . $page . '/');
    }

    /**
     * Método para listar los territorios urbanos
     */
    public function listar_territorio_ur($order = 'order.territorio.asc', $page = 'page.1')
    {
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $territorios = new Territorio();
        $this->territorios = $territorios->getListadoTerritorio('urbano', $order, $page);
        $this->order = $order;
        $this->page = $page;
        $this->page_title = 'Listado de territorios monitoriados';
        $this->page_module = Territorio::TERRITORIO_URBANO;
        Session::set('url_back', 'observatorio/territorios/listar_territorio_ur/' . $order . '/page.' . $page . '/');
    }

    /**
     * Método para buscar
     * 
     * @param type $field Nombre del campo a buscar
     * @param type $value Valor del campo
     * @param type $order Método de ordenamiento
     * @param type $page Número de página
     */
    public function buscar_territorio_cn($field = 'nombre', $value = 'none', $order = 'order.id.asc', $page = 'page.1')
    {
        $page       = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field      = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value      = (Input::hasPost('value')) ? Input::post('value') : $value;
        //$tipo       = (Input::hasPost('tipo')) ? Input::post('tipo') : $tipo;

        $territorio     = new Territorio();
        $territorios    = $territorio->getAjaxTerritorio($field, $value, $order, $page, $tipo = 'comunidad_negra');
        if (empty($territorios->items)) {
            Flash::info('No se han encontrado registros');
        }
        $this->territorios  = $territorios;
        $this->order        = $order;
        $this->field        = $field;
        $this->value        = $value;
        $this->page_title   = 'Búsqueda de territorios en el sistema';
        $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        Session::set('url_back', 'observatorio/territorios/buscar_territorio_cn/' . $field . '/' . $value . '/' . $order . '/page.' . $page . '/');
    }

    /**
     * Método para buscar
     * 
     * @param type $field Nombre del campo a buscar
     * @param type $value Valor del campo
     * @param type $order Método de ordenamiento
     * @param type $page Número de página
     */
    public function buscar_territorio_ci($field = 'nombre', $value = 'none', $order = 'order.id.asc', $page = 'page.1')
    {
        $page       = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field      = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value      = (Input::hasPost('value')) ? Input::post('value') : $value;

        $territorio     = new Territorio();
        $territorios    = $territorio->getAjaxTerritorio($field, $value, $order, $page, $tipo = 'indigena');
        if (empty($territorios->items)) {
            Flash::info('No se han encontrado registros');
        }
        $this->territorios  = $territorios;
        $this->order        = $order;
        $this->field        = $field;
        $this->value        = $value;
        $this->page_title   = 'Búsqueda de territorios en el sistema';
        $this->page_module = Territorio::TERRITORIO_INDIGENA;
        Session::set('url_back', 'observatorio/territorios/buscar_territorio_ci/' . $field . '/' . $value . '/' . $order . '/page.' . $page . '/');
    }

    /**
     * Método para buscar
     * 
     * @param type $field Nombre del campo a buscar
     * @param type $value Valor del campo
     * @param type $order Método de ordenamiento
     * @param type $page Número de página
     */
    public function buscar_territorio_ur($field = 'nombre', $value = 'none', $order = 'order.id.asc', $page = 'page.1')
    {
        $page       = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field      = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value      = (Input::hasPost('value')) ? Input::post('value') : $value;
        //$tipo       = (Input::hasPost('tipo')) ? Input::post('tipo') : $tipo;

        $territorio     = new Territorio();
        $territorios    = $territorio->getAjaxTerritorio($field, $value, $order, $page, $tipo = 'urbano');
        if (empty($territorios->items)) {
            Flash::info('No se han encontrado registros');
        }
        $this->territorios  = $territorios;
        $this->order        = $order;
        $this->field        = $field;
        $this->value        = $value;
        $this->page_title   = 'Búsqueda de territorios en el sistema';
        $this->page_module = Territorio::TERRITORIO_URBANO;
        Session::set('url_back', 'observatorio/territorios/buscar_territorio_ur/' . $field . '/' . $value . '/' . $order . '/page.' . $page . '/');
    }

    /**
     * Método para agregar territorios colectivos de comunidades negras
     */
    public function index_agregar_territorio($tipo_territorio)
    {

        $redir = '';
        if ($tipo_territorio == 'comunidad_negra') {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
            $redir = 'agregar_territorio_cn';
        } elseif ($tipo_territorio == 'indigena') {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
            $redir = 'agregar_territorio_ci';
        } elseif ($tipo_territorio == 'urbano') {
            $this->page_module = Territorio::TERRITORIO_URBANO;
            $redir = 'agregar_territorio_ur';
            Redirect::toAction('index_agregar_territorio_urbano');
        }

        $departamentos = new Departamento();
        $this->departamentos = $departamentos->getListadoDepartamentoDBS();

        $territorio_obj = new Territorio();

        if (Input::hasPost('territorio')) {
            if (Input::hasPost('municipio')) {
                $dataTerritorio = Input::post('territorio');
                $dataMunicipio = Input::post('municipio');

                if ($territorio_obj->validateExistTerritorio($dataTerritorio['nombre'], $dataMunicipio)) {
                    Flash::error('Ya existe un territorio con ese nombre y en el mismo municipio!');
                } else {
                    Session::set('data_territorio', serialize($dataTerritorio));
                    Session::set('data_municipio', serialize($dataMunicipio));

                    return Redirect::toAction($redir);
                }
            } else {
                Flash::error('Debe selecionar por lo menos un municipio!');
            }
        }



        $this->page_title = 'Agregar territorio';
    }

    /**
     * Método para agregar territorios urbanos a cada municipio
     */
    public function index_agregar_territorio_urbano()
    {

        $this->page_module = Territorio::TERRITORIO_URBANO;
        $redir = 'editar_urbano';


        $territorio_obj = new Territorio();

        if (Input::hasPost('territorio')) {
            if (Input::hasPost('territorio')) {
                $dataTerritorio = Input::post('territorio');

                if ($territorio_obj->validateExistTerritorioUrbano($dataTerritorio['nombre'], $dataTerritorio['muncipio_id'])) {
                    Flash::error('Ya existe un territorio urbano con ese nombre y en el mismo municipio!');
                } else {
                    $Municipio = new Municipio();
                    $Municipio->find_first($dataTerritorio['muncipio_id']);
                    $departamento_id = $Municipio->getDepartamento()->id;

                    $Territorio = new Territorio();

                    $Territorio->tipo = 'urbano';
                    $Territorio->nombre = $dataTerritorio['nombre'];
                    $Territorio->departamento_id = $departamento_id;
                    $Territorio->cant_sin_ninos_primera_inf = 1;
                    if ($Territorio->create()) {
                        $TerritorioMunicipio = new TerritorioMunicipio();
                        $TerritorioMunicipio->territorio_id = $Territorio->id;
                        $TerritorioMunicipio->municipio_id = $dataTerritorio['muncipio_id'];
                        $TerritorioMunicipio->create();
                    }
                    Session::set('data_territorio', serialize($dataTerritorio));
                    $key_upd = Security::setKey($Territorio->id, 'upd_territorio');
                    return Redirect::toAction("editar_urbano/$key_upd/1/order.asc.territorio/page.1/");
                }
            } else {
                Flash::error('Debe selecionar por lo menos un municipio!');
            }
        }



        $this->page_title = 'Agregar territorio urbano';
    }

    /**
     * Método para agregar territorios colectivos de comunidades negras
     */
    public function agregar_territorio_cn()
    {

        $dataTerritorio = unserialize(Session::get('data_territorio'));
        $dataMunicipio = unserialize(Session::get('data_municipio'));

        $this->territorio_nombre = $dataTerritorio['nombre'];
        $this->territorio_titulado = $dataTerritorio['titulado'];
        $this->departamento_nombre = $dataTerritorio['departamento_nombre'];
        $this->departamento_id = $dataTerritorio['departamento_id'];
        $str_municipios = '';

        $territorio_obj = new Territorio();
        $territorio_id = NULL;

        $municipio_obj = new Municipio();
        $municipios = $municipio_obj->getMunicipioPorDepartamento($this->departamento_id);

        for ($i = 0; $i < count($dataMunicipio); $i++) {
            foreach ($municipios as $municipio) :
                if ($municipio->id === $dataMunicipio[$i]) {
                    $str_municipios = $str_municipios . $municipio->nombre . ' - ';
                    break;
                }
            endforeach;
        }

        $this->str_municipios = $str_municipios;


        if (Input::hasPost('territorio')) {
            $territorio_obj = Territorio::setTerritorio('create', Input::post('territorio'), array('estado' => Territorio::ACTIVO));
            $territorio_id = $territorio_obj->id;
            if ($territorio_obj) {

                //TerritorioMunicipio::setTerritorioMunicipio('create', $dataMunicipio, $territorio_id);
                $obj_TerritorioMunicipio = new TerritorioMunicipio();
                $obj_TerritorioMunicipio->guardar($dataMunicipio, $territorio_id);
                Poblacion::setPoblacion('create', Input::post('poblacion'), 'territorio_id', $territorio_id);
                Fuente::setFuente('create', Input::post('fuente'), 'territorio', $territorio_id);

                if ($this->territorio_titulado == 'SI') {
                    $post_tituladosi = Input::post('tituladosi');
                    $post_tituladosi['area_titulada'] = Currency::comaApunto($post_tituladosi['area_titulada']);
                    $post_tituladosi['solicitud_ampliacion_area'] = Currency::comaApunto($post_tituladosi['solicitud_ampliacion_area']);
                    $post_tituladosi['solicitud_saneamiento_area'] = Currency::comaApunto($post_tituladosi['solicitud_saneamiento_area']);

                    TituladoSi::setTituladoSi('create', $post_tituladosi, $territorio_id);
                }
                if ($this->territorio_titulado == 'NO') {
                    $post_tituladono = Input::post('tituladono');
                    $post_tituladono['solicitud_titulo_area'] = Currency::comaApunto($post_tituladono['solicitud_titulo_area']);

                    TituladoNo::setTituladoNo('create', $post_tituladono, $territorio_id);
                }
                Flash::valid('El territorio se ha registrado correctamente!');
                return Redirect::toAction('listar_territorio_cn');
            }
        }

        $this->page_title = 'Agregar territorio';
        $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        $this->url_redir_back = Session::get('url_back');
    }

    /**
     * Método para agregar territorios colectivos indigena
     */
    public function agregar_territorio_ci()
    {

        $dataTerritorio = unserialize(Session::get('data_territorio'));
        $dataMunicipio = unserialize(Session::get('data_municipio'));

        $this->territorio_nombre = $dataTerritorio['nombre'];
        $this->territorio_titulado = $dataTerritorio['titulado'];
        $this->departamento_nombre = $dataTerritorio['departamento_nombre'];
        $this->departamento_id = $dataTerritorio['departamento_id'];
        $str_municipios = '';

        $territorio_obj = new Territorio();
        $territorio_id = NULL;

        $municipio_obj = new Municipio();
        $municipios = $municipio_obj->getMunicipioPorDepartamento($this->departamento_id);

        for ($i = 0; $i < count($dataMunicipio); $i++) {
            foreach ($municipios as $municipio) :
                if ($municipio->id === $dataMunicipio[$i]) {
                    $str_municipios = $str_municipios . $municipio->nombre . ' - ';
                    break;
                }
            endforeach;
        }

        $this->str_municipios = $str_municipios;


        if (Input::hasPost('territorio')) {
            $territorio_obj = Territorio::setTerritorio('create', Input::post('territorio'), array('estado' => Territorio::ACTIVO));
            $territorio_id = $territorio_obj->id;
            if ($territorio_obj) {

                TerritorioMunicipio::setTerritorioMunicipio('create', $dataMunicipio, $territorio_id);
                Poblacion::setPoblacion('create', Input::post('poblacion'), 'territorio_id', $territorio_id);
                Fuente::setFuente('create', Input::post('fuente'), 'territorio', $territorio_id);

                if ($this->territorio_titulado == 'SI') {
                    $post_tituladosi = Input::post('tituladosi');
                    $post_tituladosi['area_titulada'] = Currency::comaApunto($post_tituladosi['area_titulada']);
                    $post_tituladosi['solicitud_ampliacion_area'] = Currency::comaApunto($post_tituladosi['solicitud_ampliacion_area']);
                    $post_tituladosi['solicitud_saneamiento_area'] = Currency::comaApunto($post_tituladosi['solicitud_saneamiento_area']);

                    TituladoSi::setTituladoSi('create', $post_tituladosi, $territorio_id);
                }
                if ($this->territorio_titulado == 'NO') {
                    $post_tituladono = Input::post('tituladono');
                    $post_tituladono['solicitud_titulo_area'] = Currency::comaApunto($post_tituladono['solicitud_titulo_area']);

                    TituladoNo::setTituladoNo('create', $post_tituladono, $territorio_id);
                }
                Flash::valid('El territorio se ha registrado correctamente!');
                return Redirect::toAction('listar_territorio_ci');
            }
        }

        $this->page_title = 'Agregar territorio';
        $this->page_module = Territorio::TERRITORIO_INDIGENA;
        $this->url_redir_back = Session::get('url_back');
    }


    /**
     * Método para agregar territorios urbanos
     */
    public function agregar_territorio_ur()
    {

        $dataTerritorio = unserialize(Session::get('data_territorio'));
        $dataMunicipio = unserialize(Session::get('data_municipio'));

        $this->territorio_nombre = $dataTerritorio['nombre'];
        $this->territorio_titulado = $dataTerritorio['titulado'];
        $this->departamento_nombre = $dataTerritorio['departamento_nombre'];
        $this->departamento_id = $dataTerritorio['departamento_id'];
        $str_municipios = '';

        $territorio_obj = new Territorio();
        $territorio_id = NULL;

        $municipio_obj = new Municipio();
        $municipios = $municipio_obj->getMunicipioPorDepartamento($this->departamento_id);

        for ($i = 0; $i < count($dataMunicipio); $i++) {
            foreach ($municipios as $municipio) :
                if ($municipio->id === $dataMunicipio[$i]) {
                    $str_municipios = $str_municipios . $municipio->nombre . ' - ';
                    break;
                }
            endforeach;
        }

        $this->str_municipios = $str_municipios;


        if (Input::hasPost('territorio')) {
            $territorio_obj = Territorio::setTerritorio('create', Input::post('territorio'), array('estado' => Territorio::ACTIVO));
            $territorio_id = $territorio_obj->id;
            if ($territorio_obj) {

                //TerritorioMunicipio::setTerritorioMunicipio('create', $dataMunicipio, $territorio_id);
                $obj_TerritorioMunicipio = new TerritorioMunicipio();
                $obj_TerritorioMunicipio->guardar($dataMunicipio, $territorio_id);
                Poblacion::setPoblacion('create', Input::post('poblacion'), 'territorio_id', $territorio_id);
                Fuente::setFuente('create', Input::post('fuente'), 'territorio', $territorio_id);

                if ($this->territorio_titulado == 'SI') {
                    $post_tituladosi = Input::post('tituladosi');
                    $post_tituladosi['area_titulada'] = Currency::comaApunto($post_tituladosi['area_titulada']);
                    $post_tituladosi['solicitud_ampliacion_area'] = Currency::comaApunto($post_tituladosi['solicitud_ampliacion_area']);
                    $post_tituladosi['solicitud_saneamiento_area'] = Currency::comaApunto($post_tituladosi['solicitud_saneamiento_area']);

                    TituladoSi::setTituladoSi('create', $post_tituladosi, $territorio_id);
                }
                if ($this->territorio_titulado == 'NO') {
                    $post_tituladono = Input::post('tituladono');
                    $post_tituladono['solicitud_titulo_area'] = Currency::comaApunto($post_tituladono['solicitud_titulo_area']);

                    TituladoNo::setTituladoNo('create', $post_tituladono, $territorio_id);
                }
                Flash::valid('El territorio se ha registrado correctamente!');
                return Redirect::toAction('listar_territorio_ur');
            }
        }

        $this->page_title = 'Agregar territorio';
        $this->page_module = Territorio::TERRITORIO_URBANO;
        $this->url_redir_back = Session::get('url_back');
    }


    /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key)
    {
        if (!$id = Security::getKey($key, $tipo . '_territorio', 'int')) {
            return Redirect::toAction('listar');
        }

        $territorio = new Territorio();
        if (!$territorio->find_first($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del territorio');
        } else {
            if ($tipo == 'inactivar' && $territorio->estado == Territorio::INACTIVO) {
                Flash::info('El territorio ya se encuentra inactivo');
            } else if ($tipo == 'reactivar' && $territorio->estado == Territorio::ACTIVO) {
                Flash::info('El territorio ya se encuentra activo');
            } else {
                $estado = ($tipo == 'inactivar') ? Territorio::INACTIVO : Territorio::ACTIVO;
                if (Territorio::setTerritorio('update', $territorio->to_array(), array('id' => $id, 'estado' => $estado))) {
                    ($estado == Territorio::ACTIVO) ? Flash::valid('El territorio se ha reactivado correctamente!') : Flash::valid('El territorio se ha bloqueado correctamente!');
                }
            }
        }
        return Redirect::to(Session::get('url_back'));
    }

    /**
     * Método para ver
     */
    public function ver($key, $tab, $order, $page)
    {

        if (!$id = Security::getKey($key, 'show_territorio', 'int')) {
            return Redirect::toAction('listar');
        }

        //Para saber que pestaña estara activa cuando visualice un territorio
        $this->tab_1_active = '';
        $this->tab_2_active = '';
        $this->tab_3_active = '';
        $this->tab_4_active = '';
        $this->tab_5_active = '';

        if ($tab == 1) {
            $this->tab_1_active = 'active';
        }
        if ($tab == 2) {
            $this->tab_2_active = 'active';
        }
        if ($tab == 3) {
            $this->tab_3_active = 'active';
        }
        if ($tab == 4) {
            $this->tab_4_active = 'active';
        }
        if ($tab == 5) {
            $this->tab_5_active = 'active';
        }


        $obj_territorio = new Territorio();
        if (!$obj_territorio->getTerritorioById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del territorio');
            return Redirect::toAction('listar_cn');
        }
        $this->obj_territorio = $obj_territorio;

        $obj_territorio_municipio = new TerritorioMunicipio();
        $this->ubicaciones = $obj_territorio_municipio->getDepartamentoMunicipioByTerritorioId($id);
        //
        //        
        //        $obj_territorio_municipio = new TerritorioMunicipio();
        //        $array_territorio_municipio = $obj_territorio_municipio->getTerritorioMunicipio($id);
        //        
        //        $str_municipios = '';
        //        foreach($array_territorio_municipio as $municipio):        
        //            $str_municipios = $str_municipios.$municipio->municipio.' - ';                   
        //        endforeach;
        //        $this->str_municipios = substr($str_municipios, 0, -3);
        //


        if ($obj_territorio->titulado == 'SI') {
            $obj_titulado_si = new TituladoSi();
            $obj_titulado_si->getTituladoSiByTerritorioId($id);
            $this->obj_titulado_si = $obj_titulado_si;
        }

        if ($obj_territorio->titulado == 'NO') {
            $obj_titulado_no = new TituladoNo();
            $obj_titulado_no->getTituladoNoByTerritorioId($id);
            $this->obj_titulado_no = $obj_titulado_no;
        }

        $poblacion = new Poblacion();
        $poblacion = Poblacion::getPoblacion('territorio_id', $id);
        $this->poblacion = $poblacion;

        $obj_comunidades = new Comunidad();
        $this->comunidades = $obj_comunidades->getComunidadesByTerritorioId($id);

        $cantidad_comunidad = 0;
        foreach ($this->comunidades as $registros) :
            $cantidad_comunidad++;
        endforeach;
        $this->cantidad_comunidad = $cantidad_comunidad;

        $obj_conflictos = new Conflicto();
        $this->conflictos = $obj_conflictos->getConflictosByTerritorioId($id);

        $redir_back = '';
        if ($obj_territorio->tipo == 'comunidad_negra') {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
            $redir_back = 'observatorio/territorios/listar_territorio_cn/';
        } elseif ($obj_territorio->tipo == 'indigena') {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
            $redir_back = 'observatorio/territorios/listar_territorio_ci/';
        } elseif ($obj_territorio->tipo == 'urbano') {
            $this->page_module = Territorio::TERRITORIO_URBANO;
            $redir_back = 'observatorio/territorios/listar_territorio_ur/';
        }


        $fuente = new Fuente();
        $this->fuentes = $fuente->getListadoFuente('territorio', $obj_territorio->id);

        $this->redir_back = $redir_back;
        $this->page_title = 'Información del Territorio: ' . $obj_territorio->nombre;
        $this->tab = $tab;
        $this->key = $key;
        $this->order = $order;
        $this->page = $page;
        //$this->url_redir_back = $redir_back.$order.'/'.$page.'/';
        $this->url_redir_back = Session::get('url_back');
    }

    /**
     * Método para editar
     */
    public function editar($key, $tab, $order, $page)
    {

        if (!$id = Security::getKey($key, 'upd_territorio', 'int')) {
            return Redirect::toAction('listar_cn');
        }

        $this->territorio_id = $id;

        //Para saber que pestaña estara activa cuando visualice un territorio
        $this->tab_1_active = '';
        $this->tab_2_active = '';
        $this->tab_3_active = '';
        $this->tab_4_active = '';
        $this->tab_5_active = '';

        if ($tab == 1) {
            $this->tab_1_active = 'active';
        }
        if ($tab == 2) {
            $this->tab_2_active = 'active';
        }
        if ($tab == 3) {
            $this->tab_3_active = 'active';
        }
        if ($tab == 4) {
            $this->tab_4_active = 'active';
        }
        if ($tab == 5) {
            $this->tab_5_active = 'active';
        }

        /////


        $obj_territorio = new Territorio();
        if (!$obj_territorio->getTerritorioById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del territorio');
            return Redirect::toAction('listar_cn');
        }
        $this->obj_territorio = $obj_territorio;

        $obj_titulado_si = new TituladoSi();
        if ($obj_territorio->titulado == 'SI') {

            $obj_titulado_si->getTituladoSiByTerritorioId($id);
            $this->obj_titulado_si = $obj_titulado_si;
        }

        $obj_titulado_no = new TituladoNo();
        if ($obj_territorio->titulado == 'NO') {

            $obj_titulado_no->getTituladoNoByTerritorioId($id);
            $this->obj_titulado_no = $obj_titulado_no;
        }


        if (Input::hasPost('territorio') && Input::hasPost('poblacion')) {

            if (Territorio::setTerritorio('update', Input::post('territorio'), array('id' => $id))) {
                Poblacion::setPoblacion('update', Input::post('poblacion'), 'territorio_id', $id);

                Fuente::setFuente('update', Input::post('fuente'), 'territorio', $id);

                $data_e = Input::post('territorio');
                if ($data_e['titulado'] == 'SI') {
                    if ($obj_titulado_si->find_first('territorio_id = ' . $id)) {

                        $post_tituladosi = Input::post('tituladosi');
                        $post_tituladosi['area_titulada'] = Currency::comaApunto($post_tituladosi['area_titulada']);
                        $post_tituladosi['solicitud_ampliacion_area'] = Currency::comaApunto($post_tituladosi['solicitud_ampliacion_area']);
                        $post_tituladosi['solicitud_saneamiento_area'] = Currency::comaApunto($post_tituladosi['solicitud_saneamiento_area']);


                        TituladoSi::setTituladoSi('update', $post_tituladosi, $obj_territorio->id, array('id' => $obj_titulado_si->id));
                    } else {
                        $post_tituladosi = Input::post('tituladosi');
                        $post_tituladosi['area_titulada'] = Currency::comaApunto($post_tituladosi['area_titulada']);
                        $post_tituladosi['solicitud_ampliacion_area'] = Currency::comaApunto($post_tituladosi['solicitud_ampliacion_area']);
                        $post_tituladosi['solicitud_saneamiento_area'] = Currency::comaApunto($post_tituladosi['solicitud_saneamiento_area']);

                        TituladoSi::setTituladoSi('create', $post_tituladosi, $obj_territorio->id);
                    }
                } elseif ($data_e['titulado'] == 'NO') {
                    if ($obj_titulado_no->find_first('territorio_id = ' . $id)) {

                        $post_tituladono = Input::post('tituladono');
                        $post_tituladono['solicitud_titulo_area'] = Currency::comaApunto($post_tituladono['solicitud_titulo_area']);

                        TituladoNo::setTituladoNo('update', $post_tituladono, $obj_territorio->id, array('id' => $obj_titulado_no->id));
                    } else {
                        $post_tituladono = Input::post('tituladono');
                        $post_tituladono['solicitud_titulo_area'] = Currency::comaApunto($post_tituladono['solicitud_titulo_area']);

                        TituladoNo::setTituladoNo('create', $post_tituladono, $obj_territorio->id);
                    }
                }
                Flash::valid('El territorio se ha actualizado correctamente!');
                return Redirect::to(Session::get('url_back'));
            }
        }



        $obj_territorio_municipio = new TerritorioMunicipio();
        $this->ubicaciones = $obj_territorio_municipio->getDepartamentoMunicipioByTerritorioId($id);


        $poblacion = new Poblacion();
        $poblacion = Poblacion::getPoblacion('territorio_id', $id);
        $this->poblacion = $poblacion;

        $obj_comunidades = new Comunidad();
        $this->comunidades = $obj_comunidades->getComunidadesByTerritorioId($id);

        $cantidad_comunidad = 0;
        foreach ($this->comunidades as $registros) :
            $cantidad_comunidad++;
        endforeach;
        $this->cantidad_comunidad = $cantidad_comunidad;

        $obj_conflictos = new Conflicto();
        $this->conflictos = $obj_conflictos->getConflictosByTerritorioId($id);

        $fuente = new Fuente();
        $this->fuentes = $fuente->getListadoFuente('territorio', $obj_territorio->id);


        $redir_update = '';
        if ($obj_territorio->tipo == 'comunidad_negra') {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
            $this->redir_back = 'observatorio/territorios/listar_territorio_cn/';
            $redir_update = 'listar_territorio_cn';
        } elseif ($obj_territorio->tipo == 'indigena') {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
            $this->redir_back = 'observatorio/territorios/listar_territorio_ci/';
            $redir_update = 'listar_territorio_ci';
        } elseif ($obj_territorio->tipo == 'urbano') {
            $this->page_module = Territorio::TERRITORIO_URBANO;
            $this->redir_back = 'observatorio/territorios/listar_territorio_ur/';
            $redir_update = 'listar_territorio_ur';
        }


        $this->page_title = 'Actualizar Territorio: ' . $obj_territorio->nombre;
        $this->tab = $tab;
        $this->key = $key;
        $this->order = $order;
        $this->page = $page;
        //$this->url_redir_back = 'observatorio/territorios/'.$redir_update.'/'.$order.'/'.$page.'/';
        $this->url_redir_back = Session::get('url_back');
    }

    /**
     * Método para editar
     */
    public function editar_urbano($key, $tab, $order, $page)
    {

        if (!$id = Security::getKey($key, 'upd_territorio', 'int')) {
            return Redirect::toAction('listar_territorio_ur');
        }

        $this->territorio_id = $id;

        //Para saber que pestaña estara activa cuando visualice un territorio
        $this->tab_1_active = '';
        $this->tab_2_active = '';
        $this->tab_3_active = '';

        if ($tab == 1) {
            $this->tab_1_active = 'active';
        }
        if ($tab == 2) {
            $this->tab_2_active = 'active';
        }
        if ($tab == 3) {
            $this->tab_3_active = 'active';
        }

        /////


        $obj_territorio = new Territorio();
        if (!$obj_territorio->getTerritorioById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del territorio');
            return Redirect::toAction('listar_cn');
        }
        $this->obj_territorio = $obj_territorio;



        if (Input::hasPost('territorio')) {
            if (Territorio::setTerritorio('update', Input::post('territorio'), array('id' => $id))) {
                $data_e = Input::post('territorio');
                Flash::valid('El territorio se ha actualizado correctamente!');
                return Redirect::to(Session::get('url_back'));
            }
        }



        $obj_territorio_municipio = new TerritorioMunicipio();
        $this->ubicaciones = $obj_territorio_municipio->getDepartamentoMunicipioByTerritorioId($id);

        $obj_comunidades = new Comunidad();
        $this->comunidades = $obj_comunidades->getComunidadesByTerritorioId($id);
        $this->cantidad_comunidad = sizeof($this->comunidades);

        $obj_conflictos = new Conflicto();
        $this->conflictos = $obj_conflictos->getConflictosByTerritorioId($id);

        $redir_update = '';

        $this->page_module = Territorio::TERRITORIO_URBANO;
        $this->redir_back = 'observatorio/territorios/listar_territorio_ur/';
        $redir_update = 'listar_territorio_ur';

        $this->page_title = 'Actualizar Territorio: ' . $obj_territorio->nombre;
        $this->tab = $tab;
        $this->key = $key;
        $this->order = $order;
        $this->page = $page;
        //$this->url_redir_back = 'observatorio/territorios/'.$redir_update.'/'.$order.'/'.$page.'/';
        $this->url_redir_back = Session::get('url_back');
    }

    public function agregar_comunidad($territorio_id, $territorio_nombre, $tipo_territorio, $key_back, $order, $page)
    {
        $obj_comunidad = new Comunidad();
        if (Input::hasPost('comunidad')) {
            $obj_comunidad = Comunidad::setComunidad('create', Input::post('comunidad'), $territorio_nombre, array('estado' =>  Comunidad::ACTIVO));
            $comunidad_id = $obj_comunidad->id;
            Poblacion::setPoblacion('create', Input::post('poblacion'), 'comunidad_id', $comunidad_id);

            Flash::valid('La comunidad se ha registrado correctamente!');
            return Redirect::toAction('editar/' . $key_back . '/3/' . $order . '/' . $page . '/');
        }

        $this->url_redir_back = 'observatorio/territorios/editar/' . $key_back . '/3/' . $order . '/' . $page . '/';
        $this->territorio_id = $territorio_id;
        $this->page_title = 'Agregar comunidad a ' . $territorio_nombre;
        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_COMUNIDAD_NEGRA) {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_INDIGENA) {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $this->page_module = Territorio::TERRITORIO_URBANO;
        }
    }

    public function agregar_barrio($territorio_id, $territorio_nombre, $tipo_territorio, $key_back, $order, $page)
    {
        $obj_comunidad = new Comunidad();
        if (Input::hasPost('comunidad')) {
            $obj_comunidad = Comunidad::setComunidad('create', Input::post('comunidad'), $territorio_nombre, array('estado' =>  Comunidad::ACTIVO));
            /*
            $comunidad_id = $obj_comunidad->id;            
            Poblacion::setPoblacion('create', Input::post('poblacion'), 'comunidad_id', $comunidad_id); 
            */

            Flash::valid('El barrio se ha registrado correctamente!');
            return Redirect::toAction('editar_urbano/' . $key_back . '/2/' . $order . '/' . $page . '/');
        }

        $this->url_redir_back = 'observatorio/territorios/editar_urbano/' . $key_back . '/2/' . $order . '/' . $page . '/';
        $this->territorio_id = $territorio_id;
        $this->page_title = 'Agregar barrio a ' . $territorio_nombre;
        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_COMUNIDAD_NEGRA) {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_INDIGENA) {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $this->page_module = Territorio::TERRITORIO_URBANO;
        }
    }

    /**
     * Método para ver comunidad de un territorio
     */
    public function ver_comunidad($key, $tipo_territorio, $key_back, $order, $page)
    {

        if (!$id = Security::getKey($key, 'show_comunidad', 'int')) {
            return Redirect::toAction('listar');
        }

        $obj_comunidad = new Comunidad();
        if (!$obj_comunidad->getComunidadById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información de la comunidad');
            return Redirect::toAction('listar_cn');
        }

        $poblacion = new Poblacion();
        $poblacion = Poblacion::getPoblacion('comunidad_id', $id);
        $this->poblacion = $poblacion;

        $this->obj_comunidad = $obj_comunidad;
        $this->page_title = 'Información de la Comunidad del Territorio: ' . $obj_comunidad->territorio;

        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_COMUNIDAD_NEGRA) {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_INDIGENA) {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $this->page_module = Territorio::TERRITORIO_URBANO;
        }

        $this->key = $key;
        $this->key_back = $key_back;
        $this->url_redir_back = 'observatorio/territorios/ver/' . $key_back . '/3/' . $order . '/' . $page . '/';
    }


    /**
     * Método para editar comunidad de un territorio
     */
    public function editar_comunidad($key, $tipo_territorio, $key_back, $order, $page)
    {

        if (!$id = Security::getKey($key, 'upd_comunidad', 'int')) {
            return Redirect::toAction('listar');
        }

        $obj_comunidad = new Comunidad();
        if (!$obj_comunidad->getComunidadById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información de la comunidad');
            return Redirect::toAction('ver/' . $key_back . '/3/');
        }

        $poblacion = new Poblacion();
        $poblacion = Poblacion::getPoblacion('comunidad_id', $id);
        $this->poblacion = $poblacion;

        $this->obj_comunidad = $obj_comunidad;
        $this->page_title = 'Actualizar Comunidad del Territorio: ' . $obj_comunidad->territorio;

        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_COMUNIDAD_NEGRA) {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_INDIGENA) {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $this->page_module = Territorio::TERRITORIO_URBANO;
        }

        if (Input::hasPost('comunidad')) {
            $obj_comunidad = Comunidad::setComunidad('update', Input::post('comunidad'), $obj_comunidad->territorio, array('estado' =>  Comunidad::ACTIVO));
            $comunidad_id = $obj_comunidad->id;
            Poblacion::setPoblacion('update', Input::post('poblacion'), 'comunidad_id', $comunidad_id);

            Flash::valid('La comunidad se ha actualizado correctamente!');
            return Redirect::toAction('editar/' . $key_back . '/3/' . $order . '/' . $page . '/');
            //'observatorio/territorios/ver/'.$key_back.'/3/'
        }

        $this->key_back = $key_back;
        $this->key = $key;
        $this->url_redir_back = 'observatorio/territorios/editar/' . $key_back . '/3/' . $order . '/' . $page . '/';
    }

    public function editar_barrio($key, $tipo_territorio, $key_back, $order, $page)
    {

        if (!$id = Security::getKey($key, 'upd_comunidad', 'int')) {
            return Redirect::toAction('listar');
        }

        $obj_comunidad = new Comunidad();
        if (!$obj_comunidad->getComunidadById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del barrio');
            return Redirect::toAction('ver/' . $key_back . '/3/');
        }


        $this->obj_comunidad = $obj_comunidad;
        $this->page_title = 'Actualizar Barrio del Territorio: ' . $obj_comunidad->territorio;

        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_COMUNIDAD_NEGRA) {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_INDIGENA) {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $this->page_module = Territorio::TERRITORIO_URBANO;
        }

        if (Input::hasPost('comunidad')) {
            $obj_comunidad = Comunidad::setComunidad('update', Input::post('comunidad'), $obj_comunidad->territorio, array('estado' =>  Comunidad::ACTIVO));
            /*
            $comunidad_id = $obj_comunidad->id;            
            Poblacion::setPoblacion('update', Input::post('poblacion'), 'comunidad_id', $comunidad_id);    
            */
            Flash::valid('El barrio se ha actualizado correctamente!');
            return Redirect::toAction('editar_urbano/' . $key_back . '/2/' . $order . '/' . $page . '/');
            //'observatorio/territorios/ver/'.$key_back.'/3/'
        }

        $this->key_back = $key_back;
        $this->key = $key;
        $this->url_redir_back = 'observatorio/territorios/editar_urbano/' . $key_back . '/2/' . $order . '/' . $page . '/';
    }

    /**
     * Método para eliminar
     */
    public function eliminar_comunidad($nombre_territorio, $tipo_comunidad, $nombre_comunidad, $key, $key_back, $order, $page)
    {
        if (!$id = Security::getKey($key, 'del_comunidad', 'int')) {
            return Redirect::toAction($redireccionar . '/' . $order . '/' . $page . '/');
        }

        $comunidad = new Comunidad();

        try {
            if ($comunidad->delete($id)) {
                Flash::valid("La comunidad $nombre_comunidad se ha eliminado correctamente");
                DwAudit::warning("Se ha ELIMINADO la comunidad $nombre_comunidad, pertenecia al territorio $nombre_territorio.");
            } else {
                Flash::warning('Lo sentimos, pero esta comunidad no se puede eliminar.');
            }
        } catch (KumbiaException $e) {
            Flash::error('Esta comunidad no se puede eliminar porque se encuentra relacionado con otro registro.');
        }
        $toAction = 'editar/' . $key_back . '/3/' . $order . '/' . $page . '/';
        if ($tipo_comunidad === 'barrio') {
            $toAction = 'editar_urbano/' . $key_back . '/2/' . $order . '/' . $page . '/';
        }
        return Redirect::toAction($toAction);
    }



    public function agregar_conflicto($territorio_id, $territorio_nombre, $tipo_territorio, $key_back, $order, $page)
    {
        $obj_conflicto = new Conflicto();
        if (Input::hasPost('conflicto')) {
            $obj_conflicto = Conflicto::setConflicto('create', Input::post('conflicto'), $territorio_nombre, array('estado' =>  Conflicto::ACTIVO));
            $conflicto_id = $obj_conflicto->id;
            //Poblacion::setPoblacion('create', Input::post('poblacion'), 'conflicto_id', $conflicto_id);    

            Flash::valid('El conflicto se ha registrado correctamente!');
            $toAction = 'editar/' . $key_back . '/4/' . $order . '/' . $page . '/';
            if ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
                $toAction = 'editar_urbano/' . $key_back . '/3/' . $order . '/' . $page . '/';
            }
            return Redirect::toAction($toAction);
        }

        $this->url_redir_back = 'observatorio/territorios/editar/' . $key_back . '/4/' . $order . '/' . $page . '/';
        $this->territorio_id = $territorio_id;
        $this->page_title = 'Agregar conflicto al Territorio: ' . $territorio_nombre;
        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_COMUNIDAD_NEGRA) {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_INDIGENA) {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $this->page_module = Territorio::TERRITORIO_URBANO;
            $this->url_redir_back = 'observatorio/territorios/editar_urbano/' . $key_back . '/3/' . $order . '/' . $page . '/';
        }
    }

    /**
     * Método para ver conflicto de un territorio
     */
    public function ver_conflicto($key, $tipo_territorio, $key_back, $order, $page)
    {

        if (!$id = Security::getKey($key, 'show_conflicto', 'int')) {
            return Redirect::toAction('listar');
        }

        $obj_conflicto = new Conflicto();
        if (!$obj_conflicto->getConflictoById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del conflicto');
            return Redirect::toAction('listar_cn');
        }

        $this->conflicto = $obj_conflicto;
        $this->page_title = 'Información del Conflicto del Territorio: ' . $obj_conflicto->territorio;

        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_COMUNIDAD_NEGRA) {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_INDIGENA) {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $this->page_module = Territorio::TERRITORIO_URBANO;
        }

        $this->key = $key;
        $this->key_back = $key_back;
        $this->url_redir_back = 'observatorio/territorios/ver/' . $key_back . '/4/' . $order . '/' . $page . '/';
    }


    /**
     * Método para editar conflicto de un territorio
     */
    public function editar_conflicto($key, $tipo_territorio, $key_back, $order, $page)
    {

        if (!$id = Security::getKey($key, 'upd_conflicto', 'int')) {
            return Redirect::toAction('listar');
        }

        $obj_conflicto = new Conflicto();
        if (!$obj_conflicto->getConflictoById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del conflicto');
            return Redirect::toAction('ver/' . $key_back . '/4/');
        }


        $this->conflicto = $obj_conflicto;
        $this->page_title = 'Actualizar Conflicto del Territorio: ' . $obj_conflicto->territorio;

        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_COMUNIDAD_NEGRA) {
            $this->page_module = Territorio::TERRITORIO_COMUNIDAD_NEGRA;
        } elseif ($tipo_territorio == Territorio::TIPO_TERRITORIO_INDIGENA) {
            $this->page_module = Territorio::TERRITORIO_INDIGENA;
        }


        if (Input::hasPost('conflicto')) {
            $obj_conflicto = Conflicto::setConflicto('update', Input::post('conflicto'), $obj_conflicto->territorio, array('estado' =>  Conflicto::ACTIVO));
            //$conflicto_id = $obj_conflicto->id;          

            Flash::valid('El conflicto se ha actualizado correctamente!');
            $toAction = 'editar/' . $key_back . '/4/' . $order . '/' . $page . '/';
            if ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
                $toAction = 'editar_urbano/' . $key_back . '/3/' . $order . '/' . $page . '/';
            }
            return Redirect::toAction($toAction);
        }

        $this->key_back = $key_back;
        $this->key = $key;
        $this->url_redir_back = 'observatorio/territorios/editar/' . $key_back . '/4/' . $order . '/' . $page . '/';
        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $this->page_module = Territorio::TERRITORIO_URBANO;
            $this->url_redir_back = 'observatorio/territorios/editar_urbano/' . $key_back . '/3/' . $order . '/' . $page . '/';
        }
    }

    /**
     * Método para eliminar
     */
    public function eliminar_conflicto($nombre_territorio, $tipo_territorio, $nombre_conflicto, $key, $key_back, $order, $page)
    {
        if (!$id = Security::getKey($key, 'del_conflicto', 'int')) {
            return Redirect::toAction($redireccionar . '/' . $order . '/' . $page . '/');
        }

        $conflicto = new Conflicto();

        try {
            if ($conflicto->delete($id)) {
                Flash::valid("El conflicto $nombre_conflicto se ha eliminado correctamente");
                DwAudit::warning("Se ha ELIMINADO el conflicto $nombre_conflicto, pertenecia al territorio $nombre_territorio.");
            } else {
                Flash::warning('Lo sentimos, pero este conflicto no se puede eliminar.');
            }
        } catch (KumbiaException $e) {
            Flash::error('Este conflicto no se puede eliminar porque se encuentra relacionado con otro registro.');
        }

        $url_redir_back = 'editar/' . $key_back . '/4/' . $order . '/' . $page . '/';
        if ($tipo_territorio == Territorio::TIPO_TERRITORIO_URBANO) {
            $url_redir_back = 'editar_urbano/' . $key_back . '/3/' . $order . '/' . $page . '/';
        }

        return Redirect::toAction($url_redir_back);
    }

    /**
     * Método para subir documentos
     */
    public function upload($documento = '')
    {

        $upload = new DwUpload($documento, 'files/upload/territorio/resolucion/' . $documento);
        $upload->setAllowedTypes('pdf|doc|docx');
        //$upload->setAllowedTypes('*');
        $upload->setEncryptNameWithLogin(TRUE);
        //$upload->setSize('50MB', 170, 200, TRUE);
        if (!$data = $upload->save()) { //retorna un array('path'=>'ruta', 'name'=>'nombre.ext');
            $data = array('error' => TRUE, 'message' => $upload->getError());
        }
        sleep(1); //Por la velocidad del script no permite que se actualize el archivo
        $this->data = $data;
        View::json();
    }

    /**
     * Método para eliminar
     */
    public function eliminar($key, $redireccionar, $order, $page)
    {
        if (!$id = Security::getKey($key, 'eliminar_territorio', 'int')) {
            return Redirect::toAction($redireccionar . '/' . $order . '/' . $page . '/');
        }

        $territorio = new Territorio();
        if (!$territorio->find_first($id)) {
            Flash::error('Lo sentimos, no se ha podido establecer la información del territorio');
            return Redirect::toAction($redireccionar . '/' . $order . '/' . $page . '/');
        }
        try {
            //            $titulado_si = new TituladoSi();
            //            $titulado_no = new TituladoNo();
            //            $poblacion = new Poblacion();
            //            $territorio_municipio = new TerritorioMunicipio();
            //            
            //            $titulado_si->delete_all("territorio_id = $id");
            //            $titulado_no->delete_all("territorio_id = $id");
            //            $poblacion->delete_all("territorio_id = $id");
            //            $territorio_municipio->delete_all("territorio_id = $id");

            if ($territorio->delete()) {
                Flash::valid('El territorio se ha eliminado correctamente!');
                DwAudit::warning("Se ha ELIMINADO el territorio $territorio->nombre.");
            } else {
                Flash::warning('Lo sentimos, pero este territorio no se puede eliminar.');
            }
        } catch (KumbiaException $e) {
            Flash::error('Este territorio no se puede eliminar porque se encuentra relacionado con otro registro.');
        }

        return Redirect::toAction($redireccionar . '/' . $order . '/' . $page . '/');
    }

    public function generar_pdf($id)
    {
        View::select(null, null);
        $pdf = new PDF();

        $this->territorio_id = $id;
        $obj_territorio = new Territorio();
        if (!$obj_territorio->getTerritorioById($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del territorio');
            return Redirect::toAction('listar_cn');
        }
        $this->obj_territorio = $obj_territorio;
        $obj_territorio_municipio = new TerritorioMunicipio();
        $ubicaciones = $obj_territorio_municipio->getDepartamentoMunicipioByTerritorioId($id);

        // Load::lib('fpdf182/fpdf');
        $data2 = array();
        $contador = 0;
        foreach ($ubicaciones as $ubicacion) {
            $array = array("$ubicacion->subregion_nombre", "$ubicacion->departamento_nombre", "$ubicacion->municipio_nombre");
            $data2[$contador] = $array;
            $contador++;
        }
        $data = array();

        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 14);
        if ($obj_territorio->tipo === 'comunidad_negra') {
            $pdf->WriteHTML('<b>Territorio Colectivo Comunidades Negras</b>');
        } elseif ($obj_territorio->tipo === 'indigena') {
            $pdf->WriteHTML('<b>Territorio Colectivo Resguardos Indigenas</b>');
        } elseif ($obj_territorio->tipo === 'urbano') {
            $pdf->WriteHTML('<b>Territorio Urbano</b>');
        }
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 12);
        $pdf->WriteHTML('<b>Nombre del territorio:</b> ' . $obj_territorio->nombre);
        $pdf->Ln();
        $pdf->WriteHTML('<b>Ubicación del territorio</b>');
        $pdf->Ln();
        // Títulos de las columnas
        $header = array('Subregión', 'Departamento', 'Municipio');
        $pdf->BasicTable($header, $data2, 60, 15);
        $pdf->Ln();
        $pdf->WriteHTML('<b>¿Territorio titulado?: </b>' . $obj_territorio->titulado);
        $pdf->Ln();
        if ($obj_territorio->titulado == 'SI') {
            $obj_titulado_si = new TituladoSi();
            $obj_titulado_si->getTituladoSiByTerritorioId($id);
            $pdf->WriteHTML('<b>Resolución de constitución #:</b> ' . $obj_titulado_si->resolucion_constitucion);
            $pdf->Ln(5);
            $pdf->WriteHTML('<b>Área titulada: </b>' . $obj_titulado_si->area_titulada . ' ha');
            $pdf->Ln(5);
            $pdf->WriteHTML('<b>Límite / Lindero: </b>' . $obj_titulado_si->limite_lindero);
            $pdf->Ln(7);
            $documento_constitucion = '<b>Documento constitución:</b> <a href="http://dbpacifico.creando.net/files/upload/territorio/resolucion/documento_constitucion/"' . $obj_titulado_si->documento_constitucion . '>' . $obj_titulado_si->documento_constitucion . '</a>';
            $pdf->WriteHTML($documento_constitucion);
            $pdf->Ln(7);
            $pdf->SetFont('Arial', '', 14);
            $pdf->WriteHTML('<b><u>Solicitud de ampliación</u></b>');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Ln(5);
            $pdf->WriteHTML('<b>¿Tiene solicitud de ampliación?:</b> ' . $obj_titulado_si->solicitud_ampliacion_si_no);
            if ($obj_titulado_si->solicitud_ampliacion_si_no == 'SI') {
                $pdf->Ln(5);
                $pdf->WriteHTML('<b>Fecha de la solicitud: </b>' . $obj_titulado_si->solicitud_ampliacion_fecha);
                $pdf->Ln();
                $pdf->WriteHTML('<b>Resolución: </b>' . $obj_titulado_si->solicitud_ampliacion_resolucion);
                $pdf->Ln();
                $pdf->WriteHTML('<b>Área solicitada: </b>' . $obj_titulado_si->solicitud_ampliacion_area . ' ha');
                $pdf->Ln();
                $pdf->WriteHTML('<b>Límite / Lindero:</b> ' . $obj_titulado_si->solicitud_ampliacion_lindero_limite);
                $pdf->Ln();
                $pdf->WriteHTML('<b>Observaciones:</b> ' . $obj_titulado_si->solicitud_ampliacion_observacion);
                $pdf->Ln();
                $documento_ampliacion = '<b>Documento solicitud ampliación:</b> <a href="http://dbpacifico.creando.net/files/upload/territorio/resolucion/documento_ampliacion/"' . $obj_titulado_si->solicitud_ampliacion_documento . '>' . $obj_titulado_si->solicitud_ampliacion_documento . '</a>';
                $pdf->WriteHTML($documento_ampliacion);
            }

            $pdf->Ln(7);
            $pdf->SetFont('Arial', '', 14);
            $pdf->WriteHTML('<b><u>Solicitud de saneamiento</u></b>');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Ln(5);
            $pdf->WriteHTML('<b>¿Tiene solicitud de saneamiento?:</b> ' . $obj_titulado_si->solicitud_saneamiento_si_no);
            if ($obj_titulado_si->solicitud_saneamiento_si_no == 'SI') {
                $pdf->Ln(5);
                $pdf->WriteHTML('<b>Fecha de la solicitud: </b>' . $obj_titulado_si->solicitud_saneamiento_fecha);
                $pdf->Ln();
                $pdf->WriteHTML('<b>Resolución: </b>' . $obj_titulado_si->solicitud_saneamiento_resolucion);
                $pdf->Ln();
                $pdf->WriteHTML('<b>Área solicitada: </b>' . $obj_titulado_si->solicitud_saneamiento_area . ' ha');
                $pdf->Ln();
                $pdf->WriteHTML('<b>Límite / Lindero:</b> ' . $obj_titulado_si->solicitud_saneamiento_lindero_limite);
                $pdf->Ln();
                $pdf->WriteHTML('<b>Observaciones:</b> ' . $obj_titulado_si->solicitud_saneamiento_observacion);
                $pdf->Ln();
                $documento_saneamiento = '<b>Documento solicitud saneamiento:</b> <a href="http://dbpacifico.creando.net/files/upload/territorio/resolucion/documento_saneamiento/"' . $obj_titulado_si->solicitud_saneamiento_documento . '>' . $obj_titulado_si->solicitud_saneamiento_documento . '</a>';
                $pdf->WriteHTML($documento_saneamiento);
            }
        } elseif ($obj_territorio->titulado == 'NO') {
            $obj_titulado_no = new TituladoNo();
            $obj_titulado_no->getTituladoNoByTerritorioId($id);
            $pdf->SetFont('Arial', '', 14);
            $pdf->WriteHTML('<b><u>Solicitud de título</u></b>');
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 12);
            $pdf->WriteHTML('<b>Fecha de la solicitud: </b>' . $obj_titulado_no->solicitud_titulo_fecha);
            $pdf->Ln();
            $pdf->WriteHTML('<b>Área solicitada: </b>' . $obj_titulado_no->solicitud_titulo_area . ' ha');
            $pdf->Ln();
            $pdf->WriteHTML('<b>Límite / Lindero:</b> ' . $obj_titulado_no->solicitud_titulo_limite_lindero);
            $pdf->Ln();
            $pdf->WriteHTML('<b>Observaciones:</b> ' . $obj_titulado_no->solicitud_titulo_observacion);
            $pdf->Ln();
            $documento_solicitud_titulo = '<b>Documento solicitud título:</b> <a href="http://dbpacifico.creando.net/files/upload/territorio/resolucion/documento_solicitud_titulo/"' . $obj_titulado_no->documento_solicitud_titulo . '>' . $obj_titulado_no->documento_solicitud_titulo . '</a>';
            $pdf->WriteHTML($documento_solicitud_titulo);
        }

        $poblacion = new Poblacion();
        $poblacion = Poblacion::getPoblacion('territorio_id', $id);

        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 14);
        $pdf->WriteHTML('<b><u>POBLACIÓN</u></b>');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(7);
        $pdf->WriteHTML('<b> Total: </b>' . $poblacion->total);
        $pdf->Ln();
        $pdf->WriteHTML('<b> Mujeres: </b>' . $poblacion->mujer);
        $pdf->Ln();
        $pdf->WriteHTML('<b> Hombres: </b>' . $poblacion->hombre);
        $pdf->Ln();
        $pdf->WriteHTML('<b> Número de familias: </b>' . $poblacion->numero_familia);
        $pdf->Ln();
        $pdf->WriteHTML('<b> Cantidad de niños con registro civil en edad de primera infancia: </b>' . $obj_territorio->cant_ninos_primera_inf);
        $pdf->Ln();
        $pdf->WriteHTML('<b> Cantidad de niños sin registro civil en edad de primera infancia: </b>' . $obj_territorio->cant_sin_ninos_primera_inf);
        $pdf->Ln(7);


        $header_tabla_poblacion = array('Rango edad (años)', 'Mujeres', 'Hombres', 'Total');
        $array_rango_edad = array();
        $e0_4 = array("0 - 4", "$poblacion->m_e0_4", "$poblacion->h_e0_4", "$poblacion->e0_4");
        $array_rango_edad[0] = $e0_4;
        $e5_9 = array("5 - 9", "$poblacion->m_e5_9", "$poblacion->h_e5_9", "$poblacion->e5_9");
        $array_rango_edad[1] = $e5_9;
        $e10_14 = array("10 - 14", "$poblacion->m_e10_14", "$poblacion->h_e10_14", "$poblacion->e10_14");
        $array_rango_edad[2] = $e10_14;
        $e15_19 = array("15 - 19", "$poblacion->m_e15_19", "$poblacion->h_e15_19", "$poblacion->e15_19");
        $array_rango_edad[3] = $e15_19;
        $e20_24 = array("20 - 24", "$poblacion->m_e20_24", "$poblacion->h_e20_24", "$poblacion->e20_24");
        $array_rango_edad[4] = $e20_24;
        $e25_29 = array("25 - 29", "$poblacion->m_e25_29", "$poblacion->h_e25_29", "$poblacion->e25_29");
        $array_rango_edad[5] = $e25_29;
        $e30_34 = array("30 - 34", "$poblacion->m_e30_34", "$poblacion->h_e30_34", "$poblacion->e30_34");
        $array_rango_edad[6] = $e30_34;
        $e35_39 = array("35 - 39", "$poblacion->m_e35_39", "$poblacion->h_e35_39", "$poblacion->e35_39");
        $array_rango_edad[7] = $e35_39;
        $e40_44 = array("40 - 44", "$poblacion->m_e40_44", "$poblacion->h_e40_44", "$poblacion->e40_44");
        $array_rango_edad[8] = $e40_44;
        $e45_49 = array("45 - 49", "$poblacion->m_e45_49", "$poblacion->h_e45_49", "$poblacion->e45_49");
        $array_rango_edad[9] = $e45_49;
        $e50_54 = array("50 - 54", "$poblacion->m_e50_54", "$poblacion->h_e50_54", "$poblacion->e50_54");
        $array_rango_edad[10] = $e50_54;
        $e55_59 = array("55 - 59", "$poblacion->m_e55_59", "$poblacion->h_e55_59", "$poblacion->e55_59");
        $array_rango_edad[11] = $e55_59;
        $e60_64 = array("60 - 64", "$poblacion->m_e60_64", "$poblacion->h_e60_64", "$poblacion->e60_64");
        $array_rango_edad[12] = $e60_64;
        $e65_69 = array("65 - 69", "$poblacion->m_e65_69", "$poblacion->h_e65_69", "$poblacion->e65_69");
        $array_rango_edad[13] = $e65_69;
        $e70_74 = array("70 - 74", "$poblacion->m_e70_74", "$poblacion->h_e70_74", "$poblacion->e70_74");
        $array_rango_edad[14] = $e70_74;
        $e75_79 = array("75 - 79", "$poblacion->m_e75_79", "$poblacion->h_e75_79", "$poblacion->e75_79");
        $array_rango_edad[15] = $e75_79;
        $e80_mas = array("80 ó mas", "$poblacion->m_e80_mas", "$poblacion->h_e80_mas", "$poblacion->e80_mas");
        $array_rango_edad[16] = $e80_mas;
        $pdf->BasicTable($header_tabla_poblacion, $array_rango_edad, 40, 15);

        /************************* */
        /************************* */

        $obj_comunidades = new Comunidad();
        $comunidades = $obj_comunidades->getComunidadesByTerritorioId($id);

        $cantidad_comunidad = 0;
        foreach ($comunidades as $registros) :
            $cantidad_comunidad++;
        endforeach;

        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 14);
        $pdf->WriteHTML('<b><u>COMUNIDADES</u></b>');
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 12);
        $pdf->WriteHTML("<b>Cantidad de comunidades: </b>$cantidad_comunidad");
        $pdf->Ln(7);
        foreach ($comunidades as $comunidad) :
            $pdf->WriteHTML("   <b><u>$comunidad->nombre</u></b>");
            $pdf->Ln();
            $pdf->WriteHTML("       <b>Fecha de creación: </b>$comunidad->fecha_creacion");
            $pdf->Ln();
            $pdf->WriteHTML("       <b>Fecha de disolución: </b>$comunidad->fecha_disolucion");
            $pdf->Ln();
            $pdf->WriteHTML("       <b>Descripción de la ubicación geográfica de la comunidad</b>");
            $pdf->Ln();
            $pdf->SetX(25);
            $pdf->MultiCell(0, 5, utf8_decode($comunidad->descripcion_ubicacion));
            $pdf->Ln();
            $poblacion = Poblacion::getPoblacion('comunidad_id', $comunidad->id);
            $pdf->WriteHTML('       <b><u>Población</u></b>');
            $pdf->Ln(7);
            $pdf->WriteHTML('       <b>Total: </b>' . $poblacion->total);
            $pdf->Ln();
            $pdf->WriteHTML('       <b>Mujeres: </b>' . $poblacion->mujer);
            $pdf->Ln();
            $pdf->WriteHTML('       <b>Hombres: </b>' . $poblacion->hombre);
            $pdf->Ln();
            $pdf->WriteHTML('       <b>Número de familias: </b>' . $poblacion->numero_familia);
            $pdf->Ln();
            $array_rango_edad = array();
            $e0_4 = array("0 - 4", "$poblacion->m_e0_4", "$poblacion->h_e0_4", "$poblacion->e0_4");
            $array_rango_edad[0] = $e0_4;
            $e5_9 = array("5 - 9", "$poblacion->m_e5_9", "$poblacion->h_e5_9", "$poblacion->e5_9");
            $array_rango_edad[1] = $e5_9;
            $e10_14 = array("10 - 14", "$poblacion->m_e10_14", "$poblacion->h_e10_14", "$poblacion->e10_14");
            $array_rango_edad[2] = $e10_14;
            $e15_19 = array("15 - 19", "$poblacion->m_e15_19", "$poblacion->h_e15_19", "$poblacion->e15_19");
            $array_rango_edad[3] = $e15_19;
            $e20_24 = array("20 - 24", "$poblacion->m_e20_24", "$poblacion->h_e20_24", "$poblacion->e20_24");
            $array_rango_edad[4] = $e20_24;
            $e25_29 = array("25 - 29", "$poblacion->m_e25_29", "$poblacion->h_e25_29", "$poblacion->e25_29");
            $array_rango_edad[5] = $e25_29;
            $e30_34 = array("30 - 34", "$poblacion->m_e30_34", "$poblacion->h_e30_34", "$poblacion->e30_34");
            $array_rango_edad[6] = $e30_34;
            $e35_39 = array("35 - 39", "$poblacion->m_e35_39", "$poblacion->h_e35_39", "$poblacion->e35_39");
            $array_rango_edad[7] = $e35_39;
            $e40_44 = array("40 - 44", "$poblacion->m_e40_44", "$poblacion->h_e40_44", "$poblacion->e40_44");
            $array_rango_edad[8] = $e40_44;
            $e45_49 = array("45 - 49", "$poblacion->m_e45_49", "$poblacion->h_e45_49", "$poblacion->e45_49");
            $array_rango_edad[9] = $e45_49;
            $e50_54 = array("50 - 54", "$poblacion->m_e50_54", "$poblacion->h_e50_54", "$poblacion->e50_54");
            $array_rango_edad[10] = $e50_54;
            $e55_59 = array("55 - 59", "$poblacion->m_e55_59", "$poblacion->h_e55_59", "$poblacion->e55_59");
            $array_rango_edad[11] = $e55_59;
            $e60_64 = array("60 - 64", "$poblacion->m_e60_64", "$poblacion->h_e60_64", "$poblacion->e60_64");
            $array_rango_edad[12] = $e60_64;
            $e65_69 = array("65 - 69", "$poblacion->m_e65_69", "$poblacion->h_e65_69", "$poblacion->e65_69");
            $array_rango_edad[13] = $e65_69;
            $e70_74 = array("70 - 74", "$poblacion->m_e70_74", "$poblacion->h_e70_74", "$poblacion->e70_74");
            $array_rango_edad[14] = $e70_74;
            $e75_79 = array("75 - 79", "$poblacion->m_e75_79", "$poblacion->h_e75_79", "$poblacion->e75_79");
            $array_rango_edad[15] = $e75_79;
            $e80_mas = array("80 ó mas", "$poblacion->m_e80_mas", "$poblacion->h_e80_mas", "$poblacion->e80_mas");
            $array_rango_edad[16] = $e80_mas;
            $pdf->BasicTable($header_tabla_poblacion, $array_rango_edad, 40, 20);
            $pdf->Ln(7);
        endforeach;

        /************************* */
        /************************* */

        $pdf->SetFont('Arial', '', 14);
        $pdf->WriteHTML('<b><u>CONFLICTOS</u></b>');
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 12);
        $obj_conflictos = new Conflicto();
        $conflictos = $obj_conflictos->getConflictosByTerritorioId($id);
        foreach ($conflictos as $conflicto) :
            $pdf->WriteHTML("   <b><u>$conflicto->nombre</u></b>");
            $pdf->Ln();

            $cadena_tipo_conflicto = '';
            if ($conflicto->inter_etnico == 1) {
                $cadena_tipo_conflicto .= "Inter étnico, ";
            }
            if ($conflicto->intra_etnico == 1) {
                $cadena_tipo_conflicto .= "Intra étnico, ";
            }
            if ($conflicto->cultural == 1) {
                $cadena_tipo_conflicto .= "Cultural, ";
            }
            if ($conflicto->economico == 1) {
                $cadena_tipo_conflicto .= "Económico, ";
            }

            if ($conflicto->recurso_natural == 1) {
                $cadena_tipo_conflicto .= "Recursos naturales, ";
            }
            if ($conflicto->politico_violencia == 1) {
                $cadena_tipo_conflicto .= "Político - Violencia, ";
            }
            if ($conflicto->politico_grupo_armado == 1) {
                $cadena_tipo_conflicto .= "Político - Presencia de grupos armados, ";
            }
            if ($conflicto->politico_electoral == 1) {
                $cadena_tipo_conflicto .= "Político - Electoral, ";
            }

            if ($conflicto->territorial_uso == 1) {
                $cadena_tipo_conflicto .= "Territorial - Uso, ";
            }
            if ($conflicto->territorial_delimitacion == 1) {
                $cadena_tipo_conflicto .= "Territorial - Delimitación, ";
            }
            if ($conflicto->otros == 1) {
                $cadena_tipo_conflicto .= "Otro";
            }
            $pdf->SetX(20);
            $pdf->WriteHTML("<b>Tipo de conflicto:</b> " . $cadena_tipo_conflicto);
            $pdf->Ln(7);

            $pdf->SetX(20);
            $pdf->WriteHTML("<b>Descripción del conflicto</b> ");
            $pdf->Ln();
            $pdf->SetX(25);
            $pdf->WriteHTML("<b>Hechos</b> ");
            $pdf->Ln();
            $pdf->SetX(30);
            $pdf->MultiCell(0, 5, utf8_decode($conflicto->hechos));
            $pdf->Ln(1);
            $pdf->SetX(25);
            $pdf->WriteHTML("<b>Actores</b> ");
            $pdf->Ln();
            $pdf->SetX(30);
            $pdf->MultiCell(0, 5, utf8_decode($conflicto->actores));
            $pdf->Ln(1);
            $pdf->SetX(25);
            $pdf->WriteHTML("<b>Ubicación</b> ");
            $pdf->Ln();
            $pdf->SetX(30);
            $pdf->MultiCell(0, 5, utf8_decode($conflicto->ubicacion));
            $pdf->Ln(1);
            $pdf->SetX(25);
            $pdf->WriteHTML("<b>Observaciones</b> ");
            $pdf->Ln();
            $pdf->SetX(30);
            $pdf->MultiCell(0, 5, utf8_decode($conflicto->observacion));
            $pdf->Ln();

            $pdf->SetX(20);
            $pdf->WriteHTML("<b>Estado del conflicto: </b>$conflicto->estado_conflicto");
            $pdf->Ln();
            $pdf->SetX(20);
            $pdf->WriteHTML("<b>Fecha comienzo del conflicto: </b>$conflicto->fecha_comienzo");
            $pdf->Ln();
            $pdf->SetX(20);
            $pdf->WriteHTML("<b>Fecha terminación del conflicto: </b>$conflicto->fecha_fin");
            $pdf->Ln();
        endforeach;




        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(7);


        $fuentes = new Fuente();
        $obj_fuente = $fuentes->getListadoFuente('territorio', $obj_territorio->id);
        $pdf->SetFont('Arial', '', 14);
        $pdf->WriteHTML('<b><u>FUENTE(S) DE LA INFORMACIÓN</u></b>');
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 12);

        foreach ($obj_fuente as $fuente) :
            $pdf->SetX(15);
            $pdf->MultiCell(0, 5, utf8_decode($fuente->nombre . " / fecha de la fuente: " . $fuente->fecha));
            $pdf->Ln();
        endforeach;




        //$pdf->Cell(40,10, utf8_decode('¡Hola, Mundo!'));
        //$pdf = new PDF();

        /*
$pdf->AddPage();
$pdf->ImprovedTable($header,$data);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
*/
        $pdf->Output();
    }
}
