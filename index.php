<?php
/**
 * Code minimalisme de d'envoi d'alerte mail pour Leboncoin.fr
 * @version 1.0
 */
ini_set("display_errors", true);

$dirname = dirname(__FILE__);

require $dirname."/lib/lbc.php";
require $dirname."/ConfigManager.php";

$config = ConfigManager::load();

$view = "list-alerts";
if (isset($_GET["a"])) {
    $view = $_GET["a"];
}
$view .= ".phtml";
$fp = @fopen("http://www.leboncoin.fr", "r");
if (!$fp || false === fgetc($fp)) {
    $error = "Cet hébergement ne semble pas permettre la récupération d'information. distantes. L'application ne fonctionnera pas.";
} else {
    fclose($fp);
}

ob_start();
require $dirname."/views/".$view;
$content = ob_get_clean();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Alerte mail pour Leboncoin.fr</title>
        <meta charset="utf-8">
        <style type="text/css">
            body {
                width: 600px;
            }
            table {
                border: 1px solid #CCCCCC;
                width: 500px;
                border-collapse: collapse;
            }
            table td, table th {
                border: 1px solid #DDDDDD;
                padding: 2px 5px;
            }
            .error {
                color: #EF0000;
                font-weight: bold;
                margin: 0;
            }
            form dd {
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <?php if (isset($error)) : ?>
        <p style="color: #EF0000; font-weight: bold;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php echo $content; ?>
    </body>
</html>
