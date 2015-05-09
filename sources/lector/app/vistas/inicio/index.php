<div id="contenido">
    <div class="bloque">
        <h2 class="titulo"><strong>¿Qué es PrensaSeria?</strong></h2>

        <div style="padding-bottom: 20px;">
            <p>PrensaSeria nació con la intención de acercarles a todos ustedes la posibilidad de contrastar noticias en
                diversos medios, permitiendoles así tener su propio punto de vista.</p>

            <p>Se ha recuaperado el código original (año 2010), realizado pequeños cambios para adecuarlo a la
                publicación en <a href=https://github.com/jh2odo/mfp5-mvc-examples" rel="external">GitHub MFP5 MVC
                    Examples</a>, quedando practicamente igual el diseño y las mismas funciones.</p>

            <p>Las modificaciones han mantenido las bases del origen del proyecto, el <strong>comparador</strong>(rudimentario)
                y lector de noticias.</p>

            <p>El diseño es accesible, es posible usar la web sin cargar los CSS, como un lector de texto. No se adapta
                a los dispositivos.</p>

            <p>Las actualizaciónes sobre el proyecto se publicarán en GitHub</p>

            <p style="text-align: center;padding: 20px;"><img src="imagenes/presentacion.gif"
                                                              alt="Por qué conformarte con un punto de vista, Si los puedes tener todos"/>
            </p>

            <cite>La prensa es la artillería de la libertad. - Hans Dietrich Genscher</cite>
        </div>
    </div>
    <div class="bloque">
        <h2 class="titulo"><strong>Fuentes </strong>actuales</h2>
        <?php
        foreach ($diarios as $diario) {
            echo ' <a href="http://' . $diario["url"] . '" title="' . $diario["titulo"] . '" rel="external">' . $diario["titulo"] . '</a>  |  ';
        }
        ?>
    </div>
</div>
	