<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortiesRepository::class)
 */
class Sorties
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_cloture_inscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_inscriptions_max;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description_infos;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $url_photo;

    /**
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="id_sortie", orphanRemoval=true)
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity=Participants::class, inversedBy="sorties_organisees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_organisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Lieux::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_lieu;

    /**
     * @ORM\ManyToOne(targetEntity=Etats::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_etat;

    /**
     * @ORM\ManyToOne(targetEntity=Sites::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_site;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->date_cloture;
    }

    public function setDateCloture(\DateTimeInterface $date_cloture): self
    {
        $this->date_cloture = $date_cloture;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nb_inscriptions_max;
    }

    public function setNbInscriptionsMax(int $nb_inscriptions_max): self
    {
        $this->nb_inscriptions_max = $nb_inscriptions_max;

        return $this;
    }

    public function getDescriptionInfos(): ?string
    {
        return $this->description_infos;
    }

    public function setDescriptionInfos(?string $description_infos): self
    {
        $this->description_infos = $description_infos;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->url_photo;
    }

    public function setUrlPhoto(?string $url_photo): self
    {
        $this->url_photo = $url_photo;

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
            $inscription->setIdSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getIdSortie() === $this) {
                $inscription->setIdSortie(null);
            }
        }

        return $this;
    }

    public function getIdOrganisateur(): ?Participants
    {
        return $this->id_organisateur;
    }

    public function setIdOrganisateur(?Participants $id_organisateur): self
    {
        $this->id_organisateur = $id_organisateur;

        return $this;
    }

    public function getIdLieu(): ?Lieux
    {
        return $this->id_lieu;
    }

    public function setIdLieu(?Lieux $id_lieu): self
    {
        $this->id_lieu = $id_lieu;

        return $this;
    }

    public function getIdEtat(): ?Etats
    {
        return $this->id_etat;
    }

    public function setIdEtat(?Etats $id_etat): self
    {
        $this->id_etat = $id_etat;

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
}
