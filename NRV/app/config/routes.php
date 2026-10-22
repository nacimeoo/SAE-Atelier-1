<?php

declare(strict_types=1);

use nrv\adapters\actions\GetSoireeAction;
use nrv\adapters\actions\GetSpectacleAction;
use nrv\adapters\actions\GetStylesAction;
use nrv\adapters\actions\AjouterAuPanierAction;
use nrv\adapters\actions\LoginAction;
use nrv\adapters\actions\RegisterAction;
use nrv\adapters\middlewares\UtilisateurMiddleware;
use nrv\adapters\actions\GetPanierAction;

return function (\Slim\App $app): \Slim\App {

    $app->get('/soirees/{id}', GetSoireeAction::class)->setName('soiree');
    $app->get('/spectacles', GetSpectacleAction::class)->setName('spectacles');
    $app->get('/styles', GetStylesAction::class)->setName('styles');
    $app->post('/panier/lignes', AjouterAuPanierAction::class)
        ->add(UtilisateurMiddleware::class)
        ->setName('ajouter_au_panier');

    $app->post('/login', LoginAction::class)->setName('login');
    $app->post('/register', RegisterAction::class)->setName('register');

    $app->options('/{routes:.+}', function ($rq, $rs) {
        return $rs->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withStatus(204);
    });

    $app->get('/panier', GetPanierAction::class)->add(UtilisateurMiddleware::class)->setName('panier');

    return $app;
};
