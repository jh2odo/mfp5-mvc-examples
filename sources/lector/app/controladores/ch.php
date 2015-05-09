<?php

// Clase cache
class Ch extends Controlador
{

    function index()
    {

        set_time_limit(0);
        ini_set('display_errors', 'on'); // On solo en desarrollo y en produccion off
        ini_set('display_startup_errors', 'on'); // On solo en desarrollo y en produccion off
        error_reporting(E_ALL);

        //restore_error_handler();
        //restore_exception_handler();
        $PUBLICO = FALSE;
        $IP_SERVIDOR = $_SERVER["SERVER_ADDR"]; // $_SERVER["SERVER_ADDR"]

        if ((!empty($this->parametros) || $this->getRealIpAddr() != $IP_SERVIDOR) && !$PUBLICO) {
            throw new NoEncontrado404Excepcion("la página 'cache/index/' no tiene parametros.");
        }

        $fecha = date('Y-m-d_H-i-s');
        $datos = $fecha . " - " . $this->getRealIpAddr() . "\n";
        file_put_contents(BASE_PATH_APP . 'logs' . DIRSEP . 'cache_accesos.txt', $datos, FILE_APPEND);

        $informe = 'Cache URLS' . "\n";
        $urls = $this->getUrls();

        $fichero_controlador = BASE_PATH_APP . 'app' . DIRSEP . 'controladores' . DIRSEP . "noticias.php";
        require_once($fichero_controlador);

        $informe .= "\nInicio...\n\n";
        $contador = 0;
        $limite = count($urls);
        $nivel = 1; // 1 - Principales y 2 - Secundarias
        $cacheadas = 0;
        foreach ($urls as $url) {
            $url_seccionada = explode('/', $url);
            $url_seccionada = array_filter($url_seccionada);
            array_shift($url_seccionada);
            array_shift($url_seccionada);
            if (count($url_seccionada) <= $nivel) {
                try {
                    // Arreglado para las opciones de getRuta() del enrrutador
                    Cargador::cargar("Enrutador")->setRuta(str_replace("noticias/ver", "noticias", $url));
                    $noticias = new noticias();
                    $noticias->setParametros($url_seccionada);
                    ob_start();
                    $cache_url = str_replace("/", "_", $url);
                    Cache::inicio($cache_url, $cache_url);
                    $noticias->ver();
                    Cache::fin(6 * 60 * 60);
                    ob_clean();

                    $informe .= 'URL: ' . $url . ' ... ' . $contador . ' procesada. [' . round((memory_get_usage() / 1024) / 1024, 2) . ' MB]' . "\n";
                    $cacheadas++;
                    //sleep(1);
                    unset($noticias);
                } catch (NoEncontrado404Excepcion $excepcion) {
                    $informe .= 'Error :: URL: ' . $url . ' ... ' . $contador . ' procesada. [' . round((memory_get_usage() / 1024) / 1024, 2) . ' MB]' . " :: Detalle: " . $excepcion->getMessage() . "\n";
                } catch (Exception $excepcion) {
                    $informe .= 'Error :: URL: ' . $url . ' ... ' . $contador . ' procesada. [' . round((memory_get_usage() / 1024) / 1024, 2) . ' MB]' . " :: Detalle: " . $excepcion->getMessage() . "\n";
                }

            } else {
                $informe .= 'Error :: URL: ' . $url . ' ... ' . $contador . ' procesada. [' . round((memory_get_usage() / 1024) / 1024, 2) . ' MB]' . " :: Detalle: Fuera del nivel de cacheo.\n";
            }
            $contador++;
            if ($contador >= $limite) {
                break;
            }
        }

        $informe .= "\nTotal Urls: " . count($urls) . "- Total cacheadas: " . $cacheadas . "\n\n";

        $informe .= "\nFIN";

        $informe = 'Fecha: ' . $fecha . " - Ip: " . $this->getRealIpAddr() . "\n\n" . $informe;
        file_put_contents(BASE_PATH_APP . 'logs' . DIRSEP . 'cache_' . $fecha . '.txt', $informe);

        echo "hola";
        exit;
        header("Content-type: text/plain", true);
        echo "¿Seguro que te has perdido?\n\n";
        echo "Yo creo que no.";
        //	echo ini_get("memory_limit");
        exit;
    }

    private function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    private function getUrls()
    {

        $base_url = Cargador::cargar('Configuracion')->BASE_URL;

        $urls = array();
        //$urls[] = 'inicio_index';
        //$urls[] = 'inicio_mapadelsitio';
        //$urls[] = 'inicio_faq';
        //$urls[] = 'inicio_sobre';
        //$urls[] = 'inicio_accesibilidad';
        //$urls[] = 'noticias_index';
        //$urls[] = 'blogs_index';
        //$urls[] = 'rss_index';

        $general = Cargador::cargar('Modelo')->Noticias;

        $secciones = $general->listadoSecciones();

        foreach ($secciones as $seccion) {
            $total = isset($seccion["secciones"]) == TRUE ? count($seccion["secciones"]) : 0;
            if ($total == 0) {
                $sec = Base::enlaceUrl($seccion["titulo"]);
                $urls[] = "noticias/ver/" . $sec;
                $fuentes = $general->listadoFuentesPorSeccion($seccion["titulo"]);
                foreach ($fuentes as $fuente) {
                    $urls[] = "noticias/ver/" . $sec . '/' . Base::enlaceUrl($fuente["titulo"]);
                }
            } else {
                foreach ($seccion["secciones"] as $sub_seccion) {
                    $sec = Base::enlaceUrl($sub_seccion["titulo"]);
                    $urls[] = "noticias/ver/" . $sec;
                    $fuentes = $general->listadoFuentesPorSeccion($sub_seccion["titulo"]);
                    foreach ($fuentes as $fuente) {
                        $urls[] = "noticias/ver/" . $sec . '/' . Base::enlaceUrl($fuente["titulo"]);
                    }
                }
            }
        }

        return $urls;
    }

}

?>