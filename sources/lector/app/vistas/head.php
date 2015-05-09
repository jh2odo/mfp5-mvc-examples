<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es">
<head>
    <?php
    $base_url = Cargador::cargar('Configuracion')->BASE_URL;
    $charset_encoding = ((isset($charset_encoding) == TRUE) ? $charset_encoding : Cargador::cargar('Configuracion')->CHARSET_ENCODING);
    ?>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=<?php echo $charset_encoding; ?>"/>
    <meta http-equiv="Content-Style-Type" content="text/css; charset=<?php echo $charset_encoding; ?>"/>
    <meta http-equiv="Content-Script-Type" content="text/javascript; charset=<?php echo $charset_encoding; ?>"/>

    <link href="<?php echo $base_url; ?>imagenes/favicon.ico" rel="shortcut icon" type="image/x-ico"/>
    <link href="<?php echo $base_url; ?>imagenes/favicon.ico" rel="icon" type="image/x-icon"/>

    <?php if (isset($keywords)) { ?>
        <meta name="keywords" content="<?php echo $keywords; ?>"/>
    <?php }
    if (isset($descripcion)) { ?>
        <meta name="description" content="<?php echo $descripcion; ?>"/>
    <?php } ?>

    <meta name="generator" content="MFP5-MVC"/>
    <meta name="robots" content="<?php echo((isset($robots) == TRUE) ? $robots : 'noindex,nofollow'); ?>"/>

    <?php
    Cargador::cargar('Helper')->Xhtml->addCSS($base_url . 'css/general.min.css');
    echo "\n";
    ?>
    <!--[if gte IE 5.5]>
    <script type="text/javascript" src="<?php echo $base_url; ?>js/ie6.min.js" type="text/javascript"></script>
    <![endif]-->
    <?php
    if (isset($css)) {
        foreach ($css as $elemento) {
            Cargador::cargar('Helper')->Xhtml->addCSS($base_url . 'css/' . $elemento["nombre"] . '.' . $elemento["extension"]);
            echo "\n";
        }
    }

    if (isset($js)) {
        foreach ($js as $elemento) {
            if (isset($elemento["nombre"])) {
                Cargador::cargar('Helper')->Xhtml->loadScript($base_url . 'js/' . $elemento["nombre"]);
            } else if (isset($elemento["script"])) {
                echo $elemento["script"] . "\n";
            }
        }
    }

    if (isset($extras)) {
        foreach ($extras as $extra) {
            echo $extra . "\n";
        }
    }
    ?>
    <link rel="canonical"
          href="<?php echo((isset($canonical) == TRUE) ? $canonical : ($base_url . Cargador::cargar('Enrutador')->getRuta())); ?>"/>
    <base href="<?php echo $base_url; ?>"/>
    <title><?php echo $title; ?></title>
</head>