<?php

namespace App\Entity;

use App\Doctrine\Types\TypeScrutinType;
use App\Enum\TypeScrutin;
use App\Repository\ScrutinRepository;
use Bricelab\Doctrine\TimestampSetter;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScrutinRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Scrutin implements \Stringable
{
    use TimestampSetter;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Scrutin:Data'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Scrutin:Data'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:Scrutin:Data'])]
    private ?int $year = null;

    #[ORM\Column(type: TypeScrutinType::NAME)]
    #[Groups(['read:Scrutin:Data'])]
    private ?TypeScrutin $type = null;

    #[ORM\Column]
    #[Groups(['read:Scrutin:Data'])]
    private ?bool $published = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:Scrutin:Data'])]
    private ?DateTimeImmutable $publishedAt = null;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getType(): ?TypeScrutin
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = TypeScrutin::tryFrom($type);

        return $this;
    }
}
