<?php

// src/Entity/Contact.php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)] // Définition de l'entité Contact associée à la table dans la base de données
class Contact
{
    #[ORM\Id] // Identifiant unique pour chaque message de contact
    #[ORM\GeneratedValue] // La valeur de l'ID est générée automatiquement
    #[ORM\Column] // Définition de la colonne pour l'ID
    private ?int $id = null;

    #[ORM\Column(length: 255)] // Définition d'une colonne pour le nom du contact, longueur maximale de 255 caractères
    private ?string $name = null;

    #[ORM\Column(length: 255)] // Définition d'une colonne pour l'email du contact, longueur maximale de 255 caractères
    private ?string $email = null;

    // Changement du nom de la propriété Sujet à subject
    #[ORM\Column(length: 255)] // Définition d'une colonne pour le sujet du message
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)] // Définition d'une colonne pour le message (texte long)
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)] // Définition de la colonne pour la date de création du message
    private ?\DateTimeInterface $createdAt = null;

    // Ajout du champ "response" pour stocker la réponse de l'administrateur
    #[ORM\Column(type: Types::TEXT, nullable: true)]  // Le champ est nullable pour permettre l'absence de réponse au début
    private ?string $response = null;

    // Ajout du champ isResponded pour marquer si un message a été répondu
    #[ORM\Column(type: Types::BOOLEAN)] // Champ booléen pour savoir si le message a été répondu
    private bool $isResponded = false; // Par défaut, le message n'est pas encore répondu

    // Constructeur pour initialiser la date de création
    public function __construct()
    {
        // Initialisation de createdAt à la date et heure actuelles
        $this->createdAt = new \DateTime();
    }

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et setter pour le nom du contact
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // Getter et setter pour l'email du contact
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Getter et setter pour le sujet du message (anciennement "Sujet")
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    // Getter et setter pour le message
    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    // Getter pour la date de création du message
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // Setter pour la date de création du message
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Getter et setter pour la réponse de l'administrateur
    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): static
    {
        $this->response = $response;
        return $this;
    }

    // Getter et setter pour savoir si le message a été répondu
    public function getIsResponded(): bool
    {
        return $this->isResponded;
    }

    public function setIsResponded(bool $isResponded): static
    {
        $this->isResponded = $isResponded;
        return $this;
    }
}
