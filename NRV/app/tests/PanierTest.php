<?php
declare(strict_types=1);

use nrv\domain\entities\Lieu;
use nrv\domain\entities\LignePanier;
use nrv\domain\entities\Panier;
use nrv\domain\entities\Pass;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\TypeTarif;
use PHPUnit\Framework\TestCase;

class PanierTest extends TestCase
{
    private function creerSoiree(string $id = 's1'): Soiree
    {
        return new Soiree(
            $id,
            'Nuit de fer',
            'Soirée métal',
            new DateTimeImmutable('2026-11-06'),
            new DateTimeImmutable('2026-11-06 19:30'),
            32.0,
            22.0,
            new Lieu('l1', "L'Autre Canal", 'Nancy', 500)
        );
    }

    public function testPanierVide(): void
    {
        $panier = new Panier('p1', 'u1');
        $this->assertTrue($panier->estVide());
        $this->assertSame(0, $panier->getNbBillet());
        $this->assertSame(0.0, $panier->getTotal());
    }

    public function testTotalEtNbBillets(): void
    {
        $panier = new Panier('p1', 'u1');
        $panier->ajouterLignePanier(new LignePanier('l1', 2, TypeTarif::NORMAL, $this->creerSoiree()));
        $panier->ajouterLignePanier(new LignePanier('l2', 1, TypeTarif::REDUIT, $this->creerSoiree()));
        $this->assertFalse($panier->estVide());
        $this->assertSame(3, $panier->getNbBillet());
        $this->assertSame(86.0, $panier->getTotal());
    }

    public function testFusionLignesIdentiques(): void
    {
        $panier = new Panier('p1', 'u1');
        $panier->ajouterLignePanier(new LignePanier('l1', 2, TypeTarif::NORMAL, $this->creerSoiree()));
        $panier->ajouterLignePanier(new LignePanier('l2', 3, TypeTarif::NORMAL, $this->creerSoiree()));
        $this->assertCount(1, $panier->getLignesCommande());
        $this->assertSame(5, $panier->getNbBillet());
    }

    public function testSoireesDifferentesNonFusionnees(): void
    {
        $panier = new Panier('p1', 'u1');
        $panier->ajouterLignePanier(new LignePanier('l1', 1, TypeTarif::NORMAL, $this->creerSoiree('s1')));
        $panier->ajouterLignePanier(new LignePanier('l2', 1, TypeTarif::NORMAL, $this->creerSoiree('s2')));
        $this->assertCount(2, $panier->getLignesCommande());
    }

    public function testRemisePass(): void
    {
        $pass = new Pass('pa1', 'Pass', 100.0, 70.0, []);
        $panier = new Panier('p1', 'u1');
        $panier->ajouterLignePanier(new LignePanier('l1', 2, TypeTarif::NORMAL, null, $pass));
        $this->assertSame(194.0, $panier->getTotal());
        $this->assertSame(6.0, $panier->getRemiseTotal());
    }
}
