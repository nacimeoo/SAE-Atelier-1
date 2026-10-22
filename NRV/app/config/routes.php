<?php
declare(strict_types=1);

use nrv\adapters\actions\GetSoireeAction;  
use nrv\adapters\actions\GetSpectacleAction; 
use nrv\adapters\actions\GetStylesAction;

return function (\Slim\App $app): \Slim\App {

    $app->get('/soirees/{id}', GetSoireeAction::class)->setName('soiree');
    $app->get('/spectacles', GetSpectacleAction::class)->setName('spectacles');
    $app->get('/styles', GetStylesAction::class)->setName('styles');

    return $app;
};