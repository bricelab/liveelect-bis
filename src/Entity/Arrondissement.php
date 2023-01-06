<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ArrondissementRepository;
use Bricelab\Doctrine\TimestampSetter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArrondissementRepository::class)]
#[ORM\UniqueConstraint(fields: ['nom', 'commune'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ]
)]
class Arrondissement
{
    use TimestampSetter;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Scrutin:Data'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Scrutin:Data'])]
    private ?string $nom = null;

    #[ORM\Column]
    #[Groups(['read:Scrutin:Data'])]
    private ?bool $estRemonte = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Scrutin:Data'])]
    private ?Commune $commune = null;

    #[ORM\ManyToOne(inversedBy: 'arrondissements')]
    private ?Circonscription $circonscription = null;

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

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

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getEstRemonte(): ?bool
    {
        return $this->estRemonte;
    }

    public function setEstRemonte(bool $estRemonte): self
    {
        $this->estRemonte = $estRemonte;

        return $this;
    }

    #[Groups(['read:Item:Me'])]
    public function getCommuneUri(): string
    {
        return '/api/communes/' . $this->commune->getId();
    }

    public function getCirconscription(): ?Circonscription
    {
        return $this->circonscription;
    }

    public function setCirconscription(?Circonscription $circonscription): self
    {
        $this->circonscription = $circonscription;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->nom . ' (' . $this->commune->getNom() . ')';
    }
}
