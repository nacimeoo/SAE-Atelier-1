<?php

use nrv\adapters\actions\GetSoireeAction;
use nrv\adapters\persistence\SoireeRepository;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\usecases\ServiceSoiree;
use nrv\adapters\actions\GetSpectacleAction;
use nrv\adapters\persistence\SpectacleRepository;
use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\application\usecases\ServiceSpectacle;
use Psr\Container\ContainerInterface;
use nrv\adapters\actions\GetStylesAction;
use nrv\application\ports\api\ServicePanierInterface;
use nrv\application\ports\spi\PanierRepositoryInterface;
use nrv\adapters\actions\AjouterAuPanierAction;
use nrv\adapters\persistence\PanierRepository;
use nrv\application\usecases\ServicePanier;

use nrv\application\ports\api\ServiceUtilisateurInterface;
use nrv\application\ports\spi\UtilisateurRepositoryInterface;
use nrv\adapters\persistence\UtilisateurRepository;
use nrv\application\usecases\ServiceUtilisateur;
use nrv\adapters\actions\LoginAction;
use nrv\adapters\actions\RegisterAction;
use nrv\adapters\middlewares\UtilisateurMiddleware;
use nrv\adapters\actions\GetPanierAction;
use nrv\adapters\actions\GetBilletsAction;
use nrv\adapters\persistence\BilletRepository;
use nrv\application\ports\api\ServiceBilletInterface;
use nrv\application\ports\spi\BilletRepositoryInterface;
use nrv\application\usecases\ServiceBillet;

return [
    'nrv.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/.env');
        $dsn = "{$config['lib.driver']}:host={$config['lib.host']};dbname={$config['lib.database']}";
        $pdo = new PDO($dsn, $config['lib.username'], $config['lib.password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    },

    SoireeRepositoryInterface::class => function (ContainerInterface $c) {
        return new SoireeRepository($c->get('nrv.pdo'));
    },

    ServiceSoireeInterface::class => function (ContainerInterface $c) {
        return new ServiceSoiree($c->get(SoireeRepositoryInterface::class));
    },

    GetSoireeAction::class => function (ContainerInterface $c) {
        return new GetSoireeAction($c->get(ServiceSoireeInterface::class));
    },

    ServiceSpectacleInterface::class => function (ContainerInterface $c) {
        return new ServiceSpectacle($c->get(SpectacleRepositoryInterface::class));
    },

    SpectacleRepositoryInterface::class => function (ContainerInterface $c) {
        return new SpectacleRepository($c->get('nrv.pdo'));
    },

    GetStylesAction::class => function (ContainerInterface $c) {
        return new GetStylesAction($c->get(ServiceSpectacleInterface::class));
    },

    ServicePanierInterface::class => function (ContainerInterface $c) {
        return new ServicePanier($c->get(PanierRepositoryInterface::class), $c->get(SoireeRepositoryInterface::class));
    },

    PanierRepositoryInterface::class => function (ContainerInterface $c) {
        return new PanierRepository($c->get('nrv.pdo'), $c->get(SoireeRepositoryInterface::class));
    },


    UtilisateurRepositoryInterface::class => function (ContainerInterface $c) {
        return new UtilisateurRepository($c->get('nrv.pdo'));
    },

    ServiceUtilisateurInterface::class => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/.env');
        return new ServiceUtilisateur($c->get(UtilisateurRepositoryInterface::class), $config['jwt.secret']);
    },

    LoginAction::class => function (ContainerInterface $c) {
        return new LoginAction($c->get(ServiceUtilisateurInterface::class));
    },

    RegisterAction::class => function (ContainerInterface $c) {
        return new RegisterAction($c->get(ServiceUtilisateurInterface::class));
    },

    UtilisateurMiddleware::class => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/.env');
        return new UtilisateurMiddleware($config['jwt.secret']);
    },

    AjouterAuPanierAction::class => function (ContainerInterface $c) {
        return new AjouterAuPanierAction($c->get(ServicePanierInterface::class));
    },

    GetPanierAction::class => function (ContainerInterface $c) {
        return new GetPanierAction($c->get(ServicePanierInterface::class));
    },

    BilletRepositoryInterface::class => function (ContainerInterface $c) {
        return new BilletRepository($c->get('nrv.pdo'), $c->get(SoireeRepositoryInterface::class));
    },

    ServiceBilletInterface::class => function (ContainerInterface $c) {
        return new ServiceBillet($c->get(BilletRepositoryInterface::class));
    },

    GetBilletsAction::class => function (ContainerInterface $c) {
        return new GetBilletsAction($c->get(ServiceBilletInterface::class));
    },

];
