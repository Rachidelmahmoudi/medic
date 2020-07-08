<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FactureConsultationExamenRepository")
 */
class FactureConsultationExamen
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Facture", inversedBy="consult_examen",cascade={"persist"})
     */
    private $facture;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ConsultationExamen", inversedBy="factureConsultationExamens")
     */
    private $consult_examen;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): self
    {
        $this->facture = $facture;

        return $this;
    }

    public function getConsultExamen(): ?ConsultationExamen
    {
        return $this->consult_examen;
    }

    public function setConsultExamen(?ConsultationExamen $consult_examen): self
    {
        $this->consult_examen = $consult_examen;

        return $this;
    }


}
