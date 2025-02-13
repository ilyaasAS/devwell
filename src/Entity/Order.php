<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)] // Définition de l'entité Order avec son repository
#[ORM\Table(name: 'orders')]  // Spécification du nom de la table dans la base de données
class Order
{
    #[ORM\Id] // Définition de l'ID comme clé primaire
    #[ORM\GeneratedValue] // La valeur de l'ID est générée automatiquement
    #[ORM\Column(type: 'integer')] // Définition de la colonne pour l'ID (type entier)
    private $id;

    #[ORM\Column(type: 'string', length: 255)] // Définition d'une colonne pour le statut de la commande, longueur maximale de 255 caractères
    #[Assert\NotBlank] // Validation pour s'assurer que le champ 'status' ne soit pas vide
    private $status;

    #[ORM\Column(type: 'datetime')] // Définition d'une colonne pour la date de création de la commande
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')] // Relation ManyToOne avec l'entité User (chaque commande appartient à un utilisateur)
    #[ORM\JoinColumn(nullable: false)] // La colonne 'user' ne peut pas être nulle
    private $user;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])] // Relation OneToMany avec l'entité OrderItem (chaque commande peut avoir plusieurs articles)
    private $orderItems;

    // Getter and Setter pour l'ID de la commande
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour le statut de la commande
    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    // Getter et Setter pour la date de création de la commande
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    // Getter et Setter pour l'utilisateur associé à la commande
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    // Getter et Setter pour les articles de la commande
    public function getOrderItems(): \Doctrine\Common\Collections\Collection
    {
        return $this->orderItems;
    }

    // Méthode pour ajouter un article à la commande
    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setOrder($this); // Lien inverse entre l'article et la commande
        }

        return $this;
    }

    // Méthode pour retirer un article de la commande
    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // Mise à jour du côté propriétaire (null si non changé)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }
}
