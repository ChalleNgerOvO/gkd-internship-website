<?php

require '../helpers.php';


$uri = $_SERVER['REQUEST_URI'];

if (array_key_exists($uri, $routes)) {
    require(basePath($routes[$uri]));
} else {
    require(basePath($routes['404']));
}
