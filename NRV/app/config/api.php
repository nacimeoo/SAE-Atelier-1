<?php
declare(strict_types=1);


use Psr\Container\ContainerInterface;
use nrv\adapters\actions\GetSoireeAction;
use nrv\adapters\actions\GetSpectacleAction;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\ports\api\ServiceSpectacleInterface;

return [
    GetSoireeAction::class => function (ContainerInterface $c) {
        return new GetSoireeAction($c->get(ServiceSoireeInterface::class));
    },

    GetSpectacleAction::class => function (ContainerInterface $c) {
        return new GetSpectacleAction($c->get(ServiceSpectacleInterface::class));
    },
    
];