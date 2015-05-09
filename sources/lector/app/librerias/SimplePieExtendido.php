<?php
// Cargamos la libreria que esta en el core

include_once 'SimplePie.php';

class SimplePieExtendido extends SimplePie
{

    private $cacheFileObjeto = NULL;

    private function get_cache_object()
    {
        return call_user_func(array($this->cache_class, 'create'), $this->cache_location, call_user_func($this->cache_name_function, $this->feed_url), 'spc');

        if ($this->cache) {
            if ($cacheFileObjeto == NULL) {

                // Devuelve una instancia de SimplePie_Cache_file
                $this->cacheFileObjeto = call_user_func(array($this->cache_class, 'create'), $this->cache_location, call_user_func($this->cache_name_function, $this->feed_url), 'spc');
            }
        } else {
            $this->cacheFileObjeto = NULL;
        }
        return $this->cacheFileObjeto;
    }

    function get_cache_filename()
    {
        $cache = $this->get_cache_object();
        if ($cache == NULL) {
            return false;
        }
        return $cache->name;
    }

    function get_cache_timestamp($fecha = true, $format = false)
    {
        $cache = $this->get_cache_object();
        if ($cache == NULL) {
            return 0;
        }
        $mtime = $cache->mtime();
        if (!$mtime) {
            $mtime = 0;
        }
        if ($fecha) {

            $t = ($mtime + $this->cache_duration) - time();
            if ($t <= 0) {
                $t = time();
            } else {
                $t = $mtime;
            }

            if ($format) {
                $fech = strftime("%A %d de %B de %Y a las %H:%M:%S", $t);
                // Excepcion de encoding
                $encoding = mb_detect_encoding($fech, 'ASCII,UTF-8,ISO-8859-1');
                if ($encoding == "ISO-8859-1") {
                    $fech = utf8_encode($fech);
                }
                return $fech;
            } else {
                return $t;
            }
        } else {
            $t = time() - $mtime;
            if ($t < 0) {
                $t = 0;
            }
            if ($format) {
                return $this->segundosATiempo($t);
            } else {
                return ($t);
            }
        }

    }

    function get_cache_time_remaining($format = false)
    {
        $cache = $this->get_cache_object();
        if ($cache == NULL) {
            return 0;
        }
        $mtime = $cache->mtime();
        if (!$mtime) {
            $mtime = 0;
        }
        $remaining = ($mtime + $this->cache_duration) - time();
        if ($remaining < 0) {
            $remaining = 0;
        }
        if ($format) {
            return $this->segundosATiempo($remaining);
        } else {
            return $remaining;
        }
    }

    private function segundosATiempo($segundos)
    {
        $minutos = $segundos / 60;
        $horas = floor($minutos / 60);

        if ($segundos < 60) { /* segundos */
            $resultado = round($segundos) . ' Segundos';
        } elseif ($segundos > 60 && $segundos < 3600) {/* minutos */
            $resultado = ($minutos % 60) . ' Minutos y ' . ($segundos % 60 % 60 % 60) . ' Segundos';
        } else {/* horas */
            $resultado = $horas . ' Horas, ' . ($minutos % 60) . ' Minutos y ' . ($segundos % 60 % 60 % 60) . ' Segundos';
        }
        return $resultado;
    }


}

?>