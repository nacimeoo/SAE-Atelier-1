<?php
declare(strict_types=1);

use nrv\adapters\actions\GetSoireeAction;  
use nrv\adapters\actions\GetSpectacleAction; 

return function (\Slim\App $app): \Slim\App {

    $app->get('/soirees/{id}', GetSoireeAction::class)->setName('soiree');
    $app->get('/spectacles', GetSpectacleAction::class)->setName('spectacles');

    return $app;
};