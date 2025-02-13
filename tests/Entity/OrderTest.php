<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\OrderItem;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class OrderTest extends TestCase
{
    public function testOrderEntity()
    {
        // Création d'un utilisateur simulé
        $user = $this->createMock(User::class);

        // Création d'une commande
        $order = new Order();

        // Test du setter et du getter pour le statut
        $order->setStatus('pending');
        $this->assertSame('pending', $order->getStatus(), 'Le statut de la commande n\'a pas été correctement assigné.');

        // Test du setter et du getter pour la date de création
        $date = new \DateTime('2025-01-01');
        $order->setCreatedAt($date);
        $this->assertEquals($date, $order->getCreatedAt(), 'La date de création de la commande n\'a pas été correctement assignée.');

        // Test du setter et du getter pour l'utilisateur associé
        $order->setUser($user);
        $this->assertSame($user, $order->getUser(), 'L\'utilisateur de la commande n\'a pas été correctement assigné.');

        // Test de l'ajout d'un article à la commande
        $orderItem = $this->createMock(OrderItem::class);
        $order->addOrderItem($orderItem);
        $this->assertCount(1, $order->getOrderItems(), 'L\'article n\'a pas été ajouté à la commande.');
        
        // Test que l'article est bien lié à la commande (relation bidirectionnelle)
        $orderItem->expects($this->once())
                  ->method('setOrder')
                  ->with($order);

        // Test du retrait d'un article de la commande
        $order->removeOrderItem($orderItem);
        $this->assertCount(0, $order->getOrderItems(), 'L\'article n\'a pas été retiré de la commande.');

        // Test de l'ajout de plusieurs articles
        $orderItem2 = $this->createMock(OrderItem::class);
        $order->addOrderItem($orderItem);
        $order->addOrderItem($orderItem2);
        $this->assertCount(2, $order->getOrderItems(), 'Le nombre d\'articles dans la commande est incorrect.');

        // Vérification que l'article est bien lié à la commande
        $orderItem2->expects($this->once())
                  ->method('setOrder')
                  ->with($order);

        // Test de la méthode removeOrderItem pour vérifier que l'ordre inverse est bien mis à jour
        $order->removeOrderItem($orderItem);
        $this->assertCount(1, $order->getOrderItems(), 'L\'article n\'a pas été correctement retiré de la commande.');

        // Test du comportement de setStatus avec une valeur vide (la validation échouera ici)
        $this->expectException(\Symfony\Component\Validator\Exception\ValidatorException::class);
        $order->setStatus('');
    }
}
