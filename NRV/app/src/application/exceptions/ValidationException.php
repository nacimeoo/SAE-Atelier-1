<?php
declare(strict_types=1);

namespace nrv\application\exceptions;

class ValidationException extends \Exception
{
    public function __construct(private array $errors)
    {
        parent::__construct("Données invalides.");
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
