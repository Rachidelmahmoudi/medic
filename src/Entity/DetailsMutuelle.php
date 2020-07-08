<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DetailsMutuelleRepository")
 */
class DetailsMutuelle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\mutuelle", inversedBy="detailsMutuelles")
     */
    private $mutuelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom_adh;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom_adh;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $cin_adh;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $n_mutuelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parente;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNomAdh(): ?string
    {
        return $this->nom_adh;
    }

    public function setNomAdh(?string $nom_adh): self
    {
        $this->nom_adh = $nom_adh;

        return $this;
    }

    public function getPrenomAdh(): ?string
    {
        return $this->prenom_adh;
    }

    public function setPrenomAdh(?string $prenom_adh): self
    {
        $this->prenom_adh = $prenom_adh;

        return $this;
    }

    public function getCinAdh(): ?string
    {
        return $this->cin_adh;
    }

    public function setCinAdh(?string $cin_adh): self
    {
        $this->cin_adh = $cin_adh;

        return $this;
    }

    public function getNMutuelle(): ?string
    {
        return $this->n_mutuelle;
    }

    public function setNMutuelle(?string $n_mutuelle): self
    {
        $this->n_mutuelle = $n_mutuelle;

        return $this;
    }

    public function getParente(): ?string
    {
        return $this->parente;
    }

    public function setParente(?string $parente): self
    {
        $this->parente = $parente;

        return $this;
    }
}
