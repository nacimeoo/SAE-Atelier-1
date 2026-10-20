<?php
declare(strict_types=1);

namespace nrv\domain\entities;

enum TypeTarif: string
{
    case NORMAL = 'NORMAL';
    case REDUIT = 'REDUIT';
}