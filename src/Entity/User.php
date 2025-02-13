<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)] // Définition de l'entité User avec son repository associé
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id] // Définition de l'ID comme clé primaire
    #[ORM\GeneratedValue] // L'ID est généré automatiquement
    #[ORM\Column] // La colonne pour l'ID
    private ?int $id = null;

    #[ORM\Column(length: 255)] // Définition de la colonne pour le prénom
    private ?string $firstName = null;

    #[ORM\Column(length: 255)] // Définition de la colonne pour le nom
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true)] // La colonne email doit être unique
    private ?string $email = null;

    #[ORM\Column(length: 255)] // Définition de la colonne pour le mot de passe
    private ?string $password = null;

    #[ORM\Column(type: 'json')] // Stocke les rôles sous forme de tableau JSON
    private array $roles = [];

    /**
     * @var Collection<int, Cart> // Relation OneToMany avec l'entité Cart, chaque utilisateur peut avoir plusieurs paniers
     */
    #[ORM\OneToMany(targetEntity: Cart::class, mappedBy: 'user', orphanRemoval: true)] // Relation avec Cart, un utilisateur a plusieurs paniers
    private Collection $carts;

    // Constructeur pour initialiser la collection de paniers
    public function __construct()
    {
        $this->carts = new ArrayCollection();
    }

    // Getter pour l'ID de l'utilisateur
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour le prénom
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    // Getter et Setter pour le nom
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    // Getter et Setter pour l'email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    // Getter et Setter pour le mot de passe
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    // Getter pour les rôles de l'utilisateur
    public function getRoles(): array
    {
        $roles = $this->roles;

        // Garantir que chaque utilisateur a au moins le rôle ROLE_USER
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    // Setter pour les rôles de l'utilisateur
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    // Cette méthode est utilisée pour renvoyer l'identifiant unique de l'utilisateur (ici l'email)
    public function getUserIdentifier(): string
    {
        return $this->email; // Symfony utilise l'email comme identifiant
    }

    // Cette méthode est utilisée pour effacer des données sensibles, comme un mot de passe en clair
    public function eraseCredentials(): void
    {
        // Effacer ici les données sensibles si nécessaire
        // Exemple : $this->plainPassword = null;
    }

    /**
     * Getter pour obtenir les paniers associés à l'utilisateur
     * @return Collection<int, Cart> 
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    // Ajouter un panier à l'utilisateur
    public function addCart(Cart $cart): static
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setUser($this); // Associer le panier à l'utilisateur
        }

        return $this;
    }

    // Retirer un panier de l'utilisateur
    public function removeCart(Cart $cart): static
    {
        if ($this->carts->removeElement($cart)) {
            // Définir l'utilisateur du panier à null (sauf si déjà modifié)
            if ($cart->getUser() === $this) {
                $cart->setUser(null);
            }
        }

        return $this;
    }
}
