<?php
if (empty($medio["fuente"]) == FALSE) {

    echo '<div class="diario_barra_top diario_barra">' . $diario_barra . '</div>';

    if (!empty($medio["fuente"]["imagen"])) {
        echo '<div class="medio_logo"><img src="' . $medio["fuente"]["imagen"] . '" alt="' . $medio["fuente"]["titulo"] . '" width="110" /></div>';
    }
    //echo '<div class="medio_actualizacion">Actualizado el '.$medio["fuente"]["cache_tiempo_fecha"].'<br />
    //<span>Actualización en '.$medio["fuente"]["cache_tiempo_restante"].'</span></div>';
    echo "<h2 class=\"medio_titulo\"><a href=\"" . $medio["fuente"]["url"] . "\">" . $medio["fuente"]["titulo"] . "</a></h2>";

    //$numeracion = (1+$paginacion_inicio);
    //<span><?php echo $numeracion; </span>
    foreach ($medio["noticias"] as $noticia) {
        ?>
        <div class="noticia">
            <h3 class="noticia_titulo"><?php if ($noticia["url"]) echo '<a href="' . $noticia["url"] . '">';
                echo $noticia["titulo"];
                if ($noticia["url"]) echo '</a>'; ?></h3>
            <?php
            if (!empty($noticia["contenido"])) {
                echo '<div class="noticia_contenido">' . $noticia["contenido"] . '</div>';
            }
            ?>
            <div class="noticia_fecha">Publicado el <?php echo $noticia["fecha"]; ?></div>
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

}

?>