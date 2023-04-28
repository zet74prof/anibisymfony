<?php

namespace App\Entity;

use App\Repository\ExpositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpositionRepository::class)]
class Exposition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomExpo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $tarifAdulte = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $tarifEnfant = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToMany(targetEntity: Visite::class, mappedBy: 'expositions')]
    private Collection $visites;

    public function __construct()
    {
        $this->visites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomExpo(): ?string
    {
        return $this->nomExpo;
    }

    public function setNomExpo(string $nomExpo): self
    {
        $this->nomExpo = $nomExpo;

        return $this;
    }

    public function getTarifAdulte(): ?string
    {
        return $this->tarifAdulte;
    }

    public function setTarifAdulte(?string $tarifAdulte): self
    {
        $this->tarifAdulte = $tarifAdulte;

        return $this;
    }

    public function getTarifEnfant(): ?string
    {
        return $this->tarifEnfant;
    }

    public function setTarifEnfant(?string $tarifEnfant): self
    {
        $this->tarifEnfant = $tarifEnfant;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Visite>
     */
    public function getVisites(): Collection
    {
        return $this->visites;
    }

    public function addVisite(Visite $visite): self
    {
        if (!$this->visites->contains($visite)) {
            $this->visites->add($visite);
            $visite->addExposition($this);
        }

        return $this;
    }

    public function removeVisite(Visite $visite): self
    {
        if ($this->visites->removeElement($visite)) {
            $visite->removeExposition($this);
        }

        return $this;
    }
}
