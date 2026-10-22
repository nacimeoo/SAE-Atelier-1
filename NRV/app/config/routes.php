<?php
declare(strict_types=1);

use nrv\adapters\actions\GetSoireeAction;  
use nrv\adapters\actions\GetSpectacleAction; 
use nrv\adapters\actions\GetStylesAction;
use nrv\adapters\actions\AjouterAuPanierAction;

return function (\Slim\App $app): \Slim\App {

    $app->get('/soirees/{id}', GetSoireeAction::class)->setName('soiree');
    $app->get('/spectacles', GetSpectacleAction::class)->setName('spectacles');
    $app->get('/styles', GetStylesAction::class)->setName('styles');
    $app->post('/panier/{id}/lignes', AjouterAuPanierAction::class)->setName('ajouter_au_panier');

    $app->options('/{routes:.+}', function ($rq, $rs) {
        return $rs->withHeader('Access-Control-Allow-Origin', '*')
                  ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                  ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
                  ->withStatus(204);
    });

    return $app;
};