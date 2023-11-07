<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $test_title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $test_date = null;

    #[ORM\Column]
    private ?bool $test_statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTestTitle(): ?string
    {
        return $this->test_title;
    }

    public function setTestTitle(string $test_title): static
    {
        $this->test_title = $test_title;

        return $this;
    }

    public function getTestDate(): ?\DateTimeInterface
    {
        return $this->test_date;
    }

    public function setTestDate(\DateTimeInterface $test_date): static
    {
        $this->test_date = $test_date;

        return $this;
    }

    public function isTestStatut(): ?bool
    {
        return $this->test_statut;
    }

    public function setTestStatut(bool $test_statut): static
    {
        $this->test_statut = $test_statut;

        return $this;
    }
}
