<?php
declare(strict_types=1);

use nrv\adapters\actions\GetSoireeAction;   

return function (\Slim\App $app): \Slim\App {
    $app->get('/soirees/{id}', GetSoireeAction::class)->setName('soiree');
    return $app;
};