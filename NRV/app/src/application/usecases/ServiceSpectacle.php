<?php
declare(strict_types=1);

namespace nrv\application\services;

use nrv\application\dto\SpectacleDTO;
use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\application\ports\spi\SpectacleRepositoryInterface;

class ServiceSpectacle implements ServiceSpectacleInterface
{
    public function __construct(private SpectacleRepositoryInterface $repository)
    {
    }

    public function listerSpectacles(?string $date = null, ?string $style = null): array
    {
        $spectacles = $this->repository->findAll($date, $style);
        $dto = [];

        foreach ($spectacles as $spectacle) {
            $dto[] = new SpectacleDTO(
                $spectacle->getId(),
                $spectacle->getTitre(),
                $spectacle->getArtistes(),
                $spectacle->getDescription(),
                $spectacle->getStyle(),
                $spectacle->getImage(),
                $spectacle->getUrlVideo(),
                $spectacle->getHeure(),
                $spectacle->getSoireeId(),
                $spectacle->getDate()
            );
        }
        return $dto;
    }
}