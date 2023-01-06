<?php

namespace App\Entity;

use App\Repository\CirconscriptionRepository;
use Bricelab\Doctrine\TimestampSetter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CirconscriptionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Circonscription
{
    use TimestampSetter;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'circonscription', targetEntity: Arrondissement::class)]
    private Collection $arrondissements;

    public function __construct()
    {
        $this->arrondissements = new ArrayCollection();
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

    /**
     * @return Collection<int, Arrondissement>
     */
    public function getArrondissements(): Collection
    {
        return $this->arrondissements;
    }

    public function addArrondissement(Arrondissement $arrondissement): self
    {
        if (!$this->arrondissements->contains($arrondissement)) {
            $this->arrondissements->add($arrondissement);
            $arrondissement->setCirconscription($this);
        }

        return $this;
    }

    public function removeArrondissement(Arrondissement $arrondissement): self
    {
        if ($this->arrondissements->removeElement($arrondissement)) {
            // set the owning side to null (unless already changed)
            if ($arrondissement->getCirconscription() === $this) {
                $arrondissement->setCirconscription(null);
            }
        }

        return $this;
    }
}
