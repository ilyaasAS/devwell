<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    public function testContactEntity()
    {
        // Création d'une instance de l'entité Contact
        $contact = new Contact();

        // Vérification de la date de création initiale (devrait être la date actuelle)
        $this->assertInstanceOf(\DateTime::class, $contact->getCreatedAt(), 'La date de création n\'est pas une instance de \DateTime.');

        // Test du setter et du getter pour le nom
        $contact->setName('John Doe');
        $this->assertSame('John Doe', $contact->getName(), 'Le nom du contact n\'a pas été correctement assigné.');

        // Test du setter et du getter pour l'email
        $contact->setEmail('john.doe@example.com');
        $this->assertSame('john.doe@example.com', $contact->getEmail(), 'L\'email du contact n\'a pas été correctement assigné.');

        // Test du setter et du getter pour le sujet
        $contact->setSubject('Question about your product');
        $this->assertSame('Question about your product', $contact->getSubject(), 'Le sujet du contact n\'a pas été correctement assigné.');

        // Test du setter et du getter pour le message
        $contact->setMessage('I have a question regarding your product.');
        $this->assertSame('I have a question regarding your product.', $contact->getMessage(), 'Le message du contact n\'a pas été correctement assigné.');

        // Test du setter et du getter pour la réponse de l'administrateur
        $contact->setResponse('Thank you for your question. We will get back to you shortly.');
        $this->assertSame('Thank you for your question. We will get back to you shortly.', $contact->getResponse(), 'La réponse n\'a pas été correctement assignée.');

        // Test du setter et du getter pour savoir si le message a été répondu
        $contact->setIsResponded(true);
        $this->assertTrue($contact->getIsResponded(), 'La valeur du champ isResponded n\'a pas été correctement assignée.');

        // Test du setter et du getter pour savoir si le message a été répondu (cas false)
        $contact->setIsResponded(false);
        $this->assertFalse($contact->getIsResponded(), 'La valeur du champ isResponded n\'a pas été correctement assignée.');

        // Vérification du changement de date de création (en modifiant la date)
        $newDate = new \DateTime('2025-01-01 12:00:00');
        $contact->setCreatedAt($newDate);
        $this->assertEquals($newDate, $contact->getCreatedAt(), 'La date de création n\'a pas été correctement mise à jour.');
    }
}
