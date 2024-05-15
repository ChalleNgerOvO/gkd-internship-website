<?php

$router->addGet('/', 'controllers/home.php');
$router->addGet('/listings', 'controllers/listings/index.php');
$router->addGet('/listings/create', 'controllers/listings/create.php');
