<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\SoireeDTO;
use nrv\application\exceptions\SoireeNotFoundException;

interface ServiceSoireeInterface
{
    public function afficherSoiree(string $id): SoireeDTO;
}
