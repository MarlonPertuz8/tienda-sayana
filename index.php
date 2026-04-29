<?php

require_once("Config/config.php");
require_once("Helpers/Helpers.php");

$url = !empty($_GET['url']) ? $_GET['url'] : 'Home/index';
$arrUrl = explode("/", $url);

$controller = ucfirst($arrUrl[0]); // Home
$method = "index";                 // método por defecto
$params = "";

if (!empty($arrUrl[1])) {
    $method = $arrUrl[1];
}

if (!empty($arrUrl[2])) {
    for ($i = 2; $i < count($arrUrl); $i++) {
        $params .= $arrUrl[$i] . ",";
    }
    $params = trim($params, ",");
}

require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");

?>