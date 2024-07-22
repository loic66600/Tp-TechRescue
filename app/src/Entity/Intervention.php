<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\OneToOne(mappedBy: 'intervention', cascade: ['persist', 'remove'])]
    private ?Ticket $ticket = null;

    /**
     * @var Collection<int, InterventionStock>
     */
    #[ORM\OneToMany(mappedBy: 'intervention', targetEntity: InterventionStock::class)]
    private Collection $interventionStocks;

    public function __construct()
    {
        $this->interventionStocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        // unset the owning side of the relation if necessary
        if ($ticket === null && $this->ticket !== null) {
            $this->ticket->setIntervention(null);
        }

        // set the owning side of the relation if necessary
        if ($ticket !== null && $ticket->getIntervention() !== $this) {
            $ticket->setIntervention($this);
        }

        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return Collection<int, InterventionStock>
     */
    public function getInterventionStocks(): Collection
    {
        return $this->interventionStocks;
    }

    public function addInterventionStock(InterventionStock $interventionStock): static
    {
        if (!$this->interventionStocks->contains($interventionStock)) {
            $this->interventionStocks->add($interventionStock);
            $interventionStock->setIntervention($this);
        }

        return $this;
    }

    public function removeInterventionStock(InterventionStock $interventionStock): static
    {
        if ($this->interventionStocks->removeElement($interventionStock)) {
            // set the owning side to null (unless already changed)
            if ($interventionStock->getIntervention() === $this) {
                $interventionStock->setIntervention(null);
            }
        }

        return $this;
    }
}
