<?php
declare(strict_types=1);

use nrv\domain\entities\Spectacle;
use PHPUnit\Framework\TestCase;

class SpectacleTest extends TestCase
{
    private function creerSpectacle(?string $image = 'rock.jpg', ?string $video = null): Spectacle
    {
        return new Spectacle(
            'sp1',
            'Rock en scène',
            'Les Riffs',
            'Concert rock',
            'rock',
            $image,
            $video,
            new DateTimeImmutable('2026-11-06 20:00'),
            's1',
            new DateTimeImmutable('2026-11-06')
        );
    }

    public function testGetters(): void
    {
        $spectacle = $this->creerSpectacle();
        $this->assertSame('sp1', $spectacle->getId());
        $this->assertSame('Rock en scène', $spectacle->getTitre());
        $this->assertSame('Les Riffs', $spectacle->getArtistes());
        $this->assertSame('Concert rock', $spectacle->getDescription());
        $this->assertSame('rock', $spectacle->getStyle());
        $this->assertSame('s1', $spectacle->getSoireeId());
    }

    public function testDateEtHeure(): void
    {
        $spectacle = $this->creerSpectacle();
        $this->assertSame('2026-11-06', $spectacle->getDate()->format('Y-m-d'));
        $this->assertSame('20:00', $spectacle->getHeure()->format('H:i'));
    }
}
