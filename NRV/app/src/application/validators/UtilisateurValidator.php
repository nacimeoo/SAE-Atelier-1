<?php
declare(strict_types=1);

namespace nrv\application\validators;

use nrv\application\exceptions\ValidationException;
use Respect\Validation\ValidatorBuilder as v;

class UtilisateurValidator
{
    public function validate(string $nom, string $prenom, string $email, string $password): void
    {
        $errors = [];

        if ($nom === '') {
            $errors['nom'] = "Le nom est obligatoire.";
        }

        if ($prenom === '') {
            $errors['prenom'] = "Le prénom est obligatoire.";
        }

        if (!v::email()->isValid($email)) {
            $errors['email'] = "L'email est invalide.";
        }

        if (strlen($password) < 8) {
            $errors['password'] = "Le mot de passe doit faire 8 caractères minimum.";
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
