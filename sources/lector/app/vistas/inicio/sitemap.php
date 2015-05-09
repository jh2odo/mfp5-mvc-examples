<?php
echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"" . "?" . ">\n";
?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">
    <?php
    foreach ($urls as $url) {
        if ($url != NULL) {
            echo '<url>' . "\n";
            echo '<loc>' . $url["loc"] . '</loc>' . "\n";
            if (isset($url["lastmod"]) && !empty($url["lastmod"]))
                echo '<lastmod>' . $url["lastmod"] . '</lastmod>' . "\n";
            if (isset($url["changefreq"]) && !empty($url["changefreq"]))
                echo '<changefreq>' . $url["changefreq"] . '</changefreq>' . "\n";
            if (isset($url["priority"]) && !empty($url["priority"]))
                echo '<priority>' . $url["priority"] . '</priority>' . "\n";
            echo '</url>' . "\n";
        }
    }
    ?>
</urlset>