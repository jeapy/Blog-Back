<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
#[ORM\Index(columns: ['slug'], name: 'idx_slug')]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $parent_categorie_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'parent_category')]
    private ?self $parent_categorie = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent_categorie')]
    private Collection $parent_category;

    /**
     * @var Collection<int, Articles>
     */
    #[ORM\ManyToMany(targetEntity: Articles::class, mappedBy: 'categories')]
    private Collection $articles;

    public function __construct()
    {
        $this->parent_category = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->created_at = new DateTimeImmutable();
    }

    public function __toString() {
        return $this->name ?? '';
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getParentCategorieId(): ?int
    {
        return $this->parent_categorie_id;
    }

    public function setParentCategorieId(?int $parent_categorie_id): static
    {
        $this->parent_categorie_id = $parent_categorie_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getParentCategorie(): ?self
    {
        return $this->parent_categorie;
    }

    public function setParentCategorie(?self $parent_categorie): static
    {
        $this->parent_categorie = $parent_categorie;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getParentCategory(): Collection
    {
        return $this->parent_category;
    }

    public function addParentCategory(self $parentCategory): static
    {
        if (!$this->parent_category->contains($parentCategory)) {
            $this->parent_category->add($parentCategory);
            $parentCategory->setParentCategorie($this);
        }

        return $this;
    }

    public function removeParentCategory(self $parentCategory): static
    {
        if ($this->parent_category->removeElement($parentCategory)) {
            // set the owning side to null (unless already changed)
            if ($parentCategory->getParentCategorie() === $this) {
                $parentCategory->setParentCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addCategory($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): static
    {
        if ($this->articles->removeElement($article)) {
            $article->removeCategory($this);
        }

        return $this;
    }
}
