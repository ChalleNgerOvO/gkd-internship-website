<?php
$router->addGet('/', 'HomeController@index');
// $router->addGet('/listings', 'controllers/listings/index.php');
// $router->addGet('/listings/create', 'controllers/listings/create.php');
// $router->addGet('/listing', 'controllers/listings/show.php');
$router->addGet('/listings', 'ListingController@index');
$router->addGet('/listings/create', 'ListingController@create');
$router->addGet('/listings/{id}', 'ListingController@show');
$router->addGet('/listings/edit/{id}', 'ListingController@edit');
$router->addPost('/listings', 'ListingController@store');
$router->addPut('/listings/{id}', 'ListingController@update');
$router->addDelete('/listings/{id}', 'ListingController@destroy');
$router->addGet('/auth/register', 'UserController@create');
$router->addGet('/auth/login', 'UserController@login');
$router->addPost('/auth/register', 'UserController@store');
$router->addPost('/auth/logout', 'UserController@logout');
$router->addPost('/auth/login', 'UserController@authenticate');
