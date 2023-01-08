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

    #[ORM\ManyToMany(targetEntity: Arrondissement::class)]
    #[ORM\JoinTable(name: 'circonscription_arrondissement')]
    #[ORM\JoinColumn(name: 'circonscription_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'arrondissement_id', referencedColumnName: 'id', unique: true)]
    private Collection $arrondissements;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $siege = 0;

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
        }

        return $this;
    }

    public function removeArrondissement(Arrondissement $arrondissement): self
    {
        $this->arrondissements->removeElement($arrondissement);

        return $this;
    }

    public function getSiege(): ?int
    {
        return $this->siege;
    }

    public function setSiege(int $siege): self
    {
        $this->siege = $siege;

        return $this;
    }
}
