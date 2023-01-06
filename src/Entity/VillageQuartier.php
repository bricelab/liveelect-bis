<?php

namespace App\Entity;

use App\Repository\VillageQuartierRepository;
use Bricelab\Doctrine\TimestampSetter;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VillageQuartierRepository::class)]
#[ORM\UniqueConstraint(fields: ['nom', 'arrondissement'])]
#[ORM\HasLifecycleCallbacks]
class VillageQuartier
{
    use TimestampSetter;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Arrondissement $arrondissement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getArrondissement(): ?Arrondissement
    {
        return $this->arrondissement;
    }

    public function setArrondissement(?Arrondissement $arrondissement): self
    {
        $this->arrondissement = $arrondissement;

        return $this;
    }
}
