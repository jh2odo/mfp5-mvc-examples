<div id="contenido">
    <div class="bloque">
        <h2 class="titulo"><strong>Sugerir Medio</strong></h2>

        <p>Rellene el siguiente formulario si desea sugerirnos algun medio.</p>
        <?php
        if (!empty($estado)) {
            echo $estado;
        }
        ?>
        <form action="sugerirmedio/" method="post">
            <fieldset>
                <legend>Datos</legend>
                <p>
                    <label for="tipo" title="Tipo"><strong>Tipo </strong></label>
                    <select id="tipo" name="tipo">
                        <option value="diario" <?php echo($tipo == 'diario' ? 'selected="selected"' : ''); ?>>Diario
                        </option>
                        <option value="blog" <?php echo($tipo == 'blog' ? 'selected="selected"' : ''); ?>>Blog</option>
                        <option value="revista" <?php echo($tipo == 'revista' ? 'selected="selected"' : ''); ?>>
                            Revista
                        </option>
                        <option value="otro" <?php echo($tipo == 'otro' ? 'selected="selected"' : ''); ?>>Otro</option>
                    </select>
                </p>
                <p>
                    <label for="fuente" title="Fuente"><strong>Fuente </strong></label>
                    <input id="fuente" name="fuente" type="text" value="<?php echo $fuente; ?>" size="50"
                           maxlength="255"/>
                    <em>Ejemplo: www.google.es</em>
                </p>

                <p>
                    <label for="nombre" title="Nombre"><strong>Nombre </strong><em>(opcional)</em></label>
                    <input id="nombre" name="nombre" type="text" value="<?php echo $nombre; ?>" size="50"
                           maxlength="40"/> <em>Ejemplo: Pepe</em>
                </p>

                <p>
                    <label for="email" title="Email"><strong>Email </strong><em>(opcional)</em></label>
                    <input id="email" name="email" type="text" value="<?php echo $email; ?>" size="50" maxlength="320"/>
                    <em>Ejemplo: ejemplo@ejemplo.es</em>
                </p>

                <p>
                    <label for="humano" title="Humano ó Maquina"><strong>¿Eres humano? </strong></label>
                    <input id="humano" name="humano" type="text" value="0" size="5"
                           maxlength="5"/> <?php echo $captcha["pregunta"]; ?>
                </p>

                <p><input id="enviar" name="enviar" type="submit" value="Enviar"/></p>
            </fieldset>
        </form>
    </div>
</div>
	