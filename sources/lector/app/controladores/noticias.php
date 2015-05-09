<?php

class noticias extends Controlador
{

    function index()
    {

        if (!empty($this->parametros)) {
            throw new NoEncontrado404Excepcion("la página 'noticias/index/' no tiene parametros.");
        }

        $this->parametros[0] = "ultima-hora";
        $this->ver();

    }

    function ver()
    {

        // Posibles parametros
        // - 1: Seccion
        // - 2: Medio
        // - 3: Inicio - Total (separados por un guion) (opcional)
        $total_parametros = count($this->parametros);

        if ($total_parametros > 3) {
            throw new NoEncontrado404Excepcion("Ruta 'noticias/ver/' tiene demasiado parametros.");
        }

        $general = Cargador::cargar('Modelo')->Noticias;

        $ruta = array(0 => array("titulo" => "Noticias", "url" => 'noticias/'));

        $seccion = array("titulo" => '');
        $fuentes = array();
        $medio = array("id" => 0, "posicion" => 0);

        //Paginacion principal por defecto, con opcion a cambiar dinamicamente.
        $inicio = 0;
        $total = 20; //Por defecto 10


        if ($total_parametros > 2) {
            $pag = explode("-", $this->parametros[2]);
            if (count($pag) == 2) {
                $inicio = intval($pag[0]);
                $total = intval($pag[1]);
            } else {
                $inicio = intval($pag[0]);
            }
        }


        // Parametro Seccion
        if ($total_parametros > 0) {
            $seccion_tmp = ucwords(str_replace("-", " ", $this->parametros[0]));
            $seccion_tmp = str_replace("_", "-", $seccion_tmp);
            $seccion_tmp = $general->obtenerSeccion($seccion_tmp);
            if ($seccion_tmp) {
                $seccion = $seccion_tmp;
            } else {
                throw new NoEncontrado404Excepcion("Seccion no econtrada. Ruta: 'noticias/ver/'" . $this->parametros[0] . ".");
            }
            unset($seccion_tmp);
        }

        if (empty($seccion["titulo"]) == FALSE) {
            $ruta[] = array("url" => strtolower($this->formatoUrl($seccion["titulo"])) . '/', "titulo" => $seccion["titulo"]);
        }

        // Listado de fuentes por seccion
        $fuentes = $general->listadoFuentesPorSeccion($seccion["titulo"]);
        $total_fuentes = count($fuentes);


        // Parametro Medio
        // Para inicializar en una pestaña
        // Obtenemos la fuente acorde con el medio especificado y seccion
        if ($total_parametros > 1 && (empty($seccion) == FALSE)) {
            $medio_tmp = str_replace("-", " ", $this->parametros[1]);
            $medio_tmp = str_replace("_", "-", $medio_tmp);
            $medio_tmp = $general->obtenerFuente($medio_tmp, $seccion["titulo"]);
            if ($medio_tmp == TRUE) {
                $medio = $medio_tmp;
                $fuente_tmp = $general->obtenerNoticiasPorUrl($medio["url"], $inicio, $total);

                // Controlamos que el rss devuelve datos
                // En el caso de que este vacio, habria que rellenar la url con la del medio en si.
                if (empty($fuente_tmp["fuente"])) {
                    $medio["fuente"]["titulo"] = $medio["titulo"];
                    $medio["fuente"]["url"] = "";
                    $medio["fuente"]["imagen"] = "";
                    $medio["fuente"]["maximo_items"] = 0;
                    $medio["fuente"]["cache_tiempo"] = 0;
                    $medio["fuente"]["cache_tiempo_restante"] = 0;
                    $medio["fuente"]["cache_tiempo_fecha"] = "";
                    $medio["noticias"] = array();
                } else {
                    $medio["fuente"] = $fuente_tmp["fuente"];
                    $medio["noticias"] = $fuente_tmp["noticias"];
                }
                unset($fuente_tmp);

                // Posicion en el array fuentes para su posterior inicializacion en la vista
                for ($f = 0; $f < $total_fuentes; $f++) {
                    if ($fuentes[$f]["id"] == $medio["id"]) {
                        $medio["posicion"] = ($f + 1); // sumamos uno porque el cero es para el general de la seccion
                        break;
                    }
                }

            } else {
                throw new NoEncontrado404Excepcion("Medio no econtrado. Ruta: 'noticias/ver/'" . $this->parametros[0] . "/" . $this->parametros[1] . ".");
            }
            unset($medio_tmp);
        }

        if (empty($medio["id"]) == FALSE) {
            $ruta[] = array("url" => strtolower($this->formatoUrl($medio["titulo"])) . '/', "titulo" => $medio["titulo"]);
        } else if (empty($medio["id"]) == TRUE && $total_fuentes > 0) {
            // Si utilizamos caso general(comparativa de medios) anulamos la fuente por defecto
        }


        // Comparacion de titulares de una seccion (minimo dos fuentes)
        $cmp_fuentes = array();
        if ($total_fuentes >= 2) {

            foreach ($fuentes as $fuente) {
                $cmp_fuentes[] = $fuente["url"];
            }

            $cmp_fuentes = $general->obtenerNoticiasPorUrl($cmp_fuentes, 0, 5);
            $cmp_fuentes = $cmp_fuentes["noticias"];

            $cmp_fuentes = $this->similar($cmp_fuentes);

        }

        // Titulo h1 de la pagina, titulo corto(nombre de la pagina)
        $titulo = $seccion["titulo"];

        $head = array();
        $head["title"] = $seccion["titulo"] . " - Noticias - PrensaSeria";
        $head["keywords"] = 'prensa seria, prensa digital, diarios, periodicos, almeria, madrid, garrucha, ' . strtolower($this->formatoTexto($seccion["titulo"], 'abc'));
        $head["descripcion"] = 'Contraste las noticias. ' . $seccion["titulo"] . '.';
        // Canonical
        $head["canonical"] = Cargador::cargar('Configuracion')->BASE_URL;
        foreach ($ruta as $r) {
            $head["canonical"] .= $r["url"];
        }

        if (isset($medio["fuente"])) {
            $titulo = $medio["titulo"];
            $head["title"] = $medio["titulo"] . ' - ' . $head["title"];
            $head["keywords"] = $head["keywords"] . ', ' . strtolower($this->formatoTexto($medio["titulo"], 'abc'));
            $head["descripcion"] = $head["descripcion"] . '. ' . $medio["titulo"] . '.';
        }

        $head["robots"] = 'index,follow';
        $head["charset_encoding"] = Cargador::cargar('Configuracion')->CHARSET_ENCODING;
        $head["css"][] = array("nombre" => "noticias-tabs.min", "extension" => "css");
        $head["js"][] = array("nombre" => "jquery-ui.min.js");

        // Aqui medio posicion se le resta porque las pestañas son de 0...
        $head["js"][] = array("script" => '
		<script type="text/javascript">
	  	//<![CDATA[
	  		$(document).ready(function(){
	  			var $tabs = $("#diarios > ul").tabs({selected: ' . $medio["posicion"] . '}).click(function(){
		  			this.target = "_blank";
				});	
			});
		//]]>
	   </script>\'');

        $head["js"][] = array("nombre" => "noticias.min.js");
        $head["extras"][] = '
      <style type="text/css">
        #contenido{
            float:none;
            width:940px;
            padding-right: 5px;
        }
        #diarios{
            clear:both;
            min-height:500px;
        }
        .ui-tabs-panel{
            min-height:' . (($total_fuentes <= 5) ? '480px' : (480 + (($total_fuentes - 5) * 40) . 'px')) . ';
        }
      </style>';

        $datos[] = array();
        $datos["fuentes"] = $fuentes;
        $datos["medio"] = $medio;
        $datos["titulo_seccion"] = $ruta;

        $datos["paginacion_inicio"] = $inicio;
        $datos["paginacion_total"] = $total;

        //Paginacion
        if (count($fuentes) > 0 && (empty($medio["fuente"]) == FALSE)) {

            // Reajustamos el inicio y el total para que sean reales
            $max = $medio["fuente"]["maximo_items"];
            if ($inicio > $max) {
                $inicio = 0;
            }
            if ($total > $max || $total == 0) {
                $total = $max;
            }


            $datosPaginacion = array();
            $datosPaginacion["inicio"] = $inicio;
            $datosPaginacion["total"] = $total;
            $datosPaginacion["medio_posicion"] = $medio["posicion"];
            $datosPaginacion["seccion_titulo"] = $seccion["titulo"];
            $datosPaginacion["medio_titulo"] = $medio["titulo"];
            $datosPaginacion["maximo_items"] = $medio["fuente"]["maximo_items"];
            $datos["diario_barra"] = $this->barraPaginacion($datosPaginacion);
        }

        $datos["numero_secciones"] = $general->totalSecciones();
        $datos["numero_medios"] = $general->totalMedios();
        $datos["numero_fuentes"] = $general->totalFuentes();

        $datos["cmp_noticias"] = $cmp_fuentes;

        $pagina = array();
        $pagina["todo"] = $this->cargarVista('noticias/ver', $datos)->generar(TRUE);

        $this->esqueleto($head, $pagina, $titulo);

    }

    // Uso basicamente para ajax
    public function lector()
    {
        // Posibles parametros
        // - 1: seccion
        // - 2: medio
        // - 3: Inicio - Total (separados por un guion) (opcional)
        $total_parametros = count($this->parametros);

        if ($total_parametros > 3) {
            throw new NoEncontrado404Excepcion("Ruta 'noticias/lector/' deber de tener como maximo tres.");
        }

        $seccion = array("titulo" => '');
        $fuentes = array();
        $medio = array("id" => 0, "posicion" => 0);

        $inicio = 0;
        $total = 20;

        if ($total_parametros > 2) {
            $pag = explode("-", $this->parametros[2]);
            if (count($pag) == 2) {
                $inicio = intval($pag[0]);
                $total = intval($pag[1]);
            } else {
                $inicio = intval($pag[0]);
            }
        }

        $general = Cargador::cargar('Modelo')->Noticias;

        // Parametro Seccion
        $seccion_tmp = ucwords(str_replace("-", " ", $this->parametros[0]));
        $seccion_tmp = str_replace("_", "-", $seccion_tmp);
        $seccion_tmp = $general->obtenerSeccion($seccion_tmp);
        if ($seccion_tmp) {
            $seccion = $seccion_tmp;
        } else {
            trigger_error("Seccion no econtrada. Ruta: 'noticias/lector/'" . $this->parametros[0], E_USER_WARNING);
            echo("<br /><br />Sin Datos<br /> Intentelo en unos minutos. Si el resultado es el mismo, comuniquenoslo.");
            return FALSE;
        }
        unset($seccion_tmp);

        // Listado de fuentes por seccion
        $fuentes = $general->listadoFuentesPorSeccion($seccion["titulo"]);
        $total_fuentes = count($fuentes);

        // Parametro Medio
        // Para inicializar en una pestaña(posicion)
        // Obtenemos la fuente acorde con el medio especificado y seccion
        $medio_tmp = str_replace("-", " ", $this->parametros[1]);
        $medio_tmp = str_replace("_", "-", $medio_tmp);
        $medio_tmp = $general->obtenerFuente($medio_tmp, $seccion["titulo"]);
        if ($medio_tmp == TRUE) {
            $medio = $medio_tmp;
            $fuente_tmp = $general->obtenerNoticiasPorUrl($medio["url"], $inicio, $total);

            // Controlamos que el rss devuelve datos
            if (empty($fuente_tmp["fuente"])) {
                $medio["fuente"]["titulo"] = $medio["titulo"];
                $medio["fuente"]["url"] = "";
                $medio["fuente"]["imagen"] = "";
                $medio["fuente"]["maximo_items"] = 0;
                $medio["fuente"]["cache_tiempo"] = 0;
                $medio["fuente"]["cache_tiempo_restante"] = 0;
                $medio["fuente"]["cache_tiempo_fecha"] = "";
                $medio["noticias"] = array();
            } else {
                $medio["fuente"] = $fuente_tmp["fuente"];
                $medio["noticias"] = $fuente_tmp["noticias"];
            }
            unset($fuente_tmp);

            // Posicion en el array fuentes para su posterior inicializacion en la vista
            for ($f = 0; $f < $total_fuentes; $f++) {
                if ($fuentes[$f]["id"] == $medio["id"]) {
                    $medio["posicion"] = ($f + 1); // el cero es para el general
                    break;
                }
            }

        } else {
            trigger_error("Medio no econtrado. Ruta: 'noticias/lector/'" . $this->parametros[0] . "/" . $this->parametros[1], E_USER_WARNING);
            echo("<br /><br />Sin Datos<br /> Intentelo en unos minutos. Si el resultado es el mismo, comuniquenoslo.");
            return FALSE;
        }
        unset($medio_tmp);

        unset($fuentes);

        //Paginacion

        // Reajustamos el inicio y el total para que sean reales
        $max = $medio["fuente"]["maximo_items"];
        if ($inicio > $max) {
            $inicio = 0;
        }
        if ($total > $max || $total == 0) {
            $total = $max;
        }

        $datosPaginacion = array();
        $datosPaginacion["inicio"] = $inicio;
        $datosPaginacion["total"] = $total;
        $datosPaginacion["medio_posicion"] = $medio["posicion"];
        $datosPaginacion["seccion_titulo"] = $seccion["titulo"];
        $datosPaginacion["medio_titulo"] = $medio["titulo"];
        $datosPaginacion["maximo_items"] = $medio["fuente"]["maximo_items"];
        $barra = $this->barraPaginacion($datosPaginacion);

        if (empty($medio["fuente"]) == FALSE) {
            echo '<div class="diario_barra_top diario_barra">' . $barra . '</div>';

            if (!empty($medio["fuente"]["imagen"])) {
                echo '<div class="medio_logo"><img src="' . $medio["fuente"]["imagen"] . '" alt="' . $medio["fuente"]["titulo"] . '" width="110" /></div>';
            }
            echo '<div class="medio_actualizacion">Actualizado el ' . $medio["fuente"]["cache_tiempo_fecha"] . '</div>';
            //<span>Actualización en '.$medio["fuente"]["cache_tiempo_restante"].'</span></div>';
            echo "<h2 class=\"medio_titulo\"><a href=\"" . $medio["fuente"]["url"] . "\">" . $medio["fuente"]["titulo"] . "</a></h2>";

            $dia = (int)date('d') + 1; // Incializamos con un dia de mas(para imprimir el dia actual)
            foreach ($medio["noticias"] as $noticia) {

                $noticia_dia = (int)substr($noticia["fecha"], (strpos($noticia["fecha"], "de") - 3), 2);
                if ($dia > $noticia_dia) {
                    $dia = $noticia_dia;
                    echo('<div class="noticias_fecha">Publicado el ' . $noticia["fecha"] . '</div>');
                }

                ?>
                <div class="noticia">
                    <h3 class="noticia_titulo"><?php if ($noticia["url"]) echo '<a href="' . $noticia["url"] . '" rel="external">';
                        echo $noticia["titulo"];
                        if ($noticia["url"]) echo '</a>'; ?></h3>
                    <?php
                    if (!empty($noticia["contenido"])) {
                        echo '<div class="noticia_contenido">' . $noticia["contenido"] . '</div>';
                    }
                    if (false) {
                        ?>
                        <div class="noticia_fecha">Publicado el <?php echo $noticia["fecha"]; ?></div>
                    <?php } ?>
                </div>
            <?php
            }

            if (empty($medio["noticias"])) {
                echo '<p>Temporalmente no podemos ofrecerle noticias de este medio. Intentelo en unos minutos.</p><p>Si sigue sin poder ver la noticias, comuniquenoslo. Gracias.</p>';
            }

            echo '<div class="medio_copyright">' . $medio["fuente"]["copyright"] . '</div>';
            //echo '<div class="medio_actualizacion"><span>Actualización en '.$medio["fuente"]["cache_tiempo_restante"].'</span></div>';
            echo '<div class="diario_barra_bottom diario_barra">' . $barra . '</div>';
        }

    }


    function estado()
    {

        if (!empty($this->parametros)) {
            throw new NoEncontrado404Excepcion("la página 'noticias/estado/' no tiene parametros.");
        }

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Acceso restringido"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization Required.';
            return;
        }

        if (($_SERVER['PHP_AUTH_USER'] == "lector") && ($_SERVER['PHP_AUTH_PW'] == "estado")) {

            set_time_limit(0);


            $noticias = Cargador::cargar('Modelo')->Noticias;

            $fuentes = $noticias->listadoFuentes();

            $data = '';
            foreach ($fuentes as $fuente) {
                $headers = get_headers($fuente['url'], 1);

                $status = $headers['0'];
                $type = $headers['Content-Type'];

                if (is_array($type)) {
                    $type = implode(",", $type);
                }

                $data .= $fuente['url'];

                if (strpos($type, 'xml') !== false) {
                    $data .= ' - CORRECTO';
                } else {
                    $data .= ' - ERROR (XML)';
                }

                if ((strpos($status, '200') !== false) || (strpos($status, '302') !== false) || (strpos($status, '301') !== false)) {
                    $data .= ' - CORRECTO';
                    $status = 'activo';
                } else {
                    $data .= ' - ERROR (ESTADO)';
                    $status = 'suspendido';
                }

                $data .= "\n";

                // Actualizamos la lista
                if ($fuente['estado'] != $status) {
                    $noticias->actualizarFuente($fuente['id'], FALSE, FALSE, FALSE, $status, FALSE, FALSE, FALSE);
                }

            }

            if (is_dir(Cargador::cargar('Configuracion')->CACHE_PATH)) {
                exec('rm -frv ' . Cargador::cargar('Configuracion')->CACHE_PATH);
            }

            if (!is_dir(Cargador::cargar('Configuracion')->CACHE_PATH)) {
                mkdir(Cargador::cargar('Configuracion')->CACHE_PATH);
                mkdir(Cargador::cargar('Configuracion')->RSS_CACHE_PATH);
                mkdir(Cargador::cargar('Configuracion')->RSS_CACHE_IMAGENES_PATH);
            }

            header("Content-type: text/html; charset=UTF-8", TRUE);
            echo '<pre>';
            echo $data;
            echo '</pre>';

        } else {
            header('WWW-Authenticate: Basic realm="Acceso restringido"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization Required.';
            return;
        }

    }

    private function esqueleto($head = array("head" => array("title" => "Noticias - PrensaSeria")), $pagina = array(), $titulo = '')
    {

        $general = Cargador::cargar('Modelo')->Noticias;

        $secciones = $general->listadoSecciones();

        $body = array();
        $body['cabecera'] = $this->cargarVista('cabecera', array("secciones" => $secciones, "titulo" => $titulo))->generar(TRUE);
        $body['pagina'] = $this->cargarVista('pagina', array("pagina" => $pagina))->generar(TRUE);
        $body['pie'] = $this->cargarVista('pie')->generar(TRUE);

        $this->cargarVista('head', $head, FALSE, array("Content-type" => "text/html; charset=utf-8"))->generar();
        $this->cargarVista('body', $body)->generar();
    }

    private function formatoUrl($url)
    {
        $carac = array("á", "é", "í", "ó", "ú", "-", "ñ", " ");
        $carac_sano = array("a", "e", "i", "o", "u", "_", "n", "-");
        return strtolower(str_replace($carac, $carac_sano, $url));
    }

    private function formatoTexto($texto, $tipo = 'url')
    {
        $carac = array("á", "é", "í", "ó", "ú", "-", "ñ", " ");
        $carac_sano = array("a", "e", "i", "o", "u", "_", "n", "-");

        if ($tipo == 'aeiou') {
            $carac = array("á", "é", "í", "ó", "ú");
            $carac_sano = array("a", "e", "i", "o", "u");
        }

        if ($tipo == 'abc') {
            $carac = array("á", "é", "í", "ó", "ú", "ñ");
            $carac_sano = array("a", "e", "i", "o", "u", "n");
        }

        if ($tipo == 'url') {
            return strtolower(str_replace($carac, $carac_sano, $texto));
        }

        return str_replace($carac, $carac_sano, $texto);
    }

    private function barraPaginacion($datos = array())
    {

        $inicio = $datos["inicio"];
        $total = $datos["total"];

        $medio["posicion"] = $datos["medio_posicion"];

        $ruta = Cargador::cargar("Configuracion")->BASE_URL . 'noticias/' . strtolower($this->formatoUrl($datos["seccion_titulo"])) . '/' .
            strtolower($this->formatoUrl($datos["medio_titulo"])) . '/';

        // Total de item
        $maximoItems = $datos["maximo_items"];
        // Siguiente y Anterior Pagina
        $siguiente = $inicio + $total;
        $anterior = $inicio - $total;

        // Creamos el enlace siguiente
        $siguiente_url = $ruta . $siguiente . '-' . $total . '/';
        $siguiente_url_tope = $ruta . ($siguiente - $total) . '-' . $total . '/';

        $enlaceSiguiente = '<a class="pagina_siguiente_activa" href="' . $siguiente_url . '" title="' . $medio["posicion"] . '" onclick="cargarPagina(this);return false;" onkeypress="cargarPagina(this);return false;" rel="nofollow next">Siguiente</a>';
        if ($siguiente >= $maximoItems) {
            $enlaceSiguiente = '<span class="pagina_siguiente">Siguiente</span>';
        }


        // Creamos el enlace anterior
        $anterior_url = $ruta . $anterior . '-' . $total . '/';

        $enlaceAnterior = '<a class="pagina_anterior_activa" href="' . $anterior_url . '" title="' . $medio["posicion"] . '" onclick="cargarPagina(this);return false;" onkeypress="cargarPagina(this);return false;" rel="nofollow prev">Anterior</a>';
        if ($anterior <= 0 && $inicio > 0) {
            $anterior_url = $ruta . '0-' . $total . '/';
            $enlaceAnterior = '<a class="pagina_anterior_activa" href="' . $anterior_url . '" title="' . $medio["posicion"] . '" onclick="cargarPagina(this);return false;" onkeypress="cargarPagina(this);return false;" rel="nofollow prev">Anterior</a>';
        } else if ($anterior <= 0) {
            $enlaceAnterior = '<span class="pagina_anterior">Anterior</span>';
        }

        // Normalizamos los numeros
        $principio = $inicio + 1;
        $final = ($siguiente > $maximoItems) ? $maximoItems : $siguiente;

        $barra = $enlaceAnterior . ' - Viendo desde <strong>' . $principio . '</strong> al <strong>' . $final . '</strong> de <strong>' . $maximoItems . '</strong> - ' . $enlaceSiguiente;

        return $barra;
    }

    private function similar($cadenas)
    {
        set_time_limit(0);

        $combinaciones = $this->combinatoria($cadenas);

        $similarCAD = array();

        for ($index = 0; $index < count($combinaciones); $index++) {
            if (isset($combinaciones[$index])) {
                $cad1 = $cadenas[$combinaciones[$index][0]]["titulo"];
                $cad2 = $cadenas[$combinaciones[$index][1]]["titulo"];
                $comp = $this->comparar($cad1, $cad2, 3);
                similar_text($cad1, $cad2, $similar);
                //$similar = $comp;
                if ((($comp + $similar) / 1.5) >= 50) {
                    if (!isset($similarCAD[md5($cad1)])) {
                        $similarCAD[md5($cad1)][] = $cadenas[$combinaciones[$index][0]];
                        $similarCAD[md5($cad1)][] = $cadenas[$combinaciones[$index][1]];

                        $tot = count($combinaciones);
                        for ($index2 = 0; $index2 < $tot; $index2++) {
                            if (isset($combinaciones[$index2])) {
                                if ($combinaciones[$index2][0] == $combinaciones[$index][1]) {
                                    unset($combinaciones[$index2]);
                                }
                            }
                        }

                    } else {
                        $similarCAD[md5($cad1)][] = $cadenas[$combinaciones[$index][1]];
                    }
                }
                unset($cad1, $cad2, $comp, $similar);
            }
        }
        //echo '<pre>';
        //print_r($similarCAD);
        //exit;
        return $similarCAD;
    }

    private function combinatoria($cadenas, $valores = FALSE, $salto = 5)
    {

        $combinatorias = array();

        $total = count($cadenas);
        for ($i = 0; $i < ($total - 1); $i++) {
            for ($j = ($i + 1 + ($i != 0 ? $salto % $i : $salto)); $j < $total; $j++) {
                if ($valores) {
                    $combinatorias[] = array($cadenas[$i], $cadenas[$j]);
                } else {
                    $combinatorias[] = array($i, $j);
                }
            }
        }

        return $combinatorias;

    }

    private function comparar($str1, $str2, $longitud = 1)
    {
        $count = 0;

        //echo $str1.'<br /><br />';

        //echo $str1.'<br /><br />';
        $str1 = strtolower($str1);
        $str1 = preg_replace("[:punct:]", ' ', $str1);
        while (strstr($str1, '  ')) {
            $str1 = str_replace('  ', ' ', $str1);
        }
        //echo $str1.'<br /><br />';
        $str1 = explode(' ', $str1);

        //echo count($str1);
        //echo count($this->filtroLongitud($str1));
        $str1 = $this->filtroLongitud($str1, $longitud);

        $str2 = strtolower($str2);
        $str2 = preg_replace("[:punct:]", ' ', $str2);

        while (strstr($str2, '  ')) {
            $str2 = str_replace('  ', ' ', $str2);
        }
        $str2 = explode(' ', $str2);

        $str2 = $this->filtroLongitud($str2, $longitud);

        $str1original = $str1;
        $str2original = $str2;

        // Valores unicos
        $str1 = array_unique($str1);
        $str2 = array_unique($str2);

        // Ordenamos y de paso reajustamos los indices
        sort($str1);
        sort($str2);

        // La primera cadena debe de ser mayor que la segunda
        if (count($str1) < count($str2)) {
            $tmp = $str1;
            $str1 = $str2;
            $str2 = $tmp;
            unset($tmp);
        }

        //echo '<pre>';
        //print_r($str1);
        //print_r($str2);
        //exit;

        $coincidencias = array();
        for ($i = 0; $i < count($str1); $i++) {
            if (in_array($str1[$i], $str2, TRUE)) {
                $coincidencias[] = array($str1[$i], count(array_keys($str2original, $str1[$i], TRUE)));
                $count++;
            }
        }

        // echo '<pre>';
        // print_r($coincidencias)."<br >";
        // echo '</pre>';
        //echo count($str1).'-';
        //echo count($str2).'-';
        //echo $count;
        //exit;
        if (count($str2) != 0) {
            return ($count / (count($str2)) * 100);
        }
        return 0;
    }

    private function filtroLongitud($array, $longitud = 0)
    {
        $a = array();
        foreach ($array as $arr) {
            if (strlen($arr) >= $longitud) {
                $a[] = $arr;
            }
        }
        return $a;
    }

    public function __destruct()
    {
        parent::__destruct();
    }

}

?>