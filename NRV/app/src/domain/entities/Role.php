<?php
declare(strict_types=1);

namespace nrv\domain\entities;

enum Role: string
{
    case CLIENT = 'CLIENT';
    case GESTIONNAIRE = 'GESTIONNAIRE';
}