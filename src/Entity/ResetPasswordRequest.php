<?php

namespace App\Entity;

use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)] // Définition de l'entité ResetPasswordRequest avec son repository associé
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait; // Utilisation du trait qui gère la logique des demandes de réinitialisation de mot de passe

    #[ORM\Id] // Définition de l'ID comme clé primaire
    #[ORM\GeneratedValue] // L'ID est généré automatiquement
    #[ORM\Column] // La colonne pour l'ID
    private ?int $id = null;

    #[ORM\ManyToOne] // Relation ManyToOne avec l'entité User, indiquant qu'une demande de réinitialisation est liée à un utilisateur
    #[ORM\JoinColumn(nullable: false)] // La colonne pour l'utilisateur ne peut pas être nulle
    private ?User $user = null;

    // Le constructeur initialise la demande de réinitialisation avec un utilisateur, une date d'expiration, un sélecteur et un token haché
    public function __construct(User $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $user; // L'utilisateur associé à la demande
        $this->initialize($expiresAt, $selector, $hashedToken); // Initialisation avec les données liées à la demande (expiration, sélecteur et token)
    }

    // Getter pour l'ID de la demande de réinitialisation
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour l'utilisateur associé à la demande de réinitialisation
    public function getUser(): User
    {
        return $this->user;
    }
}
