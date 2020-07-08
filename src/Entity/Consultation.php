<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsultationRepository")
 */
class Consultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $user;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_consultation;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix_total = 0;

    /**
    * @ORM\Column(type="float", nullable=true)
    */
    private $montant_regle = 0;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $personne_a_contacter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse_contact;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $tel_contact;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Medecin")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $medecin;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Origine", inversedBy="consultations")
     */
    private $origine;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $R_Clinique;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="consultations")
     */
    private $patient;



    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $avance = 0;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateConsultation(): ?\DateTimeInterface
    {
        return $this->date_consultation;
    }

    public function setDateConsultation(?\DateTimeInterface $date_consultation): self
    {
        $this->date_consultation = $date_consultation;

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prix_total;
    }

    public function setPrixTotal(?float $prix_total): self
    {
        $this->prix_total = $prix_total;

        return $this;
    }

    public function getMontantRegle(): ?float
    {
        return $this->montant_regle;
    }

    public function setMontantRegle(?float $montant_regle): self
    {
        $this->montant_regle = $montant_regle;

        return $this;
    }

    public function getPersonneAContacter(): ?string
    {
        return $this->personne_a_contacter;
    }

    public function setPersonneAContacter(?string $personne_a_contacter): self
    {
        $this->personne_a_contacter = $personne_a_contacter;

        return $this;
    }

    public function getAdresseContact(): ?string
    {
        return $this->adresse_contact;
    }

    public function setAdresseContact(?string $adresse_contact): self
    {
        $this->adresse_contact = $adresse_contact;

        return $this;
    }

    public function getTelContact(): ?string
    {
        return $this->tel_contact;
    }

    public function setTelContact(?string $tel_contact): self
    {
        $this->tel_contact = $tel_contact;

        return $this;
    }

    public function getRClinique(): ?string
    {
        return $this->R_Clinique;
    }

    public function setRClinique(?string $R_Clinique): self
    {
        $this->R_Clinique = $R_Clinique;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): self
    {
        $this->medecin = $medecin;

        return $this;
    }

    public function getOrigine(): ?Origine
    {
        return $this->origine;
    }

    public function setOrigine(?Origine $origine): self
    {
        $this->origine = $origine;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

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

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getAvance(): ?float
    {
        return $this->avance;
    }

    public function setAvance(?float $avance): self
    {
        $this->avance = $avance;

        return $this;
    }
}
