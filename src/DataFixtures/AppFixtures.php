<?php

namespace App\DataFixtures;

use App\Document\Review;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly DocumentManager $documentManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $categories = $this->loadCategories($manager);
        $this->loadProducts($manager, $categories);
        $this->loadUsers($manager);

        $manager->flush();

        $this->loadReviews();
        $this->documentManager->flush();
    }

    /**
     * @return Category[]
     */
    private function loadCategories(ObjectManager $manager): array
    {
        $categoryNames = ['Santé', 'High-Tech', 'Bien-être'];
        $categories = [];

        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * @param Category[] $categories
     */
    private function loadProducts(ObjectManager $manager, array $categories): void
    {
        if (\count($categories) < 3) {
            return;
        }

        $productsByCategory = [
            'Santé' => [
                ['name' => 'Compléments vitamines D3', 'price' => 14.90, 'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=500&q=80', 'description' => 'Renforce les défenses immunitaires.'],
                ['name' => 'Huile essentielle lavande', 'price' => 9.50, 'image' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=500&q=80', 'description' => 'Apaisement et sommeil.'],
                ['name' => 'Tensiomètre brassard digital', 'price' => 34.99, 'image' => 'https://images.unsplash.com/photo-1628595351029-c2bf17511435?w=500&q=80', 'description' => 'Électronique avec écran LCD.'],
                ['name' => 'Coussin cervical mémoire', 'price' => 42.00, 'image' => 'https://images.unsplash.com/photo-1632927265433-09b709c92861?w=500&q=80', 'description' => 'Maintien ergonomique.'],
            ],
            'High-Tech' => [
                ['name' => 'Montre connectée santé', 'price' => 89.90, 'image' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=500&q=80', 'description' => 'Suivi cardiaque et sommeil.'],
                ['name' => 'Tensiomètre connecté Bluetooth', 'price' => 59.99, 'image' => 'https://images.unsplash.com/photo-1584017911766-d451b3d0e843?w=500&q=80', 'description' => 'Envoi des mesures sur smartphone.'],
                ['name' => 'Purificateur d\'air HEPA', 'price' => 129.00, 'image' => 'https://images.unsplash.com/photo-1612362921342-02d7037f0253?w=500&q=80', 'description' => 'Filtration des allergènes.'],
                ['name' => 'Lampe luminothérapie', 'price' => 49.90, 'image' => 'https://plus.unsplash.com/premium_photo-1664303840471-46b00307806c?w=500&q=80', 'description' => 'Lumière du jour contre la fatigue.'],
            ],
            'Bien-être' => [
                ['name' => 'Tapis de yoga / acupression', 'price' => 29.90, 'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=500&q=80', 'description' => 'Détente musculaire.'],
                ['name' => 'Diffuseur huiles essentielles', 'price' => 24.99, 'image' => 'https://images.unsplash.com/photo-1602928321679-560bb453f190?w=500&q=80', 'description' => 'Nébulisation à froid.'],
                ['name' => 'Couverture lestée 6kg', 'price' => 79.00, 'image' => 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=500&q=80', 'description' => 'Apaisement et sommeil profond.'],
                ['name' => 'Roller de massage', 'price' => 19.90, 'image' => 'https://images.unsplash.com/photo-1600880292089-90a7e086ee0c?w=500&q=80', 'description' => 'Auto-massage musculaire.'],
            ],
        ];

        foreach ($categories as $category) {
            $name = $category->getName();
            if (!isset($productsByCategory[$name])) {
                continue;
            }
            foreach ($productsByCategory[$name] as $data) {
                $product = new Product();
                $product->setName($data['name']);
                $product->setPrice($data['price']);
                $product->setStock(100);
                $product->setImage($data['image']);
                $product->setDescription($data['description']);
                $product->setCategory($category);
                $manager->persist($product);
            }
        }
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setFirstName('Admin');
        $admin->setLastName('Devwell');
        $admin->setEmail('admin@devwell.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, '123456789'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $testUsers = [
            ['firstName' => 'Jean', 'lastName' => 'Dupont', 'email' => 'user1@test.com'],
            ['firstName' => 'Marie', 'lastName' => 'Durant', 'email' => 'user2@test.com'],
            ['firstName' => 'Pierre', 'lastName' => 'Martin', 'email' => 'user3@test.com'],
        ];

        foreach ($testUsers as $data) {
            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, '123456789'));
            $user->setRoles([]);
            $manager->persist($user);
        }
    }

    private function loadReviews(): void
    {
        $reviews = [
            ['name' => 'Sophie L.', 'email' => 'sophie.leclerc@email.com', 'note' => 5, 'comment' => 'Service impeccable et produits de qualité. Je recommande vivement !'],
            ['name' => 'Thomas B.', 'email' => 'thomas.bernard@email.com', 'note' => 5, 'comment' => 'Livraison rapide, site clair. Mon tensiomètre connecté est parfait.'],
            ['name' => 'Claire M.', 'email' => 'claire.martin@email.com', 'note' => 4, 'comment' => 'Très satisfaite de ma lampe luminothérapie. Un peu chère mais efficace.'],
            ['name' => 'Marc D.', 'email' => 'marc.dubois@email.com', 'note' => 5, 'comment' => 'Meilleure expérience d\'achat bien-être en ligne. À refaire.'],
            ['name' => 'Julie R.', 'email' => 'julie.rousseau@email.com', 'note' => 4, 'comment' => 'Bons conseils et suivi client au top. Produits conformes.'],
        ];

        foreach ($reviews as $data) {
            $doc = new Review();
            $doc->setName($data['name']);
            $doc->setEmail($data['email']);
            $doc->setNote($data['note']);
            $doc->setComment($data['comment']);
            $this->documentManager->persist($doc);
        }
    }
}
