<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FactureRepository")
 */
class Facture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rapport;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FactureConsultationExamen", mappedBy="facture")
     */
    private $consult_examen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $num_facture;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_facture;

    public function __construct()
    {
        $this->consult_examen = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFactureId(): ?string
    {
        return $this->facture_id;
    }

    public function setFactureId(?string $facture_id): self
    {
        $this->facture_id = $facture_id;

        return $this;
    }

    public function getRapport(): ?string
    {
        return $this->rapport;
    }

    public function setRapport(?string $rapport): self
    {
        $this->rapport = $rapport;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|FactureConsultationExamen[]
     */
    public function getConsultExamen(): Collection
    {
        return $this->consult_examen;
    }

    public function addConsultExaman(FactureConsultationExamen $consultExaman): self
    {
        if (!$this->consult_examen->contains($consultExaman)) {
            $this->consult_examen[] = $consultExaman;
            $consultExaman->setFacture($this);
        }

        return $this;
    }

    public function removeConsultExaman(FactureConsultationExamen $consultExaman): self
    {
        if ($this->consult_examen->contains($consultExaman)) {
            $this->consult_examen->removeElement($consultExaman);
            // set the owning side to null (unless already changed)
            if ($consultExaman->getFacture() === $this) {
                $consultExaman->setFacture(null);
            }
        }

        return $this;
    }

    public function getDateFacture(): ?\DateTimeInterface
    {
        return $this->date_facture;
    }

    public function setDateFacture(?\DateTimeInterface $date_facture): self
    {
        $this->date_facture = $date_facture;

        return $this;
    }

    public function getNumFacture(): ?string
    {
        return $this->num_facture;
    }

    public function setNumFacture(?string $num_facture): self
    {
        $this->num_facture = $num_facture;

        return $this;
    }
}
