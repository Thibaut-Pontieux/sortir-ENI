<?php

namespace App\Entity;

use App\Repository\ParticipantsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity(fields={"pseudo"}, message="There is already an account with this pseudo")
 */
/**
 * @ORM\Entity(repositoryClass=ParticipantsRepository::class)
 */
class Participants
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $mdp;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="id_participant", orphanRemoval=true)
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity=Sites::class, inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_site;

    /**
     * @ORM\OneToMany(targetEntity=Sorties::class, mappedBy="id_organisateur", orphanRemoval=true)
     */
    private $sorties_organisees;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->sorties_organisees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Inscriptions>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscriptions $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setIdParticipant($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getIdParticipant() === $this) {
                $inscription->setIdParticipant(null);
            }
        }

        return $this;
    }

    public function getIdSite(): ?Sites
    {
        return $this->id_site;
    }

    public function setIdSite(?Sites $id_site): self
    {
        $this->id_site = $id_site;

        return $this;
    }

    /**
     * @return Collection<int, Sorties>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sorties_organisees;
    }

    public function addSortiesOrganisee(Sorties $sortiesOrganisee): self
    {
        if (!$this->sorties_organisees->contains($sortiesOrganisee)) {
            $this->sorties_organisees[] = $sortiesOrganisee;
            $sortiesOrganisee->setIdOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sorties $sortiesOrganisee): self
    {
        if ($this->sorties_organisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getIdOrganisateur() === $this) {
                $sortiesOrganisee->setIdOrganisateur(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->nom;
    }
}
