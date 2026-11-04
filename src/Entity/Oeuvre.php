<?php

namespace App\Entity;

use App\Repository\OeuvreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OeuvreRepository::class)]
class Oeuvre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(max: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\LessThanOrEqual('today', message: 'La date de création ne peut pas être dans le futur.')]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    private ?string $description = null;

    // Renseignée quand l'œuvre a été détruite
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'dateCreation', message: 'La date de destruction doit être après la date de création.')]
    private ?\DateTime $dateDestruction = null;

    /**
     * @var Collection<int, Artiste>
     */
    #[ORM\ManyToMany(targetEntity: Artiste::class, mappedBy: 'oeuvres')]
    private Collection $artistes;

    // Pas de NotNull ici : le lieu est rattaché par le contrôleur après la validation du formulaire
    #[ORM\ManyToOne(inversedBy: 'oeuvres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $lieu = null;

    /**
     * @var Collection<int, Technique>
     */
    #[ORM\ManyToMany(targetEntity: Technique::class, inversedBy: 'oeuvres')]
    #[Assert\Count(min: 1, minMessage: 'Choisissez au moins une technique.')]
    private Collection $techniques;

    /**
     * @var Collection<int, Photos>
     */
    #[ORM\OneToMany(targetEntity: Photos::class, mappedBy: 'oeuvre', cascade: ['remove'], orphanRemoval: true)]
    private Collection $photos;

    // Relation réflexive : l'œuvre (plus ancienne) que celle-ci recouvre
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'recouvertePar')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\Expression('value !== this', message: 'Une œuvre ne peut pas se recouvrir elle-même.')]
    private ?Oeuvre $recouvre = null;

    /**
     * @var Collection<int, Oeuvre>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'recouvre')]
    private Collection $recouvertePar;

    public function __construct()
    {
        $this->artistes = new ArrayCollection();
        $this->techniques = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->recouvertePar = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDestruction(): ?\DateTime
    {
        return $this->dateDestruction;
    }

    public function setDateDestruction(?\DateTime $dateDestruction): static
    {
        $this->dateDestruction = $dateDestruction;

        return $this;
    }

    /**
     * @return Collection<int, Artiste>
     */
    public function getArtistes(): Collection
    {
        return $this->artistes;
    }

    public function addArtiste(Artiste $artiste): static
    {
        if (!$this->artistes->contains($artiste)) {
            $this->artistes->add($artiste);
            $artiste->addOeuvre($this);
        }

        return $this;
    }

    public function removeArtiste(Artiste $artiste): static
    {
        if ($this->artistes->removeElement($artiste)) {
            $artiste->removeOeuvre($this);
        }

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * @return Collection<int, Technique>
     */
    public function getTechniques(): Collection
    {
        return $this->techniques;
    }

    public function addTechnique(Technique $technique): static
    {
        if (!$this->techniques->contains($technique)) {
            $this->techniques->add($technique);
        }

        return $this;
    }

    public function removeTechnique(Technique $technique): static
    {
        $this->techniques->removeElement($technique);

        return $this;
    }

    /**
     * @return Collection<int, Photos>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photos $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setOeuvre($this);
        }

        return $this;
    }

    public function removePhoto(Photos $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getOeuvre() === $this) {
                $photo->setOeuvre(null);
            }
        }

        return $this;
    }

    public function isDetruite(): bool
    {
        return null !== $this->dateDestruction;
    }

    public function getRecouvre(): ?Oeuvre
    {
        return $this->recouvre;
    }

    public function setRecouvre(?Oeuvre $recouvre): static
    {
        $this->recouvre = $recouvre;

        return $this;
    }

    /**
     * @return Collection<int, Oeuvre>
     */
    public function getRecouvertePar(): Collection
    {
        return $this->recouvertePar;
    }

    public function isRecouverte(): bool
    {
        return !$this->recouvertePar->isEmpty();
    }

    public function addRecouvertePar(Oeuvre $oeuvre): static
    {
        if (!$this->recouvertePar->contains($oeuvre)) {
            $this->recouvertePar->add($oeuvre);
            $oeuvre->setRecouvre($this);
        }

        return $this;
    }

    public function removeRecouvertePar(Oeuvre $oeuvre): static
    {
        if ($this->recouvertePar->removeElement($oeuvre)) {
            // set the owning side to null (unless already changed)
            if ($oeuvre->getRecouvre() === $this) {
                $oeuvre->setRecouvre(null);
            }
        }

        return $this;
    }
}
