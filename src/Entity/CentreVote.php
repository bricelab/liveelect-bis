<?php

namespace App\Entity;

use App\Repository\CentreVoteRepository;
use Bricelab\Doctrine\TimestampSetter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CentreVoteRepository::class)]
#[ORM\UniqueConstraint(fields: ['nom', 'villageQuartier'])]
#[ORM\HasLifecycleCallbacks]
class CentreVote
{
    use TimestampSetter;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Item:CentreVote', 'read:Item:PosteVote', 'read:Item:Me'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Item:CentreVote', 'read:Item:PosteVote', 'read:Item:Me'])]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?VillageQuartier $villageQuartier = null;

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

    public function getVillageQuartier(): ?VillageQuartier
    {
        return $this->villageQuartier;
    }

    public function setVillageQuartier(?VillageQuartier $villageQuartier): self
    {
        $this->villageQuartier = $villageQuartier;

        return $this;
    }
}
