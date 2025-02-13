<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Product; // Importation de la classe Product

// Définition de l'entité Cart associée à la table de la base de données
#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    // ID de la cart (clé primaire, générée automatiquement)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation avec l'entité User (un utilisateur possède plusieurs paniers)
    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)] // La colonne 'user' ne peut pas être nulle
    private ?User $user = null;

    // Relation avec l'entité Product (un panier contient un produit)
    #[ORM\ManyToOne(targetEntity: Product::class)] // Définir la relation avec Product
    #[ORM\JoinColumn(nullable: false)] // La colonne 'product' ne peut pas être nulle
    private ?Product $product = null; // Propriété product

    // Quantité de ce produit dans le panier
    #[ORM\Column]
    private ?int $quantity = null;

    // Getter pour obtenir l'ID du panier
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour obtenir l'utilisateur associé au panier
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Setter pour définir l'utilisateur associé au panier
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Getter pour obtenir la quantité de produit dans le panier
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    // Setter pour définir la quantité de produit dans le panier
    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    // Getter pour obtenir le produit dans le panier
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    // Setter pour définir le produit dans le panier
    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
