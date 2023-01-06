<?php

namespace App\Entity;

use App\Repository\SuffragesObtenusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuffragesObtenusRepository::class)]
class SuffragesObtenus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidat $candidat = null;

    #[ORM\Column]
    private ?int $nbVoix = null;

    #[ORM\ManyToOne(inversedBy: 'suffrages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ResultatParArrondissement $resultatParArrondissement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    public function getNbVoix(): ?int
    {
        return $this->nbVoix;
    }

    public function setNbVoix(int $nbVoix): self
    {
        $this->nbVoix = $nbVoix;

        return $this;
    }

    public function getResultatParArrondissement(): ?ResultatParArrondissement
    {
        return $this->resultatParArrondissement;
    }

    public function setResultatParArrondissement(?ResultatParArrondissement $resultatParArrondissement): self
    {
        $this->resultatParArrondissement = $resultatParArrondissement;

        return $this;
    }
}
