<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use nrv\application\ports\spi\UtilisateurRepositoryInterface;
use nrv\domain\entities\Role;
use nrv\domain\entities\Utilisateur;
use PDO;

class UtilisateurRepository implements UtilisateurRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new Utilisateur(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['email'],
            $row['password_hash'],
            Role::from($row['role'])
        );
    }

    public function creer(Utilisateur $utilisateur): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO utilisateur (id, nom, prenom, email, password_hash, role)
             VALUES (:id, :nom, :prenom, :email, :password_hash, :role)'
        );
        $stmt->execute([
            'id'            => $utilisateur->getId(),
            'nom'           => $utilisateur->getNom(),
            'prenom'        => $utilisateur->getPrenom(),
            'email'         => $utilisateur->getEmail(),
            'password_hash' => $utilisateur->getPasswordHash(),
            'role'          => $utilisateur->getRole()->value,
        ]);
    }
}
