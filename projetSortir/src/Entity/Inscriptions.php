<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionsRepository::class)
 */
class Inscriptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Sorties::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_sortie;

    /**
     * @ORM\ManyToOne(targetEntity=Participants::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_participant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIdSortie(): ?Sorties
    {
        return $this->id_sortie;
    }

    public function setIdSortie(?Sorties $id_sortie): self
    {
        $this->id_sortie = $id_sortie;

        return $this;
    }

    public function getIdParticipant(): ?Participants
    {
        return $this->id_participant;
    }

    public function setIdParticipant(?Participants $id_participant): self
    {
        $this->id_participant = $id_participant;

        return $this;
    }
}
