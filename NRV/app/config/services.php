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
];