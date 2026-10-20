<?php
use nrv\adapters\actions\GetSoireeAction;
use nrv\adapters\persistence\SoireeRepository;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\usecases\ServiceSoiree;
use Psr\Container\ContainerInterface;

return [
    'nrv.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/.env');
        $dsn = "pgsql:host={$config['DB_HOST']};dbname={$config['DB_NAME']}";
        $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
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
];