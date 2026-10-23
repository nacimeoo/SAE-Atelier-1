<?php
declare(strict_types=1);

namespace nrv\application\validators;

use nrv\application\exceptions\ValidationException;

class PaiementValidator
{
    public function validate(?string $numeroCarte, ?string $expiration, ?string $cvc): void
    {
        $errors = [];
        $numero = str_replace(' ', '', (string) $numeroCarte);
        if (!preg_match('/^\d{16}$/', $numero)) {
            $errors['numero_carte'] = "16 chiffre";
        }
        if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', (string) $expiration, $m)) {
            $errors['expiration'] = "MM/AA";
        } else {
            $finDuMois = \DateTimeImmutable::createFromFormat('!y-m-d', "{$m[2]}-{$m[1]}-01")->modify('last day of this month');
            if ($finDuMois < new \DateTimeImmutable('today')) {
                $errors['expiration'] = "La carte est expirée.";
            }
        }
        if (!preg_match('/^\d{3}$/', (string) $cvc)) {
            $errors['cvc'] = "3 chiffres";
        }
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}