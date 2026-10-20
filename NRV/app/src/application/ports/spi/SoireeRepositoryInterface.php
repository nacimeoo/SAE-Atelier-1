<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\application\exceptions\SoireeNotFoundException;
use nrv\domain\entities\Soiree;

interface SoireeRepositoryInterface
{
    public function findById(string $id): Soiree;
}
