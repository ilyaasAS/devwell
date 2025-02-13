<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity] // Définition de l'entité OrderItem (élément d'une commande)
class OrderItem
{
    #[ORM\Id] // Définition de l'ID comme clé primaire
    #[ORM\GeneratedValue] // L'ID est généré automatiquement
    #[ORM\Column(type: 'integer')] // La colonne pour l'ID est de type entier
    private $id;

    #[ORM\ManyToOne(targetEntity: Product::class)] // Relation ManyToOne avec l'entité Product (chaque article de commande fait référence à un produit)
    #[ORM\JoinColumn(nullable: false)] // La colonne 'product' ne peut pas être nulle
    private $product;

    #[ORM\Column(type: 'integer')] // Définition d'une colonne pour la quantité du produit dans la commande
    private $quantity;

    #[ORM\Column(type: 'float')] // Définition d'une colonne pour le prix de l'article
    private $price;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')] // Relation ManyToOne avec l'entité Order (chaque article appartient à une commande)
    #[ORM\JoinColumn(nullable: false)] // La colonne 'order' ne peut pas être nulle
    private $order;

    // Getter et Setter pour l'ID de l'article de commande
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour le produit de l'article de commande
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    // Getter et Setter pour la quantité de produit dans l'article de commande
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    // Getter et Setter pour le prix de l'article de commande
    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    // Getter et Setter pour la commande associée à l'article de commande
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
