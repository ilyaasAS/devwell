<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)] // Définition de l'entité Category associée à la table de la base de données
class Category
{
    #[ORM\Id] // Clé primaire de la catégorie
    #[ORM\GeneratedValue] // Génération automatique de l'ID
    #[ORM\Column] // Définition de la colonne pour l'ID
    private ?int $id = null;

    #[ORM\Column(length: 255)] // Définition d'une colonne pour le nom de la catégorie, avec une longueur maximale de 255 caractères
    private ?string $name = null;

    #[ORM\Column(type: "text", nullable: true)] // Définition d'une colonne de type texte pour la description, cette colonne est optionnelle
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class, orphanRemoval: true)] // Relation un-à-plusieurs avec l'entité Product
    private Collection $products; // Collection d'objets Product associés à cette catégorie

    public function __construct()
    {
        // Initialisation de la collection de produits
        $this->products = new ArrayCollection();
    }

    // Getter pour obtenir l'ID de la catégorie
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour obtenir le nom de la catégorie
    public function getName(): ?string
    {
        return $this->name;
    }

    // Setter pour définir le nom de la catégorie
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // Getter pour obtenir la description de la catégorie
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Setter pour définir la description de la catégorie
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Getter pour obtenir la collection de produits associés à la catégorie
    public function getProducts(): Collection
    {
        return $this->products;
    }

    // Méthode pour ajouter un produit à la catégorie
    public function addProduct(Product $product): static
    {
        // Vérification que le produit n'est pas déjà dans la collection
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            // Définir la catégorie du produit comme étant la catégorie actuelle
            $product->setCategory($this);
        }
        return $this;
    }

    // Méthode pour supprimer un produit de la catégorie
    public function removeProduct(Product $product): static
    {
        // Vérifier si le produit existe dans la collection
        if ($this->products->removeElement($product)) {
            // Si la catégorie du produit est la catégorie actuelle, définir la catégorie à null
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }
        return $this;
    }
}
