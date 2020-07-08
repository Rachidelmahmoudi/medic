<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsultationExamenRepository")
 */
class ConsultationExamen
{

    

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Examen")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $examen;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Consultation")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $Consultation;
    /**
     * @ORM\Column(type="date", nullable=true )
     */
    private $date_paiement ;

    /**
    * @ORM\Column(type="date", nullable=true)
    */
    private $date_examen;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_resultat;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $prix = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $avance = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $reste = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rapport;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ishaverapport;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ispaied;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FactureConsultationExamen", mappedBy="relation")
     */
    private $factureConsultationExamens;

    public function __construct()
    {
        $this->date_paiement = new \DateTime();
        $this->factureConsultationExamens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExamen(): ?Examen
    {
        return $this->examen;
    }

    public function setExamen(?Examen $examen): self
    {
        $this->examen = $examen;

        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->Consultation;
    }

    public function setConsultation(?Consultation $Consultation): self
    {
        $this->Consultation = $Consultation;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(?\DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateResultat(): ?\DateTimeInterface
    {
        return $this->date_resultat;
    }

    public function setDateResultat(?\DateTimeInterface $date_resultat): self
    {
        $this->date_resultat = $date_resultat;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getAvance(): ?string
    {
        return $this->avance;
    }

    public function setAvance(?string $avance): self
    {
        $this->avance = $avance;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(?int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateExamen(): ?\DateTimeInterface
    {
        return $this->date_examen;
    }

    public function setDateExamen(?\DateTimeInterface $date_examen): self
    {
        $this->date_examen = $date_examen;

        return $this;
    }

    public function getReste(): ?string
    {
        return $this->reste;
    }

    public function setReste(?string $reste): self
    {
        $this->reste = $reste;

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

    public function getIshaverapport(): ?int
    {
        return $this->ishaverapport;
    }

    public function setIshaverapport(?int $ishaverapport): self
    {
        $this->ishaverapport = $ishaverapport;

        return $this;
    }

    public function getIspaied(): ?int
    {
        return $this->ispaied;
    }

    public function setIspaied(?int $ispaied): self
    {
        $this->ispaied = $ispaied;

        return $this;
    }

    /**
     * @return Collection|FactureConsultationExamen[]
     */
    public function getFactureConsultationExamens(): Collection
    {
        return $this->factureConsultationExamens;
    }

    public function addFactureConsultationExamen(FactureConsultationExamen $factureConsultationExamen): self
    {
        if (!$this->factureConsultationExamens->contains($factureConsultationExamen)) {
            $this->factureConsultationExamens[] = $factureConsultationExamen;
            $factureConsultationExamen->setRelation($this);
        }

        return $this;
    }

    public function removeFactureConsultationExamen(FactureConsultationExamen $factureConsultationExamen): self
    {
        if ($this->factureConsultationExamens->contains($factureConsultationExamen)) {
            $this->factureConsultationExamens->removeElement($factureConsultationExamen);
            // set the owning side to null (unless already changed)
            if ($factureConsultationExamen->getRelation() === $this) {
                $factureConsultationExamen->setRelation(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return (string)$this->getStatut();
    }
}
