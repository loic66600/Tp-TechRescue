<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StockRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StockRepository::class)]
#[UniqueEntity(fields: ['referenceNb'], message: 'Cette référence existe déjà')]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $referenceNb = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?User $supplier = null;

    /**
     * @var Collection<int, InterventionStock>
     */
    #[ORM\OneToMany(mappedBy: 'stock', targetEntity: InterventionStock::class)]
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

    public function getReferenceNb(): ?string
    {
        return $this->referenceNb;
    }

    public function setReferenceNb(string $referenceNb): static
    {
        $this->referenceNb = $referenceNb;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getSupplier(): ?User
    {
        return $this->supplier;
    }

    public function setSupplier(?User $supplier): static
    {
        $this->supplier = $supplier;

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
            $interventionStock->setStock($this);
        }

        return $this;
    }

    public function removeInterventionStock(InterventionStock $interventionStock): static
    {
        if ($this->interventionStocks->removeElement($interventionStock)) {
            // set the owning side to null (unless already changed)
            if ($interventionStock->getStock() === $this) {
                $interventionStock->setStock(null);
            }
        }

        return $this;
    }
}
