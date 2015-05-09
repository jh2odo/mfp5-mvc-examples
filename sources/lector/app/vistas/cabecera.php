<div id="cabecera">
    <div id="logo">
        <h1><span><?php echo((isset($titulo) == TRUE) ? ($titulo) : 'PrensaSeria'); ?></span></h1>

        <p>Por qué conformarte con un punto de vista, Si los puedes tener todos</p>
    </div>
    <div id="menu">
        <ul>
            <li><a href="inicio/" title="Inicio">Inicio</a></li>
            <li><a href="sugerirmedio/" title="Sugerir Medio">Sugerir Medio</a></li>
            <li class="accesskey"><a href="noticias/" title="Noticias" accesskey="7">Noticias</a></li>
            <li><a href="contacto/" title="Contacto">Contacto</a></li>
        </ul>
    </div>
    <div id="menu-secciones">
        <ul id="menuh">
            <?php
            foreach ($secciones as $seccion) {
                $total = isset($seccion["secciones"]) == TRUE ? count($seccion["secciones"]) : 0;
                if ($total == 0) {
                    echo '<li><a href="noticias/' . Base::enlaceUrl($seccion["titulo"]) . '/" title="' . $seccion["titulo"] . '">' . $seccion["titulo"] . '</a></li>';
                } else {
                    echo '<li><a href="noticias/' . Base::enlaceUrl($seccion["titulo"]) . '/" title="' . $seccion["titulo"] . '">' . $seccion["titulo"] . '</a>';
                    echo '<ul>';
                    echo '<li><a href="noticias/' . Base::enlaceUrl($seccion["titulo"]) . '/" title="' . $seccion["titulo"] . '">General</a></li>';
                    foreach ($seccion["secciones"] as $sub_seccion) {
                        echo '<li><a href="noticias/' . Base::enlaceUrl($sub_seccion["titulo"]) . '/" title="' . $sub_seccion["titulo"] . '">' . $sub_seccion["titulo"] . '</a></li>';
                    }
                    echo '</ul>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
    </div>
    <div id="fecha_actual"><?php echo utf8_encode(strftime("%A, %d %B %Y")); ?></div>
</div>
