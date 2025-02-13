<?php

// src/Entity/Product.php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use PHPUnit\TextUI\Configuration\File;

#[ORM\Entity(repositoryClass: ProductRepository::class)] // Définition de l'entité Product avec le repository associé
class Product
{
    #[ORM\Id] // Définition de l'ID comme clé primaire
    #[ORM\GeneratedValue] // L'ID est généré automatiquement
    #[ORM\Column] // La colonne pour l'ID
    private ?int $id = null;

    #[ORM\Column(length: 255)] // Définition d'une colonne pour le nom du produit
    private ?string $name = null;

    #[ORM\Column] // Définition d'une colonne pour le prix du produit
    private ?float $price = null;

    #[ORM\Column] // Définition d'une colonne pour le stock du produit
    private ?int $stock = null;

    #[ORM\Column(length: 255, nullable: true)] // Définition d'une colonne pour l'image du produit (nullable)
    private ?string $image = null;

    #[ORM\Column(type: "text")] // Définition d'une colonne pour la description du produit
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')] // Relation ManyToOne avec la catégorie du produit
    #[ORM\JoinColumn(nullable: false)] // La catégorie du produit ne peut pas être nulle
    private ?Category $category = null;

    // Propriété temporaire pour l'image téléchargée (utilisée dans le formulaire pour la gestion des fichiers)
    private ?File $uploadedImage = null;

    public function getUploadedImage(): ?File
    {
        return $this->uploadedImage;
    }

    public function setUploadedImage(?File $uploadedImage): static
    {
        $this->uploadedImage = $uploadedImage;
        return $this;
    }

    // Propriété temporaire pour la nouvelle catégorie (utilisée dans le formulaire pour la gestion de la catégorie)
    private ?string $newCategory = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // Getters et Setters pour les propriétés de l'entité Product

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    // Getters et Setters pour la nouvelle catégorie (propriété temporaire)
    public function getNewCategory(): ?string
    {
        return $this->newCategory;
    }

    public function setNewCategory(?string $newCategory): static
    {
        $this->newCategory = $newCategory;
        return $this;
    }
}
