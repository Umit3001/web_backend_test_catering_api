<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/', App\Controllers\IndexController::class . '@test');

$router->post('/createfacility', App\Controllers\FacilityController::class . '@createFacility');
$router->get('/readonefacility', App\Controllers\FacilityController::class . '@readOneFacility');  
$router->put('/updatefacility', App\Controllers\FacilityController::class . '@updateFacility');
$router->delete('/deletefacility', App\Controllers\FacilityController::class . '@deleteFacility');
$router->get('/searchfacility', App\Controllers\FacilityController::class . '@searchFacility');
