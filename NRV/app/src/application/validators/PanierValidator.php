<?php
declare(strict_types=1);

namespace nrv\application\validators;

use nrv\application\exceptions\ValidationException;
use Respect\Validation\ValidatorBuilder as v;

class PanierValidator
{
    public function validate(?string $soireeId, ?string $typeTarif, ?int $quantite): void
    {
        $errors = [];

        if (!v::uuid()->isValid($soireeId)) {
            $errors['soiree_id'] = "L'id de soirée est invalide.";
        }

        if (!v::in(['NORMAL', 'REDUIT'], true)->isValid($typeTarif)) {
            $errors['type_tarif'] = "Le type de tarif doit être NORMAL ou REDUIT.";
        }

        if ($quantite === null || $quantite <= 0) {
            $errors['quantite'] = "La quantité doit être supérieure à 0.";
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
