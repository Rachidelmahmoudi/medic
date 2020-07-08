<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrigineRepository")
 */
class Origine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $origine;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consultation", mappedBy="origine")
     */
    private $consultations;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(?string $origine): self
    {
        $this->origine = $origine;

        return $this;
    }

    /**
     * @return Collection|Consultation[]
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): self
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations[] = $consultation;
            $consultation->setOrigine($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->contains($consultation)) {
            $this->consultations->removeElement($consultation);
            // set the owning side to null (unless already changed)
            if ($consultation->getOrigine() === $this) {
                $consultation->setOrigine(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getOrigine() != null ? $this->getOrigine()  : "";
        // TODO: Implement __toString() method.
    }
}
