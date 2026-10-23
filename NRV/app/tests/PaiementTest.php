<?php
declare(strict_types=1);

use nrv\application\exceptions\CommandeNotFoundException;
use nrv\application\exceptions\ValidationException;
use nrv\application\ports\spi\CommandeRepositoryInterface;
use nrv\application\ports\spi\PanierRepositoryInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\usecases\ServiceCommande;
use nrv\application\validators\PaiementValidator;
use nrv\domain\entities\Commande;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\StatutCommande;
use nrv\domain\exceptions\CommandeDejaPayeeException;
use nrv\domain\exceptions\PlacesInsuffisantesException;
use PHPUnit\Framework\TestCase;

class PaiementTest extends TestCase
{
    private function dateFuture(): string
    {
        return (new DateTimeImmutable('+2 years'))->format('m/y');
    }

    private function creerCommande(StatutCommande $statut = StatutCommande::EN_ATTENTE): Commande
    {
        return new Commande('c1', 'u1', new DateTimeImmutable(), 54.0, $statut);
    }

    private function creerSoiree(int $capacite = 500, int $vendues = 0): Soiree
    {
        return new Soiree(
            's1',
            'Nuit de fer',
            'Soirée métal',
            new DateTimeImmutable('2026-11-06'),
            new DateTimeImmutable('2026-11-06 19:30'),
            32.0,
            22.0,
            new Lieu('l1', "L'Autre Canal", 'Nancy', $capacite),
            [],
            $vendues
        );
    }

    private function creerService(?Commande $commande, array $billets, Soiree $soiree, ?CommandeRepositoryInterface &$repo = null): ServiceCommande
    {
        $repo = $this->createMock(CommandeRepositoryInterface::class);
        $repo->method('findById')->willReturn($commande);
        $repo->method('compterBillets')->willReturn($billets);

        $soireeRepo = $this->createStub(SoireeRepositoryInterface::class);
        $soireeRepo->method('findById')->willReturn($soiree);

        return new ServiceCommande($this->createStub(PanierRepositoryInterface::class), $repo, $soireeRepo);
    }


    public function testValidatorAccepteCarteValide(): void
    {
        (new PaiementValidator())->validate('4242424242424242', $this->dateFuture(), '123');
        $this->addToAssertionCount(1);
    }

    public function testValidatorRefuseNumeroInvalide(): void
    {
        try {
            (new PaiementValidator())->validate('1234', $this->dateFuture(), '123');
            $this->fail('ValidationException attendue');
        } catch (ValidationException $e) {
            $this->assertSame(['numero_carte'], array_keys($e->getErrors()));
        }
    }

    public function testValidatorRefuseCarteExpiree(): void
    {
        try {
            (new PaiementValidator())->validate('4242424242424242', '01/20', '123');
            $this->fail('ValidationException attendue');
        } catch (ValidationException $e) {
            $this->assertSame('La carte est expirée.', $e->getErrors()['expiration']);
        }
    }

    public function testPayerPasseLaCommandeAPayee(): void
    {
        $commande = $this->creerCommande();
        $commande->payer();
        $this->assertTrue($commande->estPayee());
        $this->assertSame(StatutCommande::PAYEE, $commande->getStatut());
    }

    public function testPayerDeuxFoisLeveException(): void
    {
        $commande = $this->creerCommande();
        $commande->payer();
        $this->expectException(CommandeDejaPayeeException::class);
        $commande->payer();
    }


    public function testPayerCommandeSucces(): void
    {
        $soiree = $this->creerSoiree(500, 10);
        $service = $this->creerService($this->creerCommande(), ['s1' => 3], $soiree, $repo);
        $repo->expects($this->once())->method('enregistrerPaiement');

        $dto = $service->payerCommande('u1', 'c1', '4242424242424242', $this->dateFuture(), '123');

        $this->assertSame('PAYEE', $dto->statut);
        $this->assertSame(3, $dto->nbBillets);
        $this->assertSame(54.0, $dto->montantTotal);
        $this->assertSame(13, $soiree->getNbPlacesVendues());
    }

    public function testPayerCommandeCarteInvalideNeTouchePasLaBase(): void
    {
        $service = $this->creerService($this->creerCommande(), ['s1' => 1], $this->creerSoiree(), $repo);
        $repo->expects($this->never())->method('enregistrerPaiement');

        $this->expectException(ValidationException::class);
        $service->payerCommande('u1', 'c1', 'abc', $this->dateFuture(), '123');
    }

    public function testPayerCommandeDUnAutreUtilisateur(): void
    {
        $service = $this->creerService($this->creerCommande(), ['s1' => 1], $this->creerSoiree(), $repo);
        $repo->expects($this->never())->method('enregistrerPaiement');

        $this->expectException(CommandeNotFoundException::class);
        $service->payerCommande('autre', 'c1', '4242424242424242', $this->dateFuture(), '123');
    }

    public function testPayerCommandeDejaPayee(): void
    {
        $service = $this->creerService($this->creerCommande(StatutCommande::PAYEE), ['s1' => 1], $this->creerSoiree(), $repo);
        $repo->expects($this->never())->method('enregistrerPaiement');

        $this->expectException(CommandeDejaPayeeException::class);
        $service->payerCommande('u1', 'c1', '4242424242424242', $this->dateFuture(), '123');
    }

    public function testPayerCommandePlacesInsuffisantes(): void
    {
        $service = $this->creerService($this->creerCommande(), ['s1' => 5], $this->creerSoiree(100, 98), $repo);
        $repo->expects($this->never())->method('enregistrerPaiement');

        $this->expectException(PlacesInsuffisantesException::class);
        $service->payerCommande('u1', 'c1', '4242424242424242', $this->dateFuture(), '123');
    }
}
