<?php

class inicio extends Controlador
{

    function index()
    {

        if (!empty($this->parametros)) {
            throw new NoEncontrado404Excepcion("la página 'inicio/index/' no tiene parametros.");
        }

        $general = Cargador::cargar('Modelo')->Noticias;

        $diarios = $general->listadoMedios();

        $pagina = array();
        $pagina["contenido"] = $this->cargarVista('inicio/index', array("diarios" => $diarios))->generar(TRUE);

        $head = array();
        $head["title"] = 'PrensaSeria, contrasta noticias de diversos medios';
        $head["keywords"] = 'prensa seria, prensa digital, diarios, periodicos, almeria, madrid, garrucha, sindicalizacion';
        $head["descripcion"] = 'Por que conformarte con un solo punto de vista si puedes tenerlos todos a la vez. Deporte, Economia, Espectaculo, Internacional, España, Tecnologia, Ultima Hora y Varios';
        $head["robots"] = 'index,follow';
        $head["charset_encoding"] = Cargador::cargar('Configuracion')->CHARSET_ENCODING;
        $head["css"] = NULL;
        $head["js"][] = array("nombre" => "jquery-ui.min.js");
        $head["js"][] = array("nombre" => "general.min.js");
        $head["extras"] = NULL;
        $head["canonical"] = Cargador::cargar('Configuracion')->BASE_URL;

        $titulo = 'Inicio';

        $this->esqueleto($head, $pagina, $titulo);
    }

    function sugerirmedio()
    {

        if (!empty($this->parametros)) {
            throw new NoEncontrado404Excepcion("la página 'inicio/sugerirmedio/' no tiene parametros.");
        }

        // Humano Captcha
        // Iniciamos la sesion para perdurar valores
        $sesion = Cargador::cargar('Sesion');
        $sesion->start();

        $CAPTCHA = Cargador::cargar('Libreria')->Humano;
        $CAPTCHA->setModo(rand(1, 3));
        $CAPTCHA->setTipo('texto');

        $captcha = array();

        $captcha = array();
        $captcha["pregunta"] = $CAPTCHA->automatico();
        $captcha["respuesta"] = $CAPTCHA->getValido();

        // Se ha enviado el formulario
        $formulario = array();
        if (!empty($this->post)) {
            $validado = TRUE; //Por defecto validado
            $enviado = FALSE;

            $mensajeUnico = md5($this->post["tipo"] . $this->post["fuente"]);

            $formulario["tipo"] = $this->post["tipo"];
            $formulario["fuente"] = $this->post["fuente"];

            // Opcionales
            $formulario["nombre"] = (isset($this->post["nombre"]) == TRUE) ? $this->post["nombre"] : 'anonimo';
            $formulario["email"] = (isset($this->post["email"]) == TRUE) ? $this->post["email"] : 'ninguno';

            $formulario["estado"] = '<ul style="color:red;">';
            // Comprobamos que exiten todos los campos obligatorios
            if (!isset($this->post["tipo"]) || !isset($this->post["fuente"]) || !isset($this->post["humano"]) || !isset($this->post["enviar"])) {
                // Posible envio de datos de otra pagina distinta a la original
                $formulario["estado"] .= '<li>Error, utilice el formulario adecuadamente.</li>';
                $validado = FALSE;
            }

            // Validamos que se haya enviado con el boton Enviar
            if ($this->post["enviar"] != "Enviar") {
                $formulario["estado"] .= '<li>Error, envie el formulario correctamente.</li>';
                $validado = FALSE;
            }

            // Validamos el destinatario
            if ($this->post["tipo"] != "diario" && $this->post["tipo"] != "blog"
                && $this->post["tipo"] != "revista" && $this->post["tipo"] != "otro"
            ) {
                $formulario["estado"] .= '<li>El tipo seleccionado no es correcto.</li>';
                $validado = FALSE;
            }

            // Validamos el Asunto y el mensaje
            if (empty($this->post["fuente"])) {
                $formulario["estado"] .= '<li>La fuente no puede estar vacía.</li>';
                $validado = FALSE;
            }

            // Validamos que sea humano
            if (empty($this->post["humano"]) || $this->post["humano"] != $sesion->get("captcha")) {
                $formulario["estado"] .= '<li>No ha contestado correctamente a la pregunta.</li>';
                $validado = FALSE;
            }

            // Validamos el maximo de longitud del campo opcional nombre
            if (strlen($formulario["nombre"]) > 40) {
                $formulario["estado"] .= '<li>El nombre es demasiado grande(máximo 40 caracteres).</li>';
                $validado = FALSE;
            }

            // Validamos el maximo de longitud del campo opcional email
            if (strlen($formulario["email"]) > 320) {
                $formulario["estado"] .= '<li>El email es demasiado grande(máximo 320 caracteres).</li>';
                $validado = FALSE;
            }

            // Comprobamos que no se hayan reenviado...
            if (file_exists(BASE_PATH . 'tmp' . DIRSEP . $mensajeUnico)) {
                $formulario["estado"] .= '<li>Ya se ha enviado y no puede reenviar un mismo mensaje.</li>';
                $validado = FALSE;
            }

            $formulario["estado"] .= '</ul>';

            $formulario["fuente"] = htmlentities(trim($this->post["fuente"]), ENT_QUOTES, 'UTF-8');
            $formulario["nombre"] = htmlentities(trim($formulario["nombre"]), ENT_QUOTES, 'UTF-8'); // Los cojemos de la variable formulario
            $formulario["email"] = htmlentities(trim($formulario["email"]), ENT_QUOTES, 'UTF-8');

            if ($validado === TRUE) {

                $this->post["tipo"] = $this->post["tipo"];
                $this->post["fuente"] = trim($this->post["fuente"]);


                $email = Cargador::cargar('Libreria')->Email;

                $mensaje = "Formulario Sugerir Medio...\r\n\r\nASUNTO: Nuevo " . $this->post["tipo"] . "\r\n\r\nFUENTE: " . $this->post["fuente"];

                $data = array(Cargador::cargar('Configuracion')->EMAIL_DE,
                    Cargador::cargar('Configuracion')->EMAIL_PARA,
                    'Sugerir Medio ' . Cargador::cargar('Configuracion')->DOMINIO,
                    $mensaje);

                $email->setData($data);

                $enviado = $email->enviar();

                if ($enviado === TRUE) {
                    // Para evitar reenvio...
                    file_put_contents(BASE_PATH . 'tmp' . DIRSEP . $mensajeUnico, "enviado");
                    $formulario["tipo"] = 'diario';
                    $formulario["fuente"] = '';
                    $formulario["nombre"] = 'Anónimo';
                    $formulario["email"] = 'Ninguno';
                    $formulario["estado"] = '<p style="color:green;">Enviado correctamente.</p>';
                } else {
                    $formulario["estado"] = '<p style="color:red;">No se puedo enviar. Intentelo en unos minutos</p>';
                }
            }
        } else {
            $formulario["tipo"] = 'diario';
            $formulario["fuente"] = '';
            $formulario["nombre"] = 'Anónimo';
            $formulario["email"] = 'Ninguno';
            $formulario["estado"] = NULL;
        }

        // Guardamos captcha
        $_SESSION["captcha"] = $captcha["respuesta"];

        $formulario["captcha"] = $captcha;

        $pagina = array();
        $pagina["contenido"] = $this->cargarVista('inicio/sugerir', $formulario)->generar(TRUE);
        //$pagina["lateral"] = $this->cargarVista('inicio/lateral')->generar(TRUE);

        $head = array();
        $head["title"] = 'Sugerir Medio, ¿tienes un diario? Recomiendalo - PrensaSeria';
        $head["keywords"] = 'prensa seria, prensa digital, diarios, periodicos, almeria, madrid, garrucha, sugerir fuente, rss, atom, feed, sindicalizacion';
        $head["descripcion"] = 'Recomienda un diario, revista, blog... digital, para tener la oportunidad de contrastar de un vistazo';
        $head["robots"] = 'noindex,follow';
        $head["charset_encoding"] = Cargador::cargar('Configuracion')->CHARSET_ENCODING;
        $head["css"] = NULL;
        $head["js"] = NULL;
        $head["extras"] = NULL;

        $titulo = 'Sugerir Medio';

        $this->esqueleto($head, $pagina, $titulo);
    }

    function contacto()
    {

        if (!empty($this->parametros)) {
            throw new NoEncontrado404Excepcion("la página 'inicio/contacto/' no tiene parametros.");
        }

        // Humano Captcha
        // Iniciamos la sesion para perdurar valores
        $sesion = Cargador::cargar('Sesion');
        $sesion->start();

        $CAPTCHA = Cargador::cargar('Libreria')->Humano;
        $CAPTCHA->setModo(rand(1, 3));
        $CAPTCHA->setTipo('texto');

        $captcha = array();
        $captcha["pregunta"] = $CAPTCHA->automatico();
        $captcha["respuesta"] = $CAPTCHA->getValido();

        // Se ha enviado el formulario
        $formulario = array();
        if (!empty($this->post)) {
            $validado = TRUE; //Por defecto validado
            $enviado = FALSE;

            $mensajeUnico = md5($this->post["nombre"] . $this->post["email"] . $this->post["asunto"] . $this->post["mensaje"]);

            $formulario["asunto"] = $this->post["asunto"];
            $formulario["mensaje"] = $this->post["mensaje"];
            $formulario["nombre"] = $this->post["nombre"];
            $formulario["email"] = $this->post["email"];

            $formulario["estado"] = '<ul style="color:red;">';
            // Comprobamos que exiten todos los campos obligatorios
            if (!isset($this->post["asunto"]) || !isset($this->post["mensaje"]) || !isset($this->post["humano"]) || !isset($this->post["enviar"])) {
                // Posible envio de datos de otra pagina distinta a la original
                $formulario["estado"] .= '<li>Error, utilice el formulario adecuadamente.</li>';
                $validado = FALSE;
            }

            // Validamos que se haya enviado con el boton Enviar
            if ($this->post["enviar"] != "Enviar") {
                $formulario["estado"] .= '<li>Error, envie el formulario correctamente.</li>';
                $validado = FALSE;
            }

            // Validamos el Asunto y el mensaje, nombre y email
            if (empty($this->post["asunto"]) || empty($this->post["mensaje"]) || empty($this->post["nombre"]) || empty($this->post["email"])) {
                $formulario["estado"] .= '<li>El nombre, el email, el asunto y el mensaje no pueden estar vacios.</li>';
                $validado = FALSE;
            }

            // Validamos que sea humano
            if (empty($this->post["humano"]) || $this->post["humano"] != $sesion->get("captcha")) {
                $formulario["estado"] .= '<li>No ha contestado correctamente a la pregunta.</li>';
                $validado = FALSE;
            }

            // Validamos el maximo de longitud del campo nombre
            if (strlen($formulario["nombre"]) > 40) {
                $formulario["estado"] .= '<li>El nombre es demasiado grande(máximo 40 caracteres).</li>';
                $validado = FALSE;
            }

            // Validamos el maximo de longitud del campo nombre
            if (strlen($formulario["nombre"]) > 40) {
                $formulario["estado"] .= '<li>El nombre es demasiado grande(máximo 40 caracteres).</li>';
                $validado = FALSE;
            }

            // Validamos el maximo de longitud del campo asunto
            if (strlen($formulario["asunto"]) > 50) {
                $formulario["estado"] .= '<li>El asunto es demasiado grande(máximo 50 caracteres).</li>';
                $validado = FALSE;
            }

            // Comprobamos que no se hayan reenviado...
            if (file_exists(BASE_PATH . 'tmp' . DIRSEP . $mensajeUnico)) {
                $formulario["estado"] .= '<li>Ya se ha enviado y no puede reenviar un mismo mensaje.</li>';
                $validado = FALSE;
            }

            $formulario["estado"] .= '</ul>';

            $formulario["asunto"] = htmlentities(trim($this->post["asunto"]), ENT_QUOTES, 'UTF-8');
            $formulario["mensaje"] = htmlentities(trim($this->post["mensaje"]), ENT_QUOTES, 'UTF-8');
            $formulario["nombre"] = htmlentities(trim($formulario["nombre"]), ENT_QUOTES, 'UTF-8'); // Los cojemos de la variable formulario
            $formulario["email"] = htmlentities(trim($formulario["email"]), ENT_QUOTES, 'UTF-8');

            if ($validado === TRUE) {

                $this->post["asunto"] = trim($this->post["asunto"]);
                $this->post["mensaje"] = trim($this->post["mensaje"]);
                $this->post["nombre"] = trim($this->post["nombre"]);
                $this->post["email"] = trim($this->post["email"]);


                $email = Cargador::cargar('Libreria')->Email;

                $mensaje = "Formulario de Contacto...\r\n\r\nASUNTO: " . $this->post["asunto"];
                $mensaje .= "\r\n\r\nNOMBRE: " . $this->post["nombre"] . "\r\n\r\nEMAIL: " . $this->post["email"];
                $mensaje .= "\r\n\r\nMENSAJE:\r\n" . $this->post["mensaje"];

                $data = array(Cargador::cargar('Configuracion')->EMAIL_DE,
                    Cargador::cargar('Configuracion')->EMAIL_PARA,
                    'Contacto ' . Cargador::cargar('Configuracion')->DOMINIO,
                    $mensaje);

                $email->setData($data);

                $enviado = $email->enviar();

                if ($enviado === TRUE) {
                    // Para evitar reenvio...
                    file_put_contents(BASE_PATH . 'tmp' . DIRSEP . $mensajeUnico, "enviado");
                    $formulario["estado"] = '<p style="color:green;">Enviado correctamente.</p>';
                    $formulario["asunto"] = '';
                    $formulario["mensaje"] = '';
                    $formulario["nombre"] = '';
                    $formulario["email"] = '';
                } else {
                    $formulario["estado"] = '<p style="color:red;">No se puedo enviar. Intentelo en unos minutos</p>';
                }
            }
        } else {
            $formulario["asunto"] = '';
            $formulario["mensaje"] = '';
            $formulario["nombre"] = '';
            $formulario["email"] = '';
            $formulario["estado"] = NULL;
        }

        // Guardamos captcha
        $sesion->set('captcha', $captcha["respuesta"]);

        $formulario["captcha"] = $captcha;


        $pagina = array();
        $pagina["contenido"] = $this->cargarVista('inicio/contacto', $formulario)->generar(TRUE);

        $head = array();
        $head["title"] = 'Contacto, para contactar con nosotros - PrensaSeria';
        $head["keywords"] = 'prensa seria, prensa digital, diarios, periodicos, almeria, madrid, garrucha, contacto, email, telefono';
        $head["descripcion"] = 'Informacion sobre distintos medios de comunicacion para establecer contacto con nosotros';
        $head["robots"] = 'noindex,follow';
        $head["charset_encoding"] = Cargador::cargar('Configuracion')->CHARSET_ENCODING;
        $head["css"] = NULL;
        $head["js"] = NULL;
        $head["extras"] = NULL;

        $titulo = 'Contacto';

        $this->esqueleto($head, $pagina, $titulo);

    }

    function mapadelsitio()
    {

        if (!empty($this->parametros)) {
            throw new NoEncontrado404Excepcion("la página 'inicio/mapadelsitio/' no tiene parametros.");
        }

        $pagina = array();
        $pagina["contenido"] = $this->cargarVista('inicio/mapadelsitio')->generar(TRUE);

        $head = array();
        $head["title"] = 'Mapa del Sitio, indice con los enlaces importantes - PrensaSeria';
        $head["keywords"] = 'prensa seria, prensa digital, diarios, periodicos, almeria, madrid, garrucha, mapa del sitio, indice, guia';
        $head["descripcion"] = 'Cuestiones que responden a dudas sobre la tematica planteada en nuestro sitio';
        $head["robots"] = 'noindex,follow';
        $head["charset_encoding"] = Cargador::cargar('Configuracion')->CHARSET_ENCODING;
        $head["css"] = NULL;
        $head["js"] = NULL;
        $head["extras"] = NULL;

        $titulo = 'Mapa del Sitio';

        $this->esqueleto($head, $pagina, $titulo);
    }

    public function site()
    {

        if (!empty($this->parametros)) {
            throw new NoEncontrado404Excepcion("la página 'inicio/site/' no tiene parametros.");
        }

        $base_url = Cargador::cargar('Configuracion')->BASE_URL;

        $urls = array(0 => array("loc" => $base_url,
            "lastmod" => "",
            "changefreq" => "monthly",
            "priority" => ""));

        $urls[] = array("loc" => $base_url . "noticias/", "changefreq" => "always");
        $urls[] = array("loc" => $base_url . "mapadelsitio/", "changefreq" => "monthly");
        $urls[] = array("loc" => $base_url . "accesibilidad/", "changefreq" => "yearly");
        $urls[] = array("loc" => $base_url . "blogs/", "changefreq" => "always");

        $general = Cargador::cargar('Modelo')->Noticias;

        $secciones = $general->listadoSecciones();

        foreach ($secciones as $seccion) {
            $total = isset($seccion["secciones"]) == TRUE ? count($seccion["secciones"]) : 0;
            if ($total == 0) {
                $sec = Base::enlaceUrl($seccion["titulo"]);
                $urls[] = array("loc" => $base_url . "noticias/" . $sec . '/');
                $fuentes = $general->listadoFuentesPorSeccion($seccion["titulo"]);
                foreach ($fuentes as $fuente) {
                    $urls[] = array("loc" => $base_url . "noticias/" . $sec . '/' . Base::enlaceUrl($fuente["titulo"]) . '/', "changefreq" => "always");
                }
            } else {
                foreach ($seccion["secciones"] as $sub_seccion) {
                    $sec = Base::enlaceUrl($sub_seccion["titulo"]);
                    $urls[] = array("loc" => $base_url . "noticias/" . $sec . '/');
                    $fuentes = $general->listadoFuentesPorSeccion($sub_seccion["titulo"]);
                    foreach ($fuentes as $fuente) {
                        $urls[] = array("loc" => $base_url . "noticias/" . $sec . '/' . Base::enlaceUrl($fuente["titulo"]) . '/', "changefreq" => "always");
                    }
                }
            }
        }

        $header = array("Content-type" => "text/xml; charset=utf-8");

        $this->cargarVista('inicio/sitemap', array("urls" => $urls), FALSE, $header)->generar();

    }

    function accesibilidad()
    {

        if (!empty($this->parametros)) {
            throw new NoEncontrado404Excepcion("la página 'inicio/accesibilidad/' no tiene parametros.");
        }

        $pagina = array();
        $pagina["contenido"] = $this->cargarVista('inicio/accesibilidad')->generar(TRUE);

        $head = array();
        $head["title"] = 'Accesibilidad, facilitar la navegacion - PrensaSeria';
        $head["keywords"] = 'prensa seria, prensa digital, diarios, periodicos, almeria, madrid, garrucha, accesibilidad, estandares, xhtml, css';
        $head["descripcion"] = 'Facilidad con la que una persona puede navegar, moverse, leer, escuchar... los recursos de un sitio web';
        $head["robots"] = 'index,follow';
        $head["charset_encoding"] = Cargador::cargar('Configuracion')->CHARSET_ENCODING;
        $head["css"] = NULL;
        $head["js"] = NULL;
        $head["extras"] = NULL;

        $titulo = 'Accesibilidad';

        $this->esqueleto($head, $pagina, $titulo);
    }


    private function esqueleto($head = array("head" => array("title" => "PrensaSeria")), $pagina = array(), $titulo = '')
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

}

?>