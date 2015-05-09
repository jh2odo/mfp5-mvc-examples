<div id="contenido">
    <div class="bloque">
        <h2 class="titulo"><strong>Contacto</strong></h2>

        <p>Rellene el siguiente formulario si desea ponerse en contacto.</p>
        <?php
        if (!empty($estado)) {
            echo $estado;
        }
        ?>
        <form action="contacto/" method="post">
            <fieldset>
                <legend>Datos</legend>
                <p>
                    <label for="nombre" title="Nombre"><strong>Nombre </strong></label>
                    <input id="nombre" name="nombre" type="text" value="<?php echo $nombre; ?>" size="50"
                           maxlength="40"/> <em>Ejemplo: Pepe</em>
                </p>

                <p>
                    <label for="email" title="Email"><strong>Email </strong></label>
                    <input id="email" name="email" type="text" value="<?php echo $email; ?>" size="50" maxlength="320"/>
                    <em>Ejemplo: ejemplo@ejemplo.es</em>
                </p>

                <p>
                    <label for="asunto" title="Asunto"><strong>Asunto </strong></label>
                    <input id="asunto" name="asunto" type="text" value="<?php echo $asunto; ?>" size="50"
                           maxlength="50"/>
                </p>

                <p>
                    <label for="mensaje" title="Mensaje"><strong>Mensaje </strong></label>
                    <textarea id="mensaje" name="mensaje" rows="7" cols="40"><?php echo $mensaje; ?></textarea>
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
