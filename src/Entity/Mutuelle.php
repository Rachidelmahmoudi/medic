<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MutuelleRepository")
 */
class Mutuelle
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
     * @ORM\OneToMany(targetEntity="App\Entity\DetailsMutuelle", mappedBy="mutuelle")
     */
    private $detailsMutuelles;

    public function __construct()
    {
        $this->detailsMutuelles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|DetailsMutuelle[]
     */
    public function getDetailsMutuelles(): Collection
    {
        return $this->detailsMutuelles;
    }

    public function addDetailsMutuelle(DetailsMutuelle $detailsMutuelle): self
    {
        if (!$this->detailsMutuelles->contains($detailsMutuelle)) {
            $this->detailsMutuelles[] = $detailsMutuelle;
            $detailsMutuelle->setMutuelle($this);
        }

        return $this;
    }

    public function removeDetailsMutuelle(DetailsMutuelle $detailsMutuelle): self
    {
        if ($this->detailsMutuelles->contains($detailsMutuelle)) {
            $this->detailsMutuelles->removeElement($detailsMutuelle);
            // set the owning side to null (unless already changed)
            if ($detailsMutuelle->getMutuelle() === $this) {
                $detailsMutuelle->setMutuelle(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom() != null ? $this->getNom()  : "";
        // TODO: Implement __toString() method.
    }
}
