<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 */
class Patient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $cin;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $sexe;



    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $tel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mutuelle")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $mutuelle;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_naiss;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $cin_delivre_le;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $cin_delivre_a;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $situation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ville")
     */
    private $ville;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consultation", mappedBy="patient")
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

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(?string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }



    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMutuelle(): ?mutuelle
    {
        return $this->mutuelle;
    }

    public function setMutuelle(?mutuelle $mutuelle): self
    {
        $this->mutuelle = $mutuelle;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDateNaiss(): ?\DateTimeInterface
    {
        return $this->date_naiss;
    }

    public function setDateNaiss(?\DateTimeInterface $date_naiss): self
    {
        $this->date_naiss = $date_naiss;

        return $this;
    }

    public function getCinDelivreLe(): ?\DateTimeInterface
    {
        return $this->cin_delivre_le;
    }

    public function setCinDelivreLe(?\DateTimeInterface $cin_delivre_le): self
    {
        $this->cin_delivre_le = $cin_delivre_le;

        return $this;
    }

    public function getCinDelivreA(): ?string
    {
        return $this->cin_delivre_a;
    }

    public function setCinDelivreA(?string $cin_delivre_a): self
    {
        $this->cin_delivre_a = $cin_delivre_a;

        return $this;
    }

    public function getSituation(): ?string
    {
        return $this->situation;
    }

    public function setSituation(?string $situation): self
    {
        $this->situation = $situation;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

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
            $consultation->setPatient($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->contains($consultation)) {
            $this->consultations->removeElement($consultation);
            // set the owning side to null (unless already changed)
            if ($consultation->getPatient() === $this) {
                $consultation->setPatient(null);
            }
        }

        return $this;
    }
}
