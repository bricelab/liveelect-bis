<?php

namespace App\Entity;

use App\Repository\ResultatParArrondissementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultatParArrondissementRepository::class)]
class ResultatParArrondissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Arrondissement $arrondissement = null;

    #[ORM\Column]
    private ?int $nbInscrits = null;

    #[ORM\Column]
    private ?int $nbVotants = null;

    #[ORM\Column]
    private ?int $nbBulletinsNuls = null;

    #[ORM\OneToMany(mappedBy: 'resultatParArrondissement', targetEntity: SuffragesObtenus::class, orphanRemoval: true)]
    private Collection $suffrages;

    public function __construct()
    {
        $this->suffrages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNbInscrits(): ?int
    {
        return $this->nbInscrits;
    }

    public function setNbInscrits(int $nbInscrits): self
    {
        $this->nbInscrits = $nbInscrits;

        return $this;
    }

    public function getNbVotants(): ?int
    {
        return $this->nbVotants;
    }

    public function setNbVotants(int $nbVotants): self
    {
        $this->nbVotants = $nbVotants;

        return $this;
    }

    public function getNbBulletinsNuls(): ?int
    {
        return $this->nbBulletinsNuls;
    }

    public function setNbBulletinsNuls(int $nbBulletinsNuls): self
    {
        $this->nbBulletinsNuls = $nbBulletinsNuls;

        return $this;
    }

    /**
     * @return Collection<int, SuffragesObtenus>
     */
    public function getSuffrages(): Collection
    {
        return $this->suffrages;
    }

    public function addSuffrage(SuffragesObtenus $suffrage): self
    {
        if (!$this->suffrages->contains($suffrage)) {
            $this->suffrages->add($suffrage);
            $suffrage->setResultatParArrondissement($this);
        }

        return $this;
    }

    public function removeSuffrage(SuffragesObtenus $suffrage): self
    {
        if ($this->suffrages->removeElement($suffrage)) {
            // set the owning side to null (unless already changed)
            if ($suffrage->getResultatParArrondissement() === $this) {
                $suffrage->setResultatParArrondissement(null);
            }
        }

        return $this;
    }
}
