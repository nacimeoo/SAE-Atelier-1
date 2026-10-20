<?php
declare(strict_types=1);

namespace nrv\domain\entities;

enum StatutCommande: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case PAYEE = 'PAYEE';
}