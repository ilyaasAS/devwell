<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; // Importation des annotations Doctrine ODM

// Déclaration de la classe AvisClient en tant que document MongoDB

// #[ODM\Document (collection: "avis")] 
#[ODM\Document]
class AvisClient
{
    // Déclaration de l'ID unique du document AvisClient
    #[ODM\Id]
    private $id;

    // Déclaration du champ 'nom' qui sera stocké dans MongoDB sous forme de chaîne de caractères
    #[ODM\Field(type: "string")]
    private string $nom;

    // Déclaration du champ 'email' pour l'email du client
    #[ODM\Field(type: "string")]
    private string $email;

    // Déclaration du champ 'note' qui est un entier représentant la note (entre 1 et 5)
    #[ODM\Field(type: "int")]
    private int $note; // Note entre 1 et 5

    // Déclaration du champ 'commentaire' qui contient le texte du commentaire du client
    #[ODM\Field(type: "string")]
    private string $commentaire;

    // Déclaration du champ 'createdAt' pour enregistrer la date de création de l'avis
    #[ODM\Field(type: "date")]
    private \DateTimeInterface $createdAt;

    // Constructeur qui initialise le champ createdAt avec la date et l'heure actuelles
    public function __construct()
    {
        $this->createdAt = new \DateTime(); // Date et heure de création de l'avis
    }

    // **Getters et Setters** pour accéder aux propriétés de la classe et les modifier

    // Récupérer l'ID du document
    public function getId(): ?string
    {
        return $this->id;
    }

    // Récupérer le nom du client
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Définir le nom du client
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this; // Permet un chaînage des méthodes (fluent interface)
    }

    // Récupérer l'email du client
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Définir l'email du client
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this; // Chaînage des méthodes
    }

    // Récupérer la note du client
    public function getNote(): ?int
    {
        return $this->note;
    }

    // Définir la note du client
    public function setNote(int $note): self
    {
        $this->note = $note;
        return $this; // Chaînage des méthodes
    }

    // Récupérer le commentaire du client
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    // Définir le commentaire du client
    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this; // Chaînage des méthodes
    }

    // Récupérer la date de création de l'avis
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
