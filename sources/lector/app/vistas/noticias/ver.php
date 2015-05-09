<?php
$xhtml = Cargador::cargar("Helper")->Xhtml;
$total_fuentes = count($fuentes);

$lector = $titulo_seccion[0]["url"] . $titulo_seccion[1]["url"];

// Usado para el enlace subir
$ruta_actual = "";
foreach ($titulo_seccion as $t) {
    $ruta_actual .= $t["url"];
}

if ($paginacion_inicio != 0 || $paginacion_total != 10) {
    $ruta_actual .= $paginacion_inicio . '-' . $paginacion_total . '/';
}


?>
<div id="contenido">

    <h2 id="ruta" class="titulo"><?php
        $titulo = '';
        $tit_acum = '';
        foreach ($titulo_seccion as $tit) {
            $tit_acum .= $tit["url"];
            $titulo .= ' <a href="' . $tit_acum . '" title="' . trim(str_replace("/", " ", $tit["titulo"])) . '">' . trim(str_replace("/", " ", $tit["titulo"])) . '</a> &gt; ';
        }
        echo substr($titulo, 0, strrpos($titulo, "&gt;"));
        ?></h2>

    <div id="diarios" class="ui-tabs">
        <ul class="ui-tabs-nav">
            <li><a href="<?php echo $lector; ?>#diario-0" title="diario-0" class="tab-puntos-vista"><span>Comparador Experimental <sub
                            style="color: blue">beta</sub></span></a></li>
            <?php
            //echo '<pre>';
            //print_r($fuentes);
            //print_r($medio);
            //exit;


            $carac = array("á", "é", "í", "ó", "ú", "-", " ", "ñ");
            $carac_sano = array("a", "e", "i", "o", "u", "_", "-", "n");

            for ($i = 0; $i < $total_fuentes; $i++) {
                if ((isset($medio["fuente"]) == TRUE) && (($i + 1) == $medio["posicion"])) {
                    ?>
                    <li>
                        <a href="<?php echo $lector . strtolower(str_replace($carac, $carac_sano, $fuentes[$i]["titulo"])) . '/'; ?>#diario-<?php echo($i + 1); ?>"
                           title="diario-<?php echo($i + 1); ?>"<?php echo(strlen($fuentes[$i]["titulo"]) > 22 == TRUE ? ' class="tab-alto"' : ''); ?>
                           rel="nofollow"><span><?php echo $fuentes[$i]["titulo"]; ?></span></a></li>
                <?php
                } else {

                    // Aqui el inicio es a cero porque suponemos que estas son las pestañas de entrada siempre a otras fuentes
                    $li_href = $lector . strtolower(str_replace($carac, $carac_sano, $fuentes[$i]["titulo"])) . '/0-' . $paginacion_total . '/';

                    // Valores por defecto para que no salgan en el html
                    if ($paginacion_inicio == 0 && $paginacion_total == 10) {
                        $li_href = $lector . strtolower(str_replace($carac, $carac_sano, $fuentes[$i]["titulo"])) . '/';
                    }

                    $atributos = array("title" => 'diario-' . ($i + 1), "rel" => 'nofollow');
                    if (strlen($fuentes[$i]["titulo"]) > 22 == TRUE) {
                        $atributos["class"] = 'tab-alto';
                    }
                    echo '<li>' . $xhtml->enlace($li_href, '<span>' . $fuentes[$i]["titulo"] . '</span>', $atributos) . '</li>';
                }
            }
            ?>
        </ul>

        <?php
        if ($total_fuentes == 0) {
            ?>
            <div id="diario-0" class="ui-tabs-panel">
                <p style="text-align:center;color:white;margin-bottom:15px;">Sin Fuentes</p>

                <p>No existe ninguna fuente asociada a esta sección.<br/> <a href="sugerirmedio/" title="Sugerir Medio">Sugerir
                        medio</a></p>
            </div>
        <?php
        } else {
            if (empty($medio["fuente"]) == FALSE) {
                echo '<div id="diario-' . $medio["posicion"] . '" class="ui-tabs-panel">';
                echo '<div class="diario_barra_top diario_barra">' . $diario_barra . '</div>';

                if (!empty($medio["fuente"]["imagen"])) {
                    echo '<div class="medio_logo"><img src="' . $medio["fuente"]["imagen"] . '" alt="' . $medio["fuente"]["titulo"] . '" width="110" /></div>';
                }
                echo '<div class="medio_actualizacion">Actualizado el ' . $medio["fuente"]["cache_tiempo_fecha"] . '</div>';
                //<span>Actualización en '.$medio["fuente"]["cache_tiempo_restante"].'</span></div>';
                echo "<h2 class=\"medio_titulo\">" . (TRUE == FALSE ? '<img src="' . $medio["fuente"]["favicon"] . '" alt="' . $medio["fuente"]["titulo"] . '" height="16" />' : '') . "<a href=\"" . $medio["fuente"]["url"] . "\">" . $medio["fuente"]["titulo"] . "</a></h2>";

                //$numeracion = (1+$paginacion_inicio);
                //<span><?php echo $numeracion; </span>
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
                        ?>
                    </div>
                    <?php
                    //$numeracion++;
                }

                if (empty($medio["noticias"])) {
                    echo '<p>Temporalmente no podemos ofrecerle noticias de este medio. Intentelo en unos minutos.</p><p>Si sigue sin poder ver la noticias, comuniquenoslo. Gracias.</p>';
                }

                echo '<div class="medio_copyright">' . $medio["fuente"]["copyright"] . '</div>';

                //echo '<div class="medio_actualizacion"><span>Actualización en '.$medio["fuente"]["cache_tiempo_restante"].'</span></div>';

                echo '<div class="diario_barra_bottom diario_barra">' . $diario_barra . '</div>';
                echo '</div>' . "\n\n";

                // Para una correcta definicion de titulos
                echo '<div id="diario-0" class="ui-tabs-panel">';
                echo '<h4>' . $titulo_seccion[1]["titulo"] . ' &quot;sobre un mismo hecho&quot;</h4>';
            } else {
                echo '<div id="diario-0" class="ui-tabs-panel">';
                echo '<h2>' . $titulo_seccion[1]["titulo"] . ' &quot;sobre un mismo hecho&quot;</h2>';
            }

            echo '<p style="text-align:center;margin-top:15px;">Comparativa de titulares <sub>Experimental</sub></p>';
            //echo '<p>Actualmente no hay ninguna comparativa disponible. <br />Solo muestra las cinco ultimas noticias de cada medio.</p>';
            //echo '<p>El sistema de visualización de cada medio funciona igual que la anterior versión.</p>';

            /*
            echo '<pre>';
            print_r($cmp_noticias);
            echo '</pre>';


            foreach ($cmp_noticias as $noticia) {
                echo '<a href="'.$noticia["url"].'">'.$noticia["titulo"].'</a> - '.$noticia["fuente"].' <br />';
            }

            */

            foreach ($cmp_noticias as $noticia) {
                echo '<div style="padding:15px;">';
                foreach ($noticia as $not) {
                    echo '<a href="' . $not["url"] . '" title="' . $not["titulo"] . '" rel="external">' . $not["titulo"] . '</a> <strong>-</strong> <span style="font-size:small;">' . $not["fuente"] . '</span> <br />';
                }
                echo '</div>';
            }

            if (count($cmp_noticias) == 0) {
                echo '<div style="padding:15px;">Sin coincidencias.</div>';
            }

            if (false) {
                echo '<div>';
                $general = Cargador::cargar('Modelo')->Noticias;

                $noticias = array();
                for ($i = 0; $i < count($fuentes); $i++) {
                    $tmp = $general->obtenerNoticias($fuentes[$i], 'bd');
                    foreach ($tmp as $clave => $valor) {
                        $noticias[] = $valor;
                    }
                }

                foreach ($noticias as $noticia) {
                    //echo $noticia["titulo"]." - ".$noticia["fuente"]."<br />";
                    $rel = $general->obtenerNoticiasRelacionadas($noticia);
                    if (count($rel) == 0) {
                        //echo "Sin relaciones<br />";
                    } else {
                        echo $noticia["titulo"] . " - " . $noticia["fuente"] . "<br />";
                        foreach ($rel as $not) {
                            echo $not["titulo"] . " - " . $not["fuente"] . "<br />";
                        }
                        echo "<hr />";
                    }
                    //echo "<hr />";
                }
                echo '</div>';
            }
            echo '</div>'; // fin diario 0
            //echo '&nbsp;</div>';

        }

        ?>

        <div class="noticias_informacion" style="margin-top: 50px;">
            <p>Fuentes: <?php echo $total_fuentes; ?></p>
            <h4>Información General</h4>

            <p>Secciones: <?php echo $numero_secciones; ?>
                <br/>Medios: <?php echo $numero_medios; ?>
                <br/>Fuentes: <?php echo $numero_fuentes; ?></p>
            <h4>Fuentes</h4>

            <p>Todas las fuentes son obtenidas de canales de sindicalización.</p>
        </div>


        <div id="ir_principio">
            <a href="<?php echo Cargador::cargar("Enrutador")->getRuta(); ?>#cabecera" title="Ir al principio"><img
                    src="imagenes/flecha_subir.png" alt="Ir al principio"/></a>
        </div>

        <div style="clear: both;">&nbsp;</div>
    </div>
</div>
