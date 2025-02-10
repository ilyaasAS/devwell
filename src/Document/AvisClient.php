<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class AvisClient
{
    #[ODM\Id]
    private $id;

    #[ODM\Field(type: "string")]
    private string $nom;

    #[ODM\Field(type: "string")]
    private string $email;

    #[ODM\Field(type: "int")]
    private int $note; // Note entre 1 et 5

    #[ODM\Field(type: "string")]
    private string $commentaire;

    #[ODM\Field(type: "date")]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?string { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getNote(): ?int { return $this->note; }
    public function setNote(int $note): self { $this->note = $note; return $this; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(string $commentaire): self { $this->commentaire = $commentaire; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
}
