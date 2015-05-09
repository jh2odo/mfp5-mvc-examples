<?php

/**
 * Class NoticiasModelo
 */
class NoticiasModelo extends ModeloBase
{

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (Cargador::cargar("Configuracion")->CACHE === TRUE) {
            parent::__construct(TRUE, TRUE); // Habilitamos la cache en el modelo
        } else {
            parent::__construct(TRUE, FALSE);
        }
    }

    /**
     * Obtenemos un listado de las secciones
     *
     * @return array $secciones secciones
     */
    public function listadoSecciones()
    {

        $secciones = array();
        $consulta = $this->bd->ejecutar("SELECT id_seccion as id,titulo_seccion as titulo, estado_seccion as estado, subseccion_seccion as subseccion
										 FROM seccion 
										 ORDER BY subseccion_seccion ASC");
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            $cont = 0;
            for ($i = 0; $i < $this->bd->numeroFilas(); $i++) {
                $tmp = $this->bd->getFila();
                if ($tmp["id"] != 0) {
                    $secciones[$cont] = $tmp;
                    $cont++;
                }
            }
            unset($consulta);
        }

        $tmp = array();
        $total = count($secciones);
        for ($index = 0; $index < $total; $index++) {
            if ($secciones[$index]["subseccion"] == 0) {
                $tmp[$secciones[$index]["id"]] = $secciones[$index];
            } else {
                $tmp[$secciones[$index]["subseccion"]]["secciones"][] = $secciones[$index];
            }
        }
        $secciones = $tmp;

        $secciones = $this->ordenarSecciones($secciones);

        //	usort($secciones, "cmptitulete");

        return $secciones;
    }

    /**
     * Obtenemos un listado con todos los medios(activos o no)
     *
     * @return array $medios medios
     */
    public function listadoMedios()
    {

        $medios = array();
        $consulta = $this->bd->ejecutar("SELECT id_medio as id, titulo_medio as titulo, url_medio as url, estado_medio as estado, tipo_medio as tipo, nombre_pais as pais
											 FROM medio, pais   
											 WHERE id_pais = id_pais_medio 
											 ORDER BY titulo_medio ASC");
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            for ($i = 0; $i < $this->bd->numeroFilas(); $i++) {
                $medios[$i] = $this->bd->getFila();
            }
            unset($consulta);
        }

        return $medios;
    }

    /**
     * @return array|bool
     */
    public function listadoSeccionesEspecial()
    {

        $secciones = array();
        $consulta = $this->bd->ejecutar("SELECT id_seccion as id,titulo_seccion as titulo, estado_seccion as estado, subseccion_seccion as seccion
										 FROM seccion 
										 ORDER BY subseccion_seccion ASC");
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            for ($i = 0; $i < $this->bd->numeroFilas(); $i++) {
                $secciones[$i] = $this->bd->getFila();
            }
            unset($consulta);
        }


        $tmp = array();
        $total = count($secciones);
        for ($index = 0; $index < $total; $index++) {
            $tmp[$secciones[$index]["id"]] = $secciones[$index];
            $tmp[$secciones[$index]["id"]]["seccion"] = $tmp[$secciones[$index]["seccion"]]["titulo"];
        }
        $secciones = $tmp;

        /*
            function cmpTitulo($a, $b){
                $a = strtolower($a["titulo"]);
                $b = strtolower($b["titulo"]);
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            }

            usort($secciones, "cmpTitulo");
            */

        $secciones = $this->ordenarSecciones($secciones);

        return $secciones;
    }

    /**
     * @return array
     */
    public function listadoPaises()
    {

        $paises = array();
        $consulta = $this->bd->ejecutar("SELECT id_pais as id, codigo_pais as codigo, nombre_pais as nombre
										FROM pais ORDER BY nombre_pais ASC");
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            for ($i = 0; $i < $this->bd->numeroFilas(); $i++) {
                $paises[$i] = $this->bd->getFila();
            }
            unset($consulta);
        }

        return $paises;
    }

    /**
     * @param $nombre
     * @return bool
     */
    public function obtenerPais($nombre)
    {

        $pais = FALSE;
        $SQL = "SELECT id_pais as id, codigo_pais as codigo, nombre_pais as nombre
				FROM pais WHERE nombre_pais = '%s' LIMIT 1";

        $pais = $this->bd->ejecutar($SQL, array(0 => $nombre));
        if ($pais !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $pais = $this->bd->getFila();
            } else {
                $pais = FALSE;
            }
        }

        return $pais;
    }

    /**
     * @param $id
     * @return bool
     */
    public function obtenerMedioPorId($id)
    {

        $medio = FALSE;
        $SQL = "SELECT id_medio as id, titulo_medio as titulo, url_medio as url, estado_medio as estado, tipo_medio as tipo, nombre_pais as pais
			    FROM medio, pais 
			    WHERE id_pais = id_pais_medio 
			    AND id_medio = '%d' LIMIT 1";

        $medio = $this->bd->ejecutar($SQL, array(0 => $id));
        if ($medio !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $medio = $this->bd->getFila();
            } else {
                $medio = FALSE;
            }
        }

        return $medio;
    }

    /**
     * @param $id
     * @return bool
     */
    public function obtenerSeccionPorId($id)
    {

        $seccion = FALSE;
        $SQL = "SELECT id_seccion as id,titulo_seccion as titulo, estado_seccion as estado, subseccion_seccion as seccion
				FROM seccion 
			    WHERE id_seccion = '%d' LIMIT 1";

        $seccion = $this->bd->ejecutar($SQL, array(0 => $id));
        if ($seccion !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $seccion = $this->bd->getFila();
            } else {
                $seccion = FALSE;
            }
        }

        return $seccion;
    }

    /**
     * @param $titulo
     * @return bool
     */
    public function obtenerSeccionPorTitulo($titulo)
    {

        $seccion = FALSE;
        $SQL = "SELECT id_seccion as id,titulo_seccion as titulo, estado_seccion as estado, subseccion_seccion as seccion
				FROM seccion 
			    WHERE titulo_seccion = '%s' LIMIT 1";

        $seccion = $this->bd->ejecutar($SQL, array(0 => $titulo));
        if ($seccion !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $seccion = $this->bd->getFila();
            } else {
                $seccion = FALSE;
            }
        }

        return $seccion;
    }

    /**
     * @param $titulo
     * @return bool
     */
    public function obtenerMedioPorTitulo($titulo)
    {

        $medio = FALSE;
        $SQL = "SELECT id_medio as id, titulo_medio as titulo, url_medio as url, estado_medio as estado, tipo_medio as tipo, nombre_pais as pais
			    FROM medio, pais 
			    WHERE id_pais = id_pais_medio 
			    AND titulo_medio = '%s' LIMIT 1";

        $medio = $this->bd->ejecutar($SQL, array(0 => $titulo));
        if ($medio !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $medio = $this->bd->getFila();
            } else {
                $medio = FALSE;
            }
        }

        return $medio;
    }

    // Solo modo edicion
    /**
     * @return array
     */
    public function listadoFuentes()
    {
        $fuentes = array();
        $consulta = $this->bd->ejecutar("SELECT fuente.id_fuente as id, fuente.titulo_fuente as titulo, fuente.url_fuente as url, fuente.fecha_alta_fuente as fecha, fuente.estado_fuente as estado, fuente.tipo_fuente as tipo,
									     medio.titulo_medio as medio, seccion.titulo_seccion as seccion 
									     FROM fuente,medio,seccion  
									     WHERE fuente.id_medio_fuente = medio.id_medio 
									     AND fuente.id_seccion_fuente = seccion.id_seccion  
									     ORDER BY medio.titulo_medio ASC");
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            for ($i = 0; $i < $this->bd->numeroFilas(); $i++) {
                $fuentes[$i] = $this->bd->getFila();
            }
            unset($consulta);
        }

        return $fuentes;
    }

    /**
     * Obtenemos un listado con las fuentes de una seccion
     *
     * @return array $fuentes fuentes
     * @param string $seccion seccion
     */
    public function listadoFuentesPorSeccion($seccion)
    {

        $fuentes = array();
        $consulta = $this->bd->ejecutar("SELECT fuente.id_fuente as id, medio.titulo_medio as titulo, fuente.url_fuente as url,fuente.estado_fuente as estado, fuente.tipo_fuente as tipo
											 FROM fuente,medio,seccion 
											 WHERE fuente.id_medio_fuente = medio.id_medio 
											 AND fuente.id_seccion_fuente = seccion.id_seccion
                                             AND fuente.estado_fuente = 'activo'
											 AND medio.estado_medio = 'activo' 
											 AND seccion.estado_seccion = 'activo' 
											 AND seccion.titulo_seccion = '%s'",
            array(0 => $seccion));
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            $total = $this->bd->numeroFilas();
            for ($i = 0; $i < $total; $i++) {
                $fila = $this->bd->getFila();
                $fuentes[$i] = $fila;
            }
            unset($consulta);
        }
        return $fuentes;
    }

    /**
     * Total de Secciones
     *
     * @return integer numero total de secciones
     */
    public function totalSecciones()
    {
        $consulta = $this->bd->ejecutar("SELECT id_seccion FROM seccion");
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            return $this->bd->numeroFilas();
        }
        return 0;
    }

    /**
     * Total de Fuentes
     *
     * @return integer numero total de fuentes
     */
    public function totalFuentes()
    {
        $consulta = $this->bd->ejecutar("SELECT id_fuente FROM fuente");
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            return $this->bd->numeroFilas();
        }
        return 0;
    }

    /**
     * Total de medios
     *
     * @return integer numero total de medios
     */
    public function totalMedios()
    {
        $consulta = $this->bd->ejecutar("SELECT id_medio FROM medio");
        if ($consulta !== FALSE) {
            $this->bd->rebobinar();
            return $this->bd->numeroFilas();
        }
        return 0;
    }

    /**
     * Obtenemos la informacion de la seccion.
     *
     * @return mixed $seccion La seccion
     * @param string $seccion Seccion
     */
    public function obtenerSeccion($sec)
    {

        $seccion = FALSE;
        $seccion = $this->bd->ejecutar("SELECT id_seccion as id, titulo_seccion as titulo, estado_seccion as estado
										 FROM seccion 
										 WHERE titulo_seccion = '%s'", array(0 => $sec));
        if ($seccion !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $seccion = $this->bd->getFila();
            } else {
                $seccion = FALSE;
            }
        }

        //print_r($seccion);
        //exit;

        return $seccion;
    }

    /**
     * Obtenemos la informacion de la fuente.
     *
     * @return mixed $fuente La fuente
     * @param string $medio Medio de la fuente
     * @param string $seccion Seccion de la fuente
     */
    public function obtenerFuente($medio, $seccion)
    {

        $fuente = FALSE;
        $fuente = $this->bd->ejecutar("SELECT fuente.id_fuente as id, medio.titulo_medio as titulo, fuente.url_fuente as url,fuente.estado_fuente as estado, fuente.tipo_fuente as tipo
											FROM fuente,medio,seccion  
											WHERE fuente.id_medio_fuente = medio.id_medio 
											AND fuente.id_seccion_fuente = seccion.id_seccion 
											AND medio.estado_medio = 'activo' 
											AND seccion.estado_seccion = 'activo' 
											AND seccion.titulo_seccion = '%s' 
											AND medio.titulo_medio = '%s'",
            array(0 => $seccion, 1 => $medio));
        if ($fuente !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $fuente = $this->bd->getFila();
            } else {
                $fuente = FALSE;
            }
        }

        return $fuente;
    }

    // Utilizado unicamente en edición: XMLRPC
    /**
     * @param $id
     * @return bool
     */
    public function obtenerFuentePorId($id)
    {

        $fuente = FALSE;
        $fuente = $this->bd->ejecutar("SELECT fuente.id_fuente as id, fuente.titulo_fuente as titulo, fuente.url_fuente as url, fuente.fecha_alta_fuente as fecha, fuente.estado_fuente as estado, fuente.tipo_fuente as tipo,
									   medio.titulo_medio as medio, seccion.titulo_seccion as seccion 
									   FROM fuente,medio,seccion  
									   WHERE fuente.id_medio_fuente = medio.id_medio 
									   AND fuente.id_seccion_fuente = seccion.id_seccion 
									   AND fuente.id_fuente = '%d'", array(0 => $id));
        if ($fuente !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $fuente = $this->bd->getFila();
            } else {
                $fuente = FALSE;
            }
        }

        return $fuente;
    }

    // Utilizado unicamente en edición: XMLRPC
    /**
     * @param $url
     * @return bool
     */
    public function obtenerFuentePorUrl($url)
    {

        $fuente = FALSE;
        $fuente = $this->bd->ejecutar("SELECT fuente.id_fuente as id, fuente.titulo_fuente as titulo, fuente.url_fuente as url, fuente.fecha_alta_fuente as fecha, fuente.estado_fuente as estado, fuente.tipo_fuente as tipo,
									   medio.titulo_medio as medio, seccion.titulo_seccion as seccion 
									   FROM fuente,medio,seccion  
									   WHERE fuente.id_medio_fuente = medio.id_medio 
									   AND fuente.id_seccion_fuente = seccion.id_seccion 
									   AND fuente.url_fuente = '%s'", array(0 => $url));
        if ($fuente !== FALSE) {
            $this->bd->rebobinar();
            if ($this->bd->numeroFilas() == 1) {
                $fuente = $this->bd->getFila();
            } else {
                $fuente = FALSE;
            }
        }

        return $fuente;
    }


    /**
     * Obtenemos las noticias de una fuente definida por url.
     * Esta extrae la informacion por medio de la libreria SimplePie.
     *
     * Origen de la fuente tiene que ser una sindicalizacion.
     *
     * @return array $noticias noticias
     * @param string $url Direccion URL de la fuente
     * @param integer $inicio Inicio del intervalo de noticias
     * @param integer $total Total de noticias
     */
    public function obtenerNoticiasPorUrl($url, $inicio = 0, $total = 0)
    {

        $noticias = array();

        ControladorFrontal::setGestorErroresExcepciones(0);

        $fuente_rss = Cargador::cargar('Libreria')->SimplePieExtendido;

        $fuente_rss->set_feed_url($url);
        $fuente_rss->set_output_encoding("UTF-8");
        $fuente_rss->enable_order_by_date(TRUE);
        $fuente_rss->set_cache_location(Cargador::cargar("Configuracion")->RSS_CACHE_PATH);
        $fuente_rss->set_cache_duration(Cargador::cargar("Configuracion")->RSS_CACHE_DURACION);
        $fuente_rss->enable_cache(Cargador::cargar("Configuracion")->RSS_CACHE);
        $fuente_rss->set_timeout(Cargador::cargar("Configuracion")->RSS_CACHE_TIMEOUT);

        if (is_array($url)) {
            //Multifeed
            $fuente_rss->set_item_limit($total);
        }

        // Cache de imagenes - imagenes/imagen.php?cache=67d5fa9a87bad230fb03ea68b9f71090
        if (Cargador::cargar("Configuracion")->RSS_CACHE_IMAGENES === TRUE) {
            $fuente_rss->set_image_handler(Cargador::cargar("Configuracion")->BASE_URL . 'imagenes/imagen.php', 'cache',
                Cargador::cargar("Configuracion")->RSS_CACHE_IMAGENES_PATH);
            //$fuente_rss->set_favicon_handler(Cargador::cargar("Configuracion")->BASE_URL.'imagenes/imagen.php', 'cache',
            //								Cargador::cargar("Configuracion")->RSS_CACHE_IMAGENES_PATH);
        }


        //$fuente_rss->set_stupidly_fast(true);

        //$fuente_rss->strip_comments(true);
        //$fuente_rss->strip_htmltags($fuente_rss->strip_htmltags);
        //$fuente_rss->remove_div(true);
        //$fuente_rss->encode_instead_of_strip(true);


        //$fuente_rss->set_autodiscovery_level(SIMPLEPIE_LOCATOR_NONE);

        //$file = new SimplePie_File($url);
        //file_put_contents(BASE_PATH . DIRSEP . 'tmp' .DIRSEP .md5($url).".xml", $file->body);
        //unset($file);

        //$fuente_rss->enable_xml_dump(false);


        $exito = $fuente_rss->init();

        $error = false;

        if ($fuente_rss->error()) {
            $error = $fuente_rss->error();
        }

        $noticias["fuente"] = array();
        if ($exito) {

            if (!is_array($url)) {

                $noticias["fuente"]["titulo"] = $fuente_rss->get_title();
                $noticias["fuente"]["url"] = $fuente_rss->get_link();
                $noticias["fuente"]["imagen"] = $fuente_rss->get_image_url();
                $noticias["fuente"]["maximo_items"] = $fuente_rss->get_item_quantity();
                $noticias["fuente"]["cache_tiempo"] = $fuente_rss->get_cache_timestamp(false, true);
                $noticias["fuente"]["cache_tiempo_restante"] = $fuente_rss->get_cache_time_remaining(true);
                $noticias["fuente"]["cache_tiempo_fecha"] = $fuente_rss->get_cache_timestamp(true, true);
                $noticias["fuente"]["favicon"] = $fuente_rss->get_favicon();


                $max = $noticias["fuente"]["maximo_items"];
                if ($inicio > $max) {
                    $inicio = 0;
                }
                if ($total > $max || $total == 0) {
                    $total = $max;
                }

                $noticias["noticias"] = array();
                foreach ($fuente_rss->get_items($inicio, $total) as $item) {
                    $noticia = array();
                    $noticia["titulo"] = $item->get_title();
                    $noticia["url"] = $item->get_permalink();

                    //$noticia["fecha"] = $item->get_date('j F Y, g:i a');
                    $noticia["fecha"] = $item->get_local_date("%A %d de %B de %Y a las %H:%M:%S");
                    // Excepcion de encoding
                    $encoding = mb_detect_encoding($noticia["fecha"], 'ASCII,UTF-8,ISO-8859-1');
                    if ($encoding == "ISO-8859-1") {
                        $noticia["fecha"] = utf8_encode($noticia["fecha"]);
                    }

                    $noticia["autor"] = "";
                    $autor = $item->get_author();
                    if ($autor) {
                        $noticia["autor"] = trim($autor->name . ' ' . $autor->email);
                    }
                    $noticia["contenido"] = $item->get_content();
                    $noticias["noticias"][] = $noticia;
                }

                $noticias["fuente"]["copyright"] = $fuente_rss->get_copyright();


                $fuente_rss->__destruct();
                unset($fuente_rss);

                // Si es un array de feed
            } else {


                $noticias["noticias"] = array();
                foreach ($fuente_rss->get_items($inicio) as $item) {

                    $feed = $item->get_feed();

                    $noticia = array();
                    $noticia["titulo"] = $item->get_title();
                    $noticia["url"] = $item->get_permalink();
                    $noticia["fuente"] = $feed->get_title();
                    $noticias["noticias"][] = $noticia;

                }


            }

            ControladorFrontal::setGestorErroresExcepciones(TRUE);

        } else {
            trigger_error("Sin exito, Error: " . $error);
            $noticias["noticias"] = array();
        }


        //echo "<pre>";
        //print_r($noticias["noticias"]);
        //exit;

        return $noticias;
    }

    /**
     * @param $id
     * @param $titulo
     * @param $url
     * @param $estado
     * @param $tipo
     * @param $pais
     * @return bool
     */
    public function actualizarMedio($id, $titulo, $url, $estado, $tipo, $pais)
    {

        $medio = FALSE;

        if ($titulo === FALSE && $url === FALSE && $estado === FALSE && $tipo === FALSE && $pais === FALSE) {
            return FALSE;
        }

        $SQL = "UPDATE medio SET ";
        $DATOS = array();
        if ($titulo !== FALSE) {
            $SQL .= "titulo_medio = '%s' , ";
            $DATOS[] = $titulo;
        }
        if ($url !== FALSE) {
            $SQL .= "url_medio = '%s' , ";
            $DATOS[] = $url;
        }
        if ($estado !== FALSE) {
            $SQL .= "estado_medio = '%s' , ";
            $DATOS[] = $estado;
        }
        if ($tipo !== FALSE) {
            $SQL .= "tipo_medio = '%s' , ";
            $DATOS[] = $tipo;
        }
        if ($pais !== FALSE) {
            $SQL .= "id_pais_medio = '%d' , ";
            $DATOS[] = $pais;
        }

        // Eliminamos la ultima comilla
        $SQL = substr($SQL, 0, strrpos($SQL, ","));

        $SQL .= " WHERE id_medio = '%d' LIMIT 1";
        $DATOS[] = $id;

        $medio = $this->bd->ejecutar($SQL, $DATOS);
        if ($medio !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        return $medio;
    }

    /**
     * @param $id
     * @param $titulo
     * @param $estado
     * @param $seccion
     * @return bool
     */
    public function actualizarSeccion($id, $titulo, $estado, $seccion)
    {

        $sec = FALSE;

        if ($titulo === FALSE && $estado === FALSE && $seccion === FALSE) {
            return FALSE;
        }

        $SQL = "UPDATE seccion SET ";
        $DATOS = array();
        if ($titulo !== FALSE) {
            $SQL .= "titulo_seccion = '%s' , ";
            $DATOS[] = $titulo;
        }
        if ($estado !== FALSE) {
            $SQL .= "estado_seccion = '%s' , ";
            $DATOS[] = $estado;
        }
        if ($seccion !== FALSE) {
            $SQL .= "subseccion_seccion = '%d' , ";
            $DATOS[] = $seccion;
        }

        // Eliminamos la ultima comilla
        $SQL = substr($SQL, 0, strrpos($SQL, ","));

        $SQL .= " WHERE id_seccion = '%d' LIMIT 1";
        $DATOS[] = $id;

        $sec = $this->bd->ejecutar($SQL, $DATOS);
        if ($sec !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        return $sec;
    }

    /**
     * @param $id
     * @param $titulo
     * @param $url
     * @param $estado
     * @param $tipo
     * @param $pais
     * @return bool
     */
    public function insertarMedio($id, $titulo, $url, $estado, $tipo, $pais)
    {
        $medio = FALSE;
        $SQL = "INSERT INTO medio (`id_medio`, `titulo_medio`, `url_medio`, `estado_medio`, `tipo_medio`, `id_pais_medio`)
				VALUES (NULL, '%s', '%s', '%s', '%s', '%d')";

        $medio = $this->bd->ejecutar($SQL, array(0 => $titulo, 1 => $url, 2 => $estado, 3 => $tipo, 4 => $pais));
        if ($medio !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                $medio = $this->bd->ultimoInsertId();
            } else {
                $medio = FALSE;
            }
        }

        return $medio;
    }

    /**
     * @param $id
     * @return bool
     */
    public function eliminarMedio($id)
    {
        $SQL = "DELETE FROM medio WHERE id_medio = '%d' LIMIT 1";
        $eliminar = $this->bd->ejecutar($SQL, array(0 => $id));
        if ($eliminar !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        return FALSE;
    }


    /**
     * @param $id
     * @return bool
     */
    public function eliminarSeccion($id)
    {
        $SQL = "DELETE FROM seccion WHERE id_seccion = '%d' LIMIT 1";
        $eliminar = $this->bd->ejecutar($SQL, array(0 => $id));
        if ($eliminar !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        return FALSE;
    }

    /**
     * @param $id
     * @param $titulo
     * @param $url
     * @param $fechaAlta
     * @param $estado
     * @param $tipo
     * @param $medio
     * @param $seccion
     * @return bool
     */
    public function insertarFuente($id, $titulo, $url, $fechaAlta, $estado, $tipo, $medio, $seccion)
    {
        $fuente = FALSE;
        $SQL = "INSERT INTO fuente (`id_fuente`, `titulo_fuente`, `url_fuente`, `fecha_alta_fuente`, `estado_fuente`, `tipo_fuente`, `id_medio_fuente`, `id_seccion_fuente`)
				VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%d', '%d')";

        $fuente = $this->bd->ejecutar($SQL, array(0 => $titulo, 1 => $url, 2 => $fechaAlta, 3 => $estado, 4 => $tipo, 5 => $medio, 6 => $seccion));
        if ($fuente !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                $fuente = $this->bd->ultimoInsertId();
            } else {
                $fuente = FALSE;
            }
        }

        return $fuente;
    }

    /**
     * @param $id
     * @param $titulo
     * @param $estado
     * @param $seccion
     * @return bool
     */
    public function insertarSeccion($id, $titulo, $estado, $seccion)
    {
        $sec = FALSE;
        $SQL = "INSERT INTO seccion (`id_seccion`, `titulo_seccion`, `estado_seccion`, `subseccion_seccion`)
				VALUES (NULL, '%s', '%s', '%d')";

        $sec = $this->bd->ejecutar($SQL, array(0 => $titulo, 1 => $estado, 2 => $seccion));
        if ($sec !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                $sec = $this->bd->ultimoInsertId();
            } else {
                $sec = FALSE;
            }
        }

        return $sec;
    }

    /**
     * @param $id
     * @param $titulo
     * @param $url
     * @param $fechaAlta
     * @param $estado
     * @param $tipo
     * @param $medio
     * @param $seccion
     * @return bool
     */
    public function actualizarFuente($id, $titulo, $url, $fechaAlta, $estado, $tipo, $medio, $seccion)
    {

        $fuente = FALSE;

        if ($titulo === FALSE && $url === FALSE && $fechaAlta === FALSE && $estado === FALSE && $tipo === FALSE && $medio === FALSE && $seccion === FALSE) {
            return FALSE;
        }

        $SQL = "UPDATE fuente SET ";
        $DATOS = array();
        if ($titulo !== FALSE) {
            $SQL .= "titulo_fuente = '%s' , ";
            $DATOS[] = $titulo;
        }
        if ($url !== FALSE) {
            $SQL .= "url_fuente = '%s' , ";
            $DATOS[] = $url;
        }
        if ($fechaAlta !== FALSE) {
            $SQL .= "fecha_alta_fuente = '%s' , ";
            $DATOS[] = $fechaAlta;
        }
        if ($estado !== FALSE) {
            $SQL .= "estado_fuente = '%s' , ";
            $DATOS[] = $estado;
        }
        if ($tipo !== FALSE) {
            $SQL .= "tipo_fuente = '%s' , ";
            $DATOS[] = $tipo;
        }
        if ($medio !== FALSE) {
            $SQL .= "id_medio_fuente = '%d' , ";
            $DATOS[] = $medio;
        }
        if ($seccion !== FALSE) {
            $SQL .= "id_seccion_fuente = '%d' , ";
            $DATOS[] = $seccion;
        }

        // Eliminamos la ultima comilla
        $SQL = substr($SQL, 0, strrpos($SQL, ","));

        $SQL .= " WHERE id_fuente = '%d' LIMIT 1";
        $DATOS[] = $id;

        $fuente = $this->bd->ejecutar($SQL, $DATOS);
        if ($fuente !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        return $fuente;
    }

    /**
     * @param $id
     * @return bool
     */
    public function eliminarFuente($id)
    {
        $SQL = "DELETE FROM fuente WHERE id_fuente = '%d' LIMIT 1";
        $eliminar = $this->bd->ejecutar($SQL, array(0 => $id));
        if ($eliminar !== FALSE) {
            if ($this->bd->filasAfectadas() == 1) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        return FALSE;
    }

    /**
     * @param $secciones
     * @return bool
     */
    private function ordenarSecciones($secciones)
    {
        $tmp = array();
        foreach ($secciones as $seccion) {
            if (isset($seccion["secciones"])) {
                $secc = $seccion;
                $secc["secciones"] = $this->ordenarBurbuja($secc["secciones"]);
                $tmp[] = $secc;
            } else {
                $tmp[] = $seccion;
            }
        }
        $secciones = $this->ordenarBurbuja($tmp);
        //echo '<pre>';
        //print_r($secciones);
        //exit;
        return $secciones;
    }

    /**
     * @param $array
     * @return bool
     */
    private function ordenarBurbuja($array)
    {
        $count = count($array);
        if ($count <= 0) {
            return false;
        }
        for ($i = 0; $i < $count; $i++) {
            for ($j = $count - 1; $j > $i; $j = $j - 1) {
                if ($array[$j]["titulo"] < $array[$j - 1]["titulo"]) {
                    $tmp = $array[$j];
                    $array[$j] = $array[$j - 1];
                    $array[$j - 1] = $tmp;
                }
            }
        }
        return $array;
    }

}

?>