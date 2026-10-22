<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use Firebase\JWT\JWT;
use nrv\application\dto\UtilisateurDTO;
use nrv\application\exceptions\EmailDejaUtiliseException;
use nrv\application\exceptions\IdentifiantsInvalidesException;
use nrv\application\ports\api\ServiceUtilisateurInterface;
use nrv\application\ports\spi\UtilisateurRepositoryInterface;
use nrv\domain\entities\Role;
use nrv\domain\entities\Utilisateur;
use Ramsey\Uuid\Uuid;

class ServiceUtilisateur implements ServiceUtilisateurInterface
{
    private const dureeToken = 3600;

    public function __construct(private UtilisateurRepositoryInterface $utilisateurRepository, private string $jwtSecret) {}

    public function inscrire(string $nom, string $prenom, string $email, string $password): void
    {
        if ($this->utilisateurRepository->findByEmail($email) !== null) {
            throw new EmailDejaUtiliseException("Cet email est déjà utilisé.");
        }

        $this->utilisateurRepository->creer(new Utilisateur(
            Uuid::uuid4()->toString(),
            $nom,
            $prenom,
            $email,
            password_hash($password, PASSWORD_BCRYPT),
            Role::CLIENT
        ));
    }

    public function connecter(string $email, string $password): UtilisateurDTO
    {
        $utilisateur = $this->utilisateurRepository->findByEmail($email);

        if ($utilisateur === null || !password_verify($password, $utilisateur->getPasswordHash())) {
            throw new IdentifiantsInvalidesException("Identifiants invalides.");
        }

        $now = time();
        $token = JWT::encode([
            'iat' => $now,
            'exp' => $now + self::dureeToken,
            'sub' => $utilisateur->getId(),
            'role' => $utilisateur->getRole()->value
        ], $this->jwtSecret, 'HS256');

        return new UtilisateurDTO(
            $token,
            $utilisateur->getNom(),
            $utilisateur->getPrenom(),
            $utilisateur->getRole()->value
        );
    }
}
