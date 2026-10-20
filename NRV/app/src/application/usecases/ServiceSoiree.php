<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\SoireeDTO;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;


class ServiceSoiree implements ServiceSoireeInterface
{
    public function __construct(
        private SoireeRepositoryInterface $soireeRepository
    ) {}

    public function afficherSoiree(string $id): SoireeDTO
    {
        $soiree = $this->soireeRepository->findById($id);
        return SoireeDTO::fromEntity($soiree);
    }
}
